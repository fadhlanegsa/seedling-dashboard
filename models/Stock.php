<?php
/**
 * Stock Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class Stock extends Model {
    protected $table = 'stock';
    
    /**
     * Get stock by BPDAS with seedling type information
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getByBPDAS($bpdasId) {
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                WHERE s.bpdas_id = ?
                ORDER BY st.name ASC";
        
        return $this->query($sql, [$bpdasId]);
    }
    
    /**
     * Get stock by BPDAS with pagination
     * 
     * @param int $bpdasId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByBPDASPaginated($bpdasId, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                WHERE s.bpdas_id = ?
                ORDER BY st.name ASC
                LIMIT ? OFFSET ?";
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE bpdas_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $bpdasId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute([$bpdasId]);
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
     * Get stock by seedling type
     * 
     * @param int $seedlingTypeId
     * @return array
     */
    public function getBySeedlingType($seedlingTypeId) {
        $sql = "SELECT s.*, b.name as bpdas_name, p.name as province_name
                FROM {$this->table} s
                INNER JOIN bpdas b ON s.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE s.seedling_type_id = ? AND s.quantity > 0
                ORDER BY b.name ASC";
        
        return $this->query($sql, [$seedlingTypeId]);
    }
    
    /**
     * Check if stock exists for BPDAS and seedling type
     * 
     * @param int $bpdasId
     * @param int $seedlingTypeId
     * @return array|null
     */
    public function findByBPDASAndSeedling($bpdasId, $seedlingTypeId) {
        return $this->findBy([
            'bpdas_id' => $bpdasId,
            'seedling_type_id' => $seedlingTypeId
        ]);
    }
    
    /**
     * Update or create stock
     * 
     * @param int $bpdasId
     * @param int $seedlingTypeId
     * @param int $quantity
     * @param string $notes
     * @return bool
     */
    public function updateOrCreate($bpdasId, $seedlingTypeId, $quantity, $notes = null) {
        $existing = $this->findByBPDASAndSeedling($bpdasId, $seedlingTypeId);
        
        $data = [
            'quantity' => $quantity,
            'last_update_date' => date('Y-m-d'),
            'notes' => $notes
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['bpdas_id'] = $bpdasId;
            $data['seedling_type_id'] = $seedlingTypeId;
            return $this->create($data);
        }
    }
    
    /**
     * Get top seedling types by stock
     * 
     * @param int $limit
     * @return array
     */
    public function getTopSeedlingTypes($limit = 10) {
        $sql = "SELECT st.name as seedling_name, st.category,
                SUM(s.quantity) as total_stock
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                GROUP BY st.id, st.name, st.category
                ORDER BY total_stock DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get stock update trend (monthly)
     * 
     * @param int $months Number of months
     * @return array
     */
    public function getUpdateTrend($months = 12) {
        $sql = "SELECT 
                DATE_FORMAT(last_update_date, '%Y-%m') as month,
                DATE_FORMAT(last_update_date, '%b %Y') as month_name,
                COUNT(DISTINCT bpdas_id, seedling_type_id) as update_count,
                SUM(quantity) as total_quantity
                FROM {$this->table}
                WHERE last_update_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(last_update_date, '%Y-%m'), DATE_FORMAT(last_update_date, '%b %Y')
                ORDER BY month ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$months, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        // If no data, return empty array with structure
        if (empty($results)) {
            return [];
        }
        
        return $results;
    }
    
    /**
     * Get total national stock
     * 
     * @return int
     */
    public function getTotalNationalStock() {
        $sql = "SELECT SUM(quantity) as total FROM {$this->table}";
        $result = $this->queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Get stock statistics for BPDAS
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getBPDASStatistics($bpdasId) {
        $sql = "SELECT 
                COUNT(DISTINCT seedling_type_id) as total_types,
                COALESCE(SUM(quantity), 0) as total_stock,
                COALESCE(SUM(quantity), 0) as total_quantity,
                MAX(last_update_date) as last_update
                FROM {$this->table}
                WHERE bpdas_id = ?";
        
        $result = $this->queryOne($sql, [$bpdasId]);
        
        // Ensure we return proper defaults if no data
        return [
            'total_types' => (int)($result['total_types'] ?? 0),
            'total_stock' => (int)($result['total_stock'] ?? 0),
            'total_quantity' => (int)($result['total_quantity'] ?? 0),
            'last_update' => $result['last_update'] ?? null
        ];
    }
    
    /**
     * Decrease stock quantity
     * 
     * @param int $bpdasId
     * @param int $seedlingTypeId
     * @param int $quantity
     * @return bool
     */
    public function decreaseStock($bpdasId, $seedlingTypeId, $quantity) {
        $stock = $this->findByBPDASAndSeedling($bpdasId, $seedlingTypeId);
        
        if (!$stock || $stock['quantity'] < $quantity) {
            return false;
        }
        
        $newQuantity = $stock['quantity'] - $quantity;
        
        return $this->update($stock['id'], [
            'quantity' => $newQuantity,
            'last_update_date' => date('Y-m-d')
        ]);
    }
    
    /**
     * Search stock across all BPDAS
     * 
     * @param array $filters
     * @return array
     */
    public function searchStock($filters = []) {
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category,
                b.name as bpdas_name, p.name as province_name
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                INNER JOIN bpdas b ON s.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['province_id'])) {
            $sql .= " AND b.province_id = ?";
            $params[] = $filters['province_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND s.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND s.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        if (!empty($filters['category'])) {
            $sql .= " AND st.category = ?";
            $params[] = $filters['category'];
        }
        
        $sql .= " ORDER BY b.name ASC, st.name ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Search stock across all BPDAS with pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function searchStockPaginated($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category,
                b.name as bpdas_name, p.name as province_name
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                INNER JOIN bpdas b ON s.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE 1=1";
        
        $countSql = "SELECT COUNT(*) as total
                     FROM {$this->table} s
                     INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                     INNER JOIN bpdas b ON s.bpdas_id = b.id
                     INNER JOIN provinces p ON b.province_id = p.id
                     WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['province_id'])) {
            $sql .= " AND b.province_id = ?";
            $countSql .= " AND b.province_id = ?";
            $params[] = $filters['province_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND s.bpdas_id = ?";
            $countSql .= " AND s.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND s.seedling_type_id = ?";
            $countSql .= " AND s.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        if (!empty($filters['category'])) {
            $sql .= " AND st.category = ?";
            $countSql .= " AND st.category = ?";
            $params[] = $filters['category'];
        }
        
        $sql .= " ORDER BY b.name ASC, st.name ASC LIMIT ? OFFSET ?";
        
        // Execute main query
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->bindValue(count($params) + 1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        // Execute count query
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
}
