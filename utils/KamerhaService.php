<?php
/**
 * KamerhaService
 * 
 * Service class untuk integrasi dengan API PostgREST Kamerha.
 * Menangani:
 *   1. pushQrIndex()   - POST indeks QR ke tabel Kamerha saat QR dicetak
 *   2. pullGeotagByCode() - GET data geotag dari Kamerha untuk update status 'Terdistribusi'
 */

require_once __DIR__ . '/BarcodeHelper.php';

class KamerhaService
{
    const BASE_URL     = 'https://postgrestdatatag.ditrh.synology.me';
    const BEARER_TOKEN = 'inipunyaaplikasikamerha22344hu890cvbccfgzzd90jx'; // acts as JWT Secret

    /**
     * Generate JWT HS256 token untuk authentication ke Kamerha
     */
    private static function generateJwtToken(): string
    {
        $payload = [
            'role' => 'authenticated',
            'exp'  => time() + 3600 // token valid selama 1 jam
        ];

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::BEARER_TOKEN, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Ambil headers standar untuk request ke Kamerha
     */
    private static function getHeaders(): array
    {
        $jwt = self::generateJwtToken();
        return [
            'Authorization: Bearer ' . $jwt,
            'apikey: ' . self::BEARER_TOKEN,
            'Content-Type: application/json',
            'Accept: application/json',
            'Prefer: return=representation',  // PostgREST: kembalikan record yang diinsert/diupdate
        ];
    }

    /**
     * Get Kamerha table name for a given BPDAS ID
     */
    public static function getBpdasTable(int $bpdasId): ?string
    {
        $mapping = [
            1  => 'krueng_aceh',
            2  => 'asahan_barumun',
            3  => 'batanghari',
            4  => 'citarum_ciliwung',
            6  => 'brantas_sampean',
            7  => 'kapuas',
            8  => 'mahakam_berau',
            9  => 'jeneberang_saddang',
            10 => 'memberamo',
            11 => 'wampu_sei_ular',
            12 => 'agam_kuantan',
            13 => 'sei_jang_duriangkang',
            14 => 'indragiri_rokan',
            15 => 'musi',
            16 => 'ketahun',
            17 => 'baturusa_cerucuk',
            19 => 'cimanuk_citanduy',
            20 => 'solo',
            21 => 'serayu_opak_progo',
            22 => 'barito',
            23 => 'kahayan',
            24 => 'unda_anyar',
            25 => 'dodokan_moyosari',
            26 => 'benain_noelmina',
            27 => 'konaweha',
            28 => 'karama',
            29 => 'palu_poso',
            30 => 'bone_limboto',
            31 => 'jeneberang_saddang',
            32 => 'tondano',
            34 => 'ake_malamo',
            35 => 'remu_ransiki'
        ];
        
        return $mapping[$bpdasId] ?? null;
    }

    /**
     * Parse BPDAS ID dari QR Index Code
     */
    public static function parseBpdasIdFromCode(string $indexCode): ?int
    {
        $parsed = BarcodeHelper::parse($indexCode);
        if ($parsed && isset($parsed['bpdas_id'])) {
            return $parsed['bpdas_id'];
        }
        
        // Fallback simple parsing
        $parts = explode('-', $indexCode);
        if (count($parts) >= 3) {
            return (int)$parts[2];
        }
        return null;
    }

    /**
     * Eksekusi HTTP request ke Kamerha API
     */
    public static function request(string $method, string $endpoint, array $payload = [], array $query = []): array
    {
        $url = self::BASE_URL . $endpoint;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false, // Bypass SSL verification
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER     => self::getHeaders(),
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        ]);

        if (in_array(strtoupper($method), ['POST', 'PATCH', 'PUT']) && !empty($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $responseBody = curl_exec($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'http_code' => 0,
                'body'      => '',
                'data'      => null,
                'error'     => 'cURL Error: ' . $curlError,
            ];
        }

        $decodedData = json_decode($responseBody, true);
        $error = null;

        if ($httpCode >= 400) {
            $errorMsg = $decodedData['message'] ?? $decodedData['hint'] ?? $responseBody;
            $error = "HTTP $httpCode: $errorMsg";
        }

        return [
            'http_code' => $httpCode,
            'body'      => $responseBody,
            'data'      => $decodedData,
            'error'     => $error,
        ];
    }

    /**
     * Push indeks QR bibit ke Kamerha saat QR Code dicetak
     */
    public static function pushQrIndex(string $indexCode, int $requestId, array $extraData = [], ?int $userId = null): array
    {
        $bpdasId = self::parseBpdasIdFromCode($indexCode);
        if (!$bpdasId) {
            return [
                'success' => false,
                'message' => 'Format QR Index Code tidak valid: tidak dapat mengekstrak BPDAS ID.',
                'http_code' => 400,
                'kamerha_id' => null,
            ];
        }

        $table = self::getBpdasTable($bpdasId);
        if (!$table) {
            return [
                'success' => false,
                'message' => "BPDAS ID $bpdasId tidak terintegrasi atau tidak memiliki tabel di sistem Kamerha.",
                'http_code' => 400,
                'kamerha_id' => null,
            ];
        }

        // Build payload matching Kamerha database schema
        $payload = [
            'barcode_id' => $indexCode,
            'bpdas'      => $table,
            'username'   => 'pedro', // default registered user
            'api'        => 'Y',     // source is API
            'timestamp'  => date('c'),
        ];

        // Kirim ke Kamerha via POST
        $result = self::request('POST', '/' . $table, $payload);

        // Log ke database lokal
        self::logToDb('push_qr', $requestId, $indexCode, $payload, $result, $userId);

        if ($result['error']) {
            return [
                'success'    => false,
                'message'    => 'Gagal push ke Kamerha: ' . $result['error'],
                'http_code'  => $result['http_code'],
                'kamerha_id' => null,
            ];
        }

        // Ambil ID yang dikembalikan Kamerha
        $kamerhaId = null;
        if (is_array($result['data']) && !empty($result['data'])) {
            $record = $result['data'][0] ?? $result['data'];
            $kamerhaId = $record['id'] ?? $record['barcode_id'] ?? null;
        }

        return [
            'success'    => true,
            'message'    => 'QR Index berhasil dipush ke Kamerha',
            'http_code'  => $result['http_code'],
            'kamerha_id' => $kamerhaId,
        ];
    }

    /**
     * Tarik data geotag dari Kamerha untuk satu QR index code
     */
    public static function pullGeotagByCode(string $indexCode): array
    {
        $bpdasId = self::parseBpdasIdFromCode($indexCode);
        if (!$bpdasId) {
            return [
                'success' => false,
                'planted' => false,
                'message' => 'Format QR Index Code tidak valid.',
                'geotag'  => null,
            ];
        }

        $table = self::getBpdasTable($bpdasId);
        if (!$table) {
            return [
                'success' => false,
                'planted' => false,
                'message' => "BPDAS ID $bpdasId tidak terintegrasi.",
                'geotag'  => null,
            ];
        }

        // Query the specific table for this barcode_id
        $result = self::request('GET', '/' . $table, [], [
            'barcode_id' => 'eq.' . $indexCode,
            'limit'      => 1,
        ]);

        if ($result['error']) {
            return [
                'success' => false,
                'planted' => false,
                'message' => $result['error'],
                'geotag'  => null,
            ];
        }

        $records = $result['data'] ?? [];

        if (empty($records)) {
            return [
                'success' => true,
                'planted' => false,
                'message' => 'Belum ada data tanam dari Kamerha untuk kode ini',
                'geotag'  => null,
            ];
        }

        $record = $records[0];

        // A record is planted if latitude and longitude are recorded
        $latitude  = $record['latitude'] ?? null;
        $longitude = $record['longitude'] ?? null;

        if ($latitude === null || $longitude === null) {
            return [
                'success' => true,
                'planted' => false,
                'message' => 'QR Code terdaftar tapi belum dipotret/ditanam di lapangan',
                'geotag'  => null,
            ];
        }

        return [
            'success'  => true,
            'planted'  => true,
            'message'  => 'Data geotag ditemukan',
            'geotag'   => [
                'kamerha_record_id' => $record['id'] ?? null,
                'index_code'        => $record['barcode_id'] ?? $indexCode,
                'latitude'          => $latitude,
                'longitude'         => $longitude,
                'photo_url'         => $record['filename'] ?? $record['url_foto'] ?? null,
                'scan_at'           => $record['timestamp'] ?? $record['updated_at'] ?? null,
                'address'           => $record['description'] ?? null,
                'raw'               => $record,
            ],
        ];
    }

    /**
     * Explore table schema
     */
    public static function exploreSchema(): array
    {
        // Root metadata endpoint is disabled in Synology PostgREST config, so return cached list
        return [
            'http_code' => 200,
            'body'      => 'Metadata disabled, showing verified list of tables.',
            'data'      => [
                'tables' => [
                    'barito', 'batanghari', 'remu_ransiki', 'memberamo', 'musi', 'kahayan',
                    'ketahun', 'palu_poso', 'kapuas', 'krueng_aceh', 'asahan_barumun',
                    'citarum_ciliwung', 'brantas_sampean', 'mahakam_berau', 'jeneberang_saddang',
                    'wampu_sei_ular', 'agam_kuantan', 'sei_jang_duriangkang', 'indragiri_rokan',
                    'baturusa_cerucuk', 'cimanuk_citanduy', 'solo', 'serayu_opak_progo',
                    'unda_anyar', 'dodokan_moyosari', 'benain_noelmina', 'konaweha', 'karama',
                    'bone_limboto', 'tondano', 'ake_malamo'
                ]
            ],
            'error'     => null,
        ];
    }

    /**
     * Catat aktivitas sinkronisasi ke tabel kamerha_sync_log
     */
    private static function logToDb(
        string $syncType,
        ?int   $requestId,
        ?string $indexCode,
        array  $requestPayload,
        array  $apiResult,
        ?int   $syncedBy = null
    ): void {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO kamerha_sync_log 
                    (sync_type, request_id, index_code, status, http_code, 
                     request_payload, response_body, error_message, synced_by)
                VALUES 
                    (:sync_type, :request_id, :index_code, :status, :http_code,
                     :request_payload, :response_body, :error_message, :synced_by)
            ");

            $status = (isset($apiResult['error']) && $apiResult['error']) ? 'failed' : 'success';

            $stmt->execute([
                ':sync_type'       => $syncType,
                ':request_id'      => $requestId,
                ':index_code'      => $indexCode,
                ':status'          => $status,
                ':http_code'       => $apiResult['http_code'] ?? 0,
                ':request_payload' => json_encode($requestPayload),
                ':response_body'   => isset($apiResult['body']) ? substr($apiResult['body'], 0, 4000) : null,
                ':error_message'   => $apiResult['error'] ?? null,
                ':synced_by'       => $syncedBy,
            ]);
        } catch (Exception $e) {
            error_log('[KamerhaService] Gagal log ke DB: ' . $e->getMessage());
        }
    }
}
