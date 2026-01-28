<?php
/**
 * Request Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class Request extends Model {
    protected $table = 'requests';
    
    /**
     * Generate unique request number
     * 
     * @return string
     */
    public function generateRequestNumber() {
        $prefix = REQUEST_NUMBER_PREFIX;
        $date = date(REQUEST_NUMBER_FORMAT);
        
        // Get last request number for this month
        $sql = "SELECT request_number FROM {$this->table}
                WHERE request_number LIKE ?
                ORDER BY id DESC
                LIMIT 1";
        
        $pattern = "$prefix-$date-%";
        $lastRequest = $this->queryOne($sql, [$pattern]);
        
        if ($lastRequest) {
            // Extract sequence number and increment
            $parts = explode('-', $lastRequest['request_number']);
            $sequence = (int)end($parts) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf("%s-%s-%04d", $prefix, $date, $sequence);
    }
    
    /**
     * Create new request
     * 
     * @param array $data
     * @return int|bool
     */
    public function createRequest($data) {
        $data['request_number'] = $this->generateRequestNumber();
        $data['status'] = 'pending';
        
        return $this->create($data);
    }
    
    /**
     * Get request with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithDetails($id) {
        $sql = "SELECT r.*, 
                u.full_name as requester_name, u.email as requester_email, 
                u.phone as requester_phone, u.nik as requester_nik, u.address as requester_address,
                b.name as bpdas_name, b.address as bpdas_address, 
                b.phone as bpdas_phone, b.email as bpdas_email,
                p.name as province_name,
                st.name as seedling_name, st.scientific_name, st.category,
                approver.full_name as approver_name
                FROM {$this->table} r
                INNER JOIN users u ON r.user_id = u.id
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                INNER JOIN seedling_types st ON r.seedling_type_id = st.id
                LEFT JOIN users approver ON r.approved_by = approver.id
                WHERE r.id = ?
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Get request detail by ID (alias for getWithDetails)
     * 
     * @param int $id
     * @return array|null
     */
    public function getDetailById($id) {
        return $this->getWithDetails($id);
    }
    
    /**
     * Get requests by user
     * 
     * @param int $userId
     * @param string $status Filter by status (optional)
     * @return array
     */
    public function getByUser($userId, $status = null) {
        $sql = "SELECT r.*, 
                b.name as bpdas_name,
                st.name as seedling_name,
                p.name as province_name
                FROM {$this->table} r
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                INNER JOIN seedling_types st ON r.seedling_type_id = st.id
                WHERE r.user_id = ?";
        
        $params = [$userId];
        
        if ($status) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get requests by BPDAS
     * 
     * @param int $bpdasId
     * @param string $status Filter by status (optional)
     * @return array
     */
    public function getByBPDAS($bpdasId, $status = null) {
        $sql = "SELECT r.*, 
                u.full_name as requester_name, u.email as requester_email,
                u.phone as requester_phone, u.nik as requester_nik,
                st.name as seedling_name, st.category
                FROM {$this->table} r
                INNER JOIN users u ON r.user_id = u.id
                INNER JOIN seedling_types st ON r.seedling_type_id = st.id
                WHERE r.bpdas_id = ?";
        
        $params = [$bpdasId];
        
        if ($status) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get all requests with pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT r.*, 
                u.full_name as requester_name,
                b.name as bpdas_name,
                p.name as province_name,
                st.name as seedling_name
                FROM {$this->table} r
                INNER JOIN users u ON r.user_id = u.id
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                INNER JOIN seedling_types st ON r.seedling_type_id = st.id
                WHERE 1=1";
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} r WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = ?";
            $countSql .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND r.bpdas_id = ?";
            $countSql .= " AND r.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($filters['province_id'])) {
            $sql .= " AND b.province_id = ?";
            $countSql .= " AND EXISTS (SELECT 1 FROM bpdas b2 WHERE b2.id = r.bpdas_id AND b2.province_id = ?)";
            $params[] = $filters['province_id'];
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->bindValue(count($params) + 1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Approve request
     * 
     * @param int $requestId
     * @param int $approvedBy
     * @param string $notes
     * @return bool
     */
    public function approve($requestId, $approvedBy, $notes = null) {
        $data = [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approval_date' => date('Y-m-d H:i:s'),
            'approval_notes' => $notes
        ];
        
        return $this->update($requestId, $data);
    }
    
    /**
     * Reject request
     * 
     * @param int $requestId
     * @param int $rejectedBy
     * @param string $reason
     * @return bool
     */
    public function reject($requestId, $rejectedBy, $reason) {
        $data = [
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approval_date' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ];
        
        return $this->update($requestId, $data);
    }
    
    /**
     * Get pending requests count
     * 
     * @param int $bpdasId Filter by BPDAS (optional)
     * @return int
     */
    public function getPendingCount($bpdasId = null) {
        if ($bpdasId) {
            return $this->count(['status' => 'pending', 'bpdas_id' => $bpdasId]);
        }
        return $this->count(['status' => 'pending']);
    }
    
    /**
     * Get request statistics
     * 
     * @param int $bpdasId Filter by BPDAS (optional)
     * @return array
     */
    public function getStatistics($bpdasId = null) {
        $sql = "SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM {$this->table}";
        
        $params = [];
        
        if ($bpdasId) {
            $sql .= " WHERE bpdas_id = ?";
            $params[] = $bpdasId;
        }
        
        $result = $this->queryOne($sql, $params);
        
        // Ensure we return proper defaults
        return [
            'total_requests' => (int)($result['total_requests'] ?? 0),
            'pending' => (int)($result['pending'] ?? 0),
            'approved' => (int)($result['approved'] ?? 0),
            'rejected' => (int)($result['rejected'] ?? 0),
            'completed' => (int)($result['completed'] ?? 0)
        ];
    }
    
    /**
     * Add request to history
     * 
     * @param int $requestId
     * @param string $status
     * @param int $changedBy
     * @param string $notes
     * @return bool
     */
    public function addHistory($requestId, $status, $changedBy, $notes = null) {
        $sql = "INSERT INTO request_history (request_id, status, changed_by, notes)
                VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$requestId, $status, $changedBy, $notes]);
        } catch (PDOException $e) {
            logError("Add Request History Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get request history
     * 
     * @param int $requestId
     * @return array
     */
    public function getHistory($requestId) {
        $sql = "SELECT rh.*, u.full_name as user_name, rh.notes, rh.status, rh.created_at
                FROM request_history rh
                LEFT JOIN users u ON rh.changed_by = u.id
                WHERE rh.request_id = ?
                ORDER BY rh.created_at DESC";
        
        return $this->query($sql, [$requestId]);
    }
    
    /**
     * Get all requests with coordinates for map visualization
     * 
     * @param array $filters Optional filters (province_id, bpdas_id, seedling_type_id, status)
     * @return array
     */
    public function getMapData($filters = []) {
        $sql = "SELECT r.id, r.request_number, r.latitude, r.longitude, r.status, r.created_at,
                r.approval_date,
                u.full_name as requester_name,
                b.name as bpdas_name, b.id as bpdas_id,
                p.name as province_name, p.id as province_id,
                st.name as seedling_name, st.id as seedling_type_id,
                r.quantity
                FROM {$this->table} r
                INNER JOIN users u ON r.user_id = u.id
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                INNER JOIN seedling_types st ON r.seedling_type_id = st.id
                WHERE r.latitude IS NOT NULL 
                AND r.longitude IS NOT NULL";
        
        $params = [];
        
        if (!empty($filters['province_id'])) {
            $sql .= " AND p.id = ?";
            $params[] = $filters['province_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND b.id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND st.id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get statistics for geotagged requests
     * 
     * @return array
     */
    public function getGeotaggingStats() {
        $sql = "SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 ELSE 0 END) as geotagged,
                SUM(CASE WHEN latitude IS NULL OR longitude IS NULL THEN 1 ELSE 0 END) as not_geotagged
                FROM {$this->table}";
        
        return $this->queryOne($sql);
    }
    
    /**
     * Get total count of all requests
     * 
     * @return int
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Get total distributed seedlings (delivered + completed)
     * 
     * @return int
     */
    public function getTotalDistributed() {
        $sql = "SELECT SUM(quantity) as total 
                FROM {$this->table} 
                WHERE status IN ('delivered', 'completed')";
        $result = $this->queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
}
