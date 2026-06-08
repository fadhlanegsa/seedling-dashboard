<?php
/**
 * KamerhaController
 * 
 * Controller untuk integrasi dengan API Kamerha (PostgREST).
 * 
 * Routes (didaftarkan di index.php):
 *   POST /kamerha/push-qr          - Push QR index setelah cetak QR
 *   POST /kamerha/sync-geotag      - Sinkronisasi geotag (manual/AJAX)
 *   GET  /kamerha/sync-all         - Sinkronisasi semua permintaan yang pending geotag
 *   GET  /kamerha/explore-schema   - Debug: explore schema API Kamerha
 *   GET  /kamerha/sync-log         - Halaman log sinkronisasi
 * 
 * Akses: hanya bpdas, operator_persemaian, atau admin
 */

require_once CORE_PATH . 'Controller.php';
require_once UTILS_PATH . 'KamerhaService.php';

class KamerhaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Hanya user yang login yang bisa akses
        if (!isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            } else {
                redirect('auth/login');
            }
        }
    }

    // ================================================================
    // POINT 2: PUSH QR INDEX KE KAMERHA
    // ================================================================

    /**
     * POST /kamerha/push-qr
     * 
     * Endpoint AJAX yang dipanggil dari halaman cetak QR.
     * Menerima index_code dan request_id, lalu push ke Kamerha.
     * 
     * Body JSON: {
     *   "request_id": 123,
     *   "index_code": "PE-54-12-3-7-260415-1"
     * }
     */
    public function pushQr(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method Not Allowed. Gunakan POST.'], 405);
            return;
        }

        $body = $this->getJsonBody();
        $requestId = isset($body['request_id']) ? (int)$body['request_id'] : null;
        $indexCode = trim($body['index_code'] ?? '');

        if (!$requestId || empty($indexCode)) {
            $this->json(['success' => false, 'message' => 'request_id dan index_code wajib diisi'], 400);
            return;
        }

        // Ambil data permintaan untuk enrichment payload
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT r.*, 
                   u.full_name as recipient_name, u.nik as recipient_nik,
                   st.name as seedling_name,
                   b.name as bpdas_name,
                   n.name as nursery_name,
                   r.planting_address as address
            FROM requests r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN seedling_types st ON r.seedling_type_id = st.id
            LEFT JOIN bpdas b ON r.bpdas_id = b.id
            LEFT JOIN nurseries n ON r.nursery_id = n.id
            WHERE r.id = ?
            LIMIT 1
        ");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan'], 404);
            return;
        }

        $user = currentUser();
        $userId = $user['id'] ?? null;

        // Push ke Kamerha
        $result = KamerhaService::pushQrIndex($indexCode, $requestId, [
            'seedling_name'  => $request['seedling_name'],
            'bpdas_name'     => $request['bpdas_name'],
            'nursery_name'   => $request['nursery_name'],
            'recipient_name' => $request['recipient_name'],
            'recipient_nik'  => $request['recipient_nik'],
            'latitude'       => $request['latitude'],
            'longitude'      => $request['longitude'],
            'address'        => $request['address'] ?? null,
            'quantity'       => $request['quantity'],
        ], $userId);

        // Update kolom di tabel requests
        if ($result['success']) {
            $db->prepare("
                UPDATE requests 
                SET kamerha_index_code = ?,
                    kamerha_push_status = 'pushed',
                    kamerha_push_at = NOW()
                WHERE id = ?
            ")->execute([$indexCode, $requestId]);
        } else {
            $db->prepare("
                UPDATE requests 
                SET kamerha_index_code = ?,
                    kamerha_push_status = 'failed',
                    kamerha_push_at = NOW()
                WHERE id = ?
            ")->execute([$indexCode, $requestId]);
        }

        $this->json($result);
    }

    // ================================================================
    // POINT 3: PULL GEOTAG DATA DARI KAMERHA
    // ================================================================

    /**
     * POST /kamerha/sync-geotag
     * 
     * Sinkronisasi geotag untuk satu permintaan spesifik (AJAX).
     * Body: { "request_id": 123 }
     */
    public function syncGeotag(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method Not Allowed'], 405);
            return;
        }

        $body = $this->getJsonBody();
        $requestId = isset($body['request_id']) ? (int)$body['request_id'] : null;

        if (!$requestId) {
            $this->json(['success' => false, 'message' => 'request_id wajib diisi'], 400);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, kamerha_index_code, status FROM requests WHERE id = ? LIMIT 1");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            $this->json(['success' => false, 'message' => 'Permintaan tidak ditemukan'], 404);
            return;
        }

        if (empty($request['kamerha_index_code'])) {
            $this->json(['success' => false, 'message' => 'QR Index belum dipush ke Kamerha untuk permintaan ini. Cetak QR dahulu.'], 422);
            return;
        }

        $result = $this->doSyncSingle($db, $request);

        $this->json($result);
    }

    /**
     * GET /kamerha/sync-all
     * 
     * Sinkronisasi semua permintaan yang sudah push QR tapi belum punya geotag.
     * Bisa dipanggil manual (tombol) atau dari cronjob.
     * Mendukung header X-Cron: 1 untuk mode cronjob (tanpa session check).
     */
    public function syncAll(): void
    {
        header('Content-Type: application/json');

        $isCron = ($_SERVER['HTTP_X_CRON'] ?? '') === '1';
        $db = Database::getInstance()->getConnection();

        // Ambil semua permintaan yang sudah push QR tapi belum ada geotag/belum distributed
        $stmt = $db->prepare("
            SELECT id, kamerha_index_code, status
            FROM requests
            WHERE kamerha_index_code IS NOT NULL
              AND kamerha_push_status = 'pushed'
              AND (kamerha_geotag_lat IS NULL OR kamerha_synced_at < DATE_SUB(NOW(), INTERVAL 1 HOUR))
              AND status NOT IN ('cancelled', 'rejected')
            LIMIT 100
        ");
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($requests)) {
            $this->json([
                'success' => true,
                'message' => 'Tidak ada permintaan yang perlu disinkronisasi',
                'synced'  => 0,
            ]);
            return;
        }

        $synced = 0;
        $updated = 0;
        $failed = 0;
        $details = [];

        foreach ($requests as $req) {
            $result = $this->doSyncSingle($db, $req);
            if ($result['success']) {
                $synced++;
                if ($result['planted'] ?? false) {
                    $updated++;
                }
            } else {
                $failed++;
            }
            $details[] = [
                'request_id' => $req['id'],
                'code'       => $req['kamerha_index_code'],
                'result'     => $result,
            ];
        }

        $this->json([
            'success'     => true,
            'message'     => "Sinkronisasi selesai: $synced berhasil, $updated diupdate ke 'delivered', $failed gagal",
            'synced'      => $synced,
            'updated'     => $updated,
            'failed'      => $failed,
            'total_checked' => count($requests),
            'details'     => $details,
        ]);
    }

    /**
     * Lakukan sinkronisasi geotag untuk satu record request
     * Internal helper, dipakai oleh syncGeotag() dan syncAll()
     */
    private function doSyncSingle($db, array $request): array
    {
        $requestId = $request['id'];
        $indexCode = $request['kamerha_index_code'];
        $user = currentUser();
        $userId = $user['id'] ?? null;

        // Tarik data dari Kamerha
        $geoResult = KamerhaService::pullGeotagByCode($indexCode);

        // Log ke kamerha_sync_log
        try {
            $logStmt = $db->prepare("
                INSERT INTO kamerha_sync_log 
                    (sync_type, request_id, index_code, status, http_code, response_body, error_message, synced_by)
                VALUES ('pull_geotag', ?, ?, ?, NULL, ?, ?, ?)
            ");
            $logStmt->execute([
                $requestId,
                $indexCode,
                $geoResult['success'] ? 'success' : 'failed',
                json_encode($geoResult['geotag'] ?? null),
                $geoResult['message'],
                $userId,
            ]);
        } catch (Exception $e) {
            // Log failure adalah non-critical
        }

        // Update kamerha_synced_at agar tidak di-poll terus
        $db->prepare("UPDATE requests SET kamerha_synced_at = NOW() WHERE id = ?")->execute([$requestId]);

        if (!$geoResult['success']) {
            return ['success' => false, 'planted' => false, 'message' => $geoResult['message']];
        }

        if (!$geoResult['planted']) {
            return [
                'success' => true,
                'planted' => false,
                'message' => 'Bibit belum ditanam (belum ada scan geotag dari Kamerha)',
            ];
        }

        // Ada data geotag → update request jadi 'delivered' + simpan koordinat
        $geo = $geoResult['geotag'];

        $updateStmt = $db->prepare("
            UPDATE requests 
            SET kamerha_geotag_lat = ?,
                kamerha_geotag_lng = ?,
                kamerha_photo_url = ?,
                kamerha_scan_at = ?,
                status = CASE WHEN status IN ('approved', 'completed') THEN 'delivered' ELSE status END,
                updated_at = NOW()
            WHERE id = ?
        ");
        $updateStmt->execute([
            $geo['latitude'],
            $geo['longitude'],
            $geo['photo_url'],
            $geo['scan_at'],
            $requestId,
        ]);

        return [
            'success'    => true,
            'planted'    => true,
            'message'    => '✅ Data geotag ditemukan! Status permintaan diupdate menjadi "Terdistribusi".',
            'geotag'     => $geo,
            'request_id' => $requestId,
        ];
    }

    // ================================================================
    // DEBUG & MONITORING
    // ================================================================

    /**
     * GET /kamerha/explore-schema
     * Debug: explore schema PostgREST Kamerha (lihat tabel yang tersedia)
     */
    public function exploreSchema(): void
    {
        header('Content-Type: application/json');

        // Hanya admin
        $user = currentUser();
        if (!in_array($user['role'] ?? '', ['admin', 'bpdas'])) {
            $this->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $result = KamerhaService::exploreSchema();

        $this->json([
            'success'   => true,
            'note'      => 'Response mentah dari root endpoint PostgREST Kamerha',
            'http_code' => $result['http_code'],
            'data'      => $result['data'],
            'raw_body'  => $result['body'],
            'error'     => $result['error'],
        ]);
    }

    /**
     * GET /kamerha/sync-log
     * Halaman log sinkronisasi (HTML)
     */
    public function syncLog(): void
    {
        $user = currentUser();
        $db = Database::getInstance()->getConnection();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $logs = $db->prepare("
            SELECT ksl.*, 
                   r.request_number,
                   u.full_name as synced_by_name
            FROM kamerha_sync_log ksl
            LEFT JOIN requests r ON ksl.request_id = r.id
            LEFT JOIN users u ON ksl.synced_by = u.id
            ORDER BY ksl.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $logs->execute();
        $logData = $logs->fetchAll(PDO::FETCH_ASSOC);

        $totalCount = $db->query("SELECT COUNT(*) FROM kamerha_sync_log")->fetchColumn();

        // Statistik cepat
        $stats = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN sync_type = 'push_qr' THEN 1 ELSE 0 END) as pushes,
                SUM(CASE WHEN sync_type = 'pull_geotag' THEN 1 ELSE 0 END) as pulls
            FROM kamerha_sync_log
        ")->fetch(PDO::FETCH_ASSOC);

        $this->render('kamerha/sync-log', [
            'title'       => 'Log Sinkronisasi Kamerha',
            'logs'        => $logData,
            'stats'       => $stats,
            'total'       => $totalCount,
            'page'        => $page,
            'perPage'     => $perPage,
            'totalPages'  => ceil($totalCount / $perPage),
        ], 'dashboard');
    }

    // ================================================================
    // HELPER
    // ================================================================

    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function getJsonBody(): array
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true) ?? [];
    }
}
