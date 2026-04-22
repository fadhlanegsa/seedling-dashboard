<?php
/**
 * PubAuditTrail Model
 * Handles querying pub_audit_trails and pub_edit_requests for Admin Audit Dashboard
 */
require_once CORE_PATH . 'Model.php';

class PubAuditTrail extends Model {
    protected $table = 'pub_audit_trails';

    /**
     * Get paginated audit trails with editor info
     */
    public function getAuditLogs($filters = [], $limit = 25, $offset = 0) {
        $sql = "SELECT at.*, 
                u.full_name as editor_name, u.role as editor_role,
                n.name as nursery_name, b.name as bpdas_name
                FROM pub_audit_trails at
                LEFT JOIN users u ON at.edited_by = u.id
                LEFT JOIN nurseries n ON u.nursery_id = n.id
                LEFT JOIN bpdas b ON n.bpdas_id = b.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['transaction_type'])) {
            $sql .= " AND at.transaction_type = ?";
            $params[] = $filters['transaction_type'];
        }
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND n.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND n.id = ?";
            $params[] = $filters['nursery_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(at.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(at.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (u.full_name LIKE ? OR at.transaction_type LIKE ? OR at.record_id LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        $sql .= " ORDER BY at.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total audit logs (for pagination)
     */
    public function countAuditLogs($filters = []) {
        $sql = "SELECT COUNT(*) FROM pub_audit_trails at
                LEFT JOIN users u ON at.edited_by = u.id
                LEFT JOIN nurseries n ON u.nursery_id = n.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['transaction_type'])) {
            $sql .= " AND at.transaction_type = ?"; $params[] = $filters['transaction_type'];
        }
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND n.bpdas_id = ?"; $params[] = $filters['bpdas_id'];
        }
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND n.id = ?"; $params[] = $filters['nursery_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(at.created_at) >= ?"; $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(at.created_at) <= ?"; $params[] = $filters['date_to'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get pending edit requests (from pub_edit_requests table)
     */
    public function getPendingEditRequests($filters = []) {
        $sql = "SELECT er.*, 
                u.full_name as requester_name, u.role as requester_role,
                n.name as nursery_name, b.name as bpdas_name
                FROM pub_edit_requests er
                LEFT JOIN users u ON er.requested_by = u.id
                LEFT JOIN nurseries n ON er.nursery_id = n.id
                LEFT JOIN bpdas b ON n.bpdas_id = b.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND er.status = ?"; $params[] = $filters['status'];
        } else {
            $sql .= " AND er.status = 'pending'"; // default pending
        }
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND n.bpdas_id = ?"; $params[] = $filters['bpdas_id'];
        }

        $sql .= " ORDER BY er.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Statistics summary for the audit dashboard
     */
    public function getAuditStats() {
        $stats = [];
        // Total edits this month
        $stmt = $this->db->query("SELECT COUNT(*) FROM pub_audit_trails WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $stats['edits_this_month'] = (int)$stmt->fetchColumn();
        // Total edits all time
        $stmt = $this->db->query("SELECT COUNT(*) FROM pub_audit_trails");
        $stats['total_edits'] = (int)$stmt->fetchColumn();
        // Pending requests
        $stmt = $this->db->query("SELECT COUNT(*) FROM pub_edit_requests WHERE status = 'pending'");
        $stats['pending_requests'] = (int)$stmt->fetchColumn();
        // Most edited table
        $stmt = $this->db->query("SELECT transaction_type, COUNT(*) as cnt FROM pub_audit_trails GROUP BY transaction_type ORDER BY cnt DESC LIMIT 1");
        $row = $stmt->fetch();
        $stats['most_edited'] = $row ? $row['transaction_type'] : '-';
        return $stats;
    }

    /**
     * Approve or reject an edit request
     */
    public function updateEditRequestStatus($id, $status, $adminId, $adminNote = '') {
        $sql = "UPDATE pub_edit_requests SET status = ?, reviewed_by = ?, admin_note = ?, reviewed_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $adminId, $adminNote, $id]);
    }
}
