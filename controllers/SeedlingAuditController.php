<?php
/**
 * Seedling Audit Controller
 * Admin-only: Audit Trail Dashboard & Edit Request Management
 */

require_once CORE_PATH . 'Controller.php';

class SeedlingAuditController extends Controller {

    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();

        if (!$this->requireAuth()) return;

        $user = currentUser();
        // Only admin and bpdas can view audit  
        if (!in_array($user['role'], ['admin', 'bpdas'])) {
            $this->redirect('auth/unauthorized');
        }
    }

    /**
     * Main Audit Dashboard
     */
    public function index() {
        $user = currentUser();
        $model = $this->model('PubAuditTrail');

        $filters = [
            'transaction_type' => $this->get('type'),
            'date_from'        => $this->get('date_from'),
            'date_to'          => $this->get('date_to'),
            'search'           => $this->get('search'),
        ];

        // Role-based filter
        if ($user['role'] === 'bpdas') {
            $filters['bpdas_id'] = $user['bpdas_id'];
        } elseif ($this->get('bpdas_id')) {
            $filters['bpdas_id'] = $this->get('bpdas_id');
        }
        if ($this->get('nursery_id')) {
            $filters['nursery_id'] = $this->get('nursery_id');
        }

        $perPage = 20;
        $page    = max(1, (int)$this->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $auditLogs    = $model->getAuditLogs($filters, $perPage, $offset);
        $totalLogs    = $model->countAuditLogs($filters);
        $totalPages   = ceil($totalLogs / $perPage);
        $stats        = $model->getAuditStats();

        // Pending requests (for bpdas/admin notification)
        $pendingRequests = $model->getPendingEditRequests(
            $user['role'] === 'bpdas' ? ['bpdas_id' => $user['bpdas_id']] : []
        );

        // Lists for filter dropdowns (admin only)
        $bpdasList   = $user['role'] === 'admin' ? $this->db->query("SELECT id, name FROM bpdas ORDER BY name")->fetchAll() : [];
        $nurseryList = $user['role'] === 'admin' ? $this->db->query("SELECT id, name FROM nurseries ORDER BY name")->fetchAll() : [];

        $processTypes = [
            'bahan_baku'        => 'Bahan Baku',
            'media_mixing'      => 'Media Mixing',
            'bag_filling'       => 'Pengisian Kantong',
            'seed_sowings'      => 'Penyemaian Benih',
            'seedling_harvests' => 'Panen Anakan',
            'seedling_weanings' => 'Sapih Bibit (PE)',
            'seedling_entres'   => 'Sapih Entres (ET)',
            'seedling_mutation' => 'Mutasi / Naik Kelas',
        ];

        $this->render('seedling_admin/audit_dashboard', compact(
            'auditLogs', 'totalLogs', 'totalPages', 'page',
            'stats', 'pendingRequests', 'filters',
            'bpdasList', 'nurseryList', 'processTypes', 'user'
        ), 'dashboard');
    }

    /**
     * View detail of a single audit log entry (JSON diff)
     */
    public function viewLog($id) {
        $stmt = $this->db->prepare("SELECT at.*, u.full_name as editor_name, u.role as editor_role,
            n.name as nursery_name, b.name as bpdas_name
            FROM pub_audit_trails at
            LEFT JOIN users u ON at.edited_by = u.id
            LEFT JOIN nurseries n ON u.nursery_id = n.id
            LEFT JOIN bpdas b ON n.bpdas_id = b.id
            WHERE at.id = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch();

        if (!$log) {
            $this->setFlash('error', 'Log tidak ditemukan.');
            $this->redirect('seedling-audit');
        }

        $log['audit_parsed'] = json_decode($log['audit_data'], true);

        $this->render('seedling_admin/audit_log_detail', ['log' => $log, 'title' => 'Detail Audit Log #' . $id], 'dashboard');
    }

    /**
     * Approve edit request
     */
    public function approveRequest($id) {
        $user = currentUser();
        $model = $this->model('PubAuditTrail');
        $adminNote = sanitize($this->post('admin_note') ?? '');
        $model->updateEditRequestStatus($id, 'approved', $user['id'], $adminNote);
        $this->setFlash('success', 'Permintaan Edit disetujui.');
        $this->redirect('seedling-audit');
    }

    /**
     * Reject edit request
     */
    public function rejectRequest($id) {
        $user = currentUser();
        $model = $this->model('PubAuditTrail');
        $adminNote = sanitize($this->post('admin_note') ?? 'Ditolak oleh Admin.');
        $model->updateEditRequestStatus($id, 'rejected', $user['id'], $adminNote);
        $this->setFlash('success', 'Permintaan Edit ditolak.');
        $this->redirect('seedling-audit');
    }
}
