<?php
/**
 * BPDAS Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class BPDAS extends Model {
    protected $table = 'bpdas';
    
    /**
     * Get all BPDAS with province information
     * 
     * @return array
     */
    public function getAllWithProvince() {
        $sql = "SELECT b.*, p.name as province_name, p.code as province_code
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE b.is_active = 1
                ORDER BY b.name ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Get BPDAS by ID with province information
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithProvince($id) {
        $sql = "SELECT b.*, p.name as province_name, p.code as province_code
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE b.id = ?
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Get BPDAS by province
     * 
     * @param int $provinceId
     * @return array
     */
    public function getByProvince($provinceId) {
        $sql = "SELECT b.*, p.name as province_name
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE b.province_id = ? AND b.is_active = 1
                ORDER BY b.name ASC";
        
        return $this->query($sql, [$provinceId]);
    }
    
    /**
     * Search BPDAS
     * 
     * @param array $filters Search filters
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function search($filters = [], $page = 1, $perPage = SEARCH_RESULTS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, p.name as province_name,
                COUNT(DISTINCT s.seedling_type_id) as seedling_types_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                LEFT JOIN stock s ON b.id = s.bpdas_id
                WHERE b.is_active = 1";
        
        $countSql = "SELECT COUNT(DISTINCT b.id) as total
                     FROM {$this->table} b
                     INNER JOIN provinces p ON b.province_id = p.id
                     LEFT JOIN stock s ON b.id = s.bpdas_id
                     WHERE b.is_active = 1";
        
        $params = [];
        
        // Filter by province
        if (!empty($filters['province_id'])) {
            $sql .= " AND b.province_id = ?";
            $countSql .= " AND b.province_id = ?";
            $params[] = $filters['province_id'];
        }
        
        // Filter by seedling type
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND s.seedling_type_id = ?";
            $countSql .= " AND s.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        // Filter by minimum stock
        if (!empty($filters['min_stock'])) {
            $sql .= " AND s.quantity >= ?";
            $countSql .= " AND s.quantity >= ?";
            $params[] = $filters['min_stock'];
        }
        
        $sql .= " GROUP BY b.id, p.name
                  ORDER BY b.name ASC
                  LIMIT ? OFFSET ?";
        
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
     * Get BPDAS with stock details
     * 
     * @param int $id BPDAS ID
     * @return array|null
     */
    public function getWithStockDetails($id) {
        $sql = "SELECT b.*, p.name as province_name,
                COUNT(DISTINCT s.seedling_type_id) as seedling_types_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                LEFT JOIN stock s ON b.id = s.bpdas_id
                WHERE b.id = ?
                GROUP BY b.id, p.name
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Get BPDAS statistics
     * 
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                COUNT(DISTINCT b.id) as total_bpdas,
                COUNT(DISTINCT b.province_id) as provinces_with_bpdas,
                COUNT(DISTINCT s.seedling_type_id) as total_seedling_types,
                SUM(s.quantity) as total_national_stock
                FROM {$this->table} b
                LEFT JOIN stock s ON b.id = s.bpdas_id
                WHERE b.is_active = 1";
        
        return $this->queryOne($sql);
    }
    
    /**
     * Get stock by province for analytics
     * 
     * @return array
     */
    public function getStockByProvince() {
        $sql = "SELECT p.name as province_name,
                SUM(s.quantity) as total_stock
                FROM provinces p
                INNER JOIN {$this->table} b ON p.id = b.province_id
                LEFT JOIN stock s ON b.id = s.bpdas_id
                WHERE b.is_active = 1
                GROUP BY p.id, p.name
                HAVING total_stock > 0
                ORDER BY total_stock DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get BPDAS with pagination
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, p.name as province_name,
                COUNT(DISTINCT s.seedling_type_id) as seedling_types_count
                FROM {$this->table} b
                INNER JOIN provinces p ON b.province_id = p.id
                LEFT JOIN stock s ON b.id = s.bpdas_id
                GROUP BY b.id, p.name
                ORDER BY b.name ASC
                LIMIT ? OFFSET ?";
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
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
     * Get active BPDAS count
     * 
     * @return int
     */
    public function getActiveCount() {
        return $this->count(['is_active' => 1]);
    }
}
