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
     * Get BPDAS for a province, respecting the delegated_bpdas_id mapping.
     *
     * Logic:
     *  1. Cek apakah provinsi ini punya delegated_bpdas_id (pelimpahan BPDAS).
     *  2. Jika ya → kembalikan hanya BPDAS yang di-delegate, beserta flag is_delegated=true
     *     dan nama provinsi asli untuk info ke warga.
     *  3. Jika tidak → fallback ke getByProvince() normal.
     *
     * @param int $provinceId
     * @return array [
     *   'bpdas_list'    => array,   // daftar BPDAS
     *   'is_delegated'  => bool,    // true jika menggunakan delegasi
     *   'province_name' => string,  // nama provinsi yang dipilih warga
     * ]
     */
    public function getByProvinceWithDelegation(int $provinceId): array {
        // Ambil data provinsi + potensi delegasi
        $provinceSql = "SELECT p.id, p.name, p.delegated_bpdas_id
                        FROM provinces p
                        WHERE p.id = ? LIMIT 1";
        $province = $this->queryOne($provinceSql, [$provinceId]);

        if (!$province) {
            return ['bpdas_list' => [], 'is_delegated' => false, 'province_name' => ''];
        }

        $provinceName = $province['name'];

        // Kasus 1: Ada delegasi → return BPDAS tujuan
        if (!empty($province['delegated_bpdas_id'])) {
            $delegatedId = (int)$province['delegated_bpdas_id'];
            $sql = "SELECT b.*, p.name as province_name
                    FROM {$this->table} b
                    INNER JOIN provinces p ON b.province_id = p.id
                    WHERE b.id = ? AND b.is_active = 1
                    LIMIT 1";
            $delegatedBpdas = $this->queryOne($sql, [$delegatedId]);
            $bpdasList = $delegatedBpdas ? [$delegatedBpdas] : [];
            return [
                'bpdas_list'   => $bpdasList,
                'is_delegated' => true,
                'province_name' => $provinceName,
            ];
        }

        // Kasus 2: Tidak ada delegasi → lookup normal berdasarkan province_id
        $bpdasList = $this->getByProvince($provinceId);
        return [
            'bpdas_list'   => $bpdasList,
            'is_delegated' => false,
            'province_name' => $provinceName,
        ];
    }


    
    /**
     * Search BPDAS
     * 
     * @param array $filters Search filters (province_id, seedling_type_id, min_stock, nursery_id)
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
                LEFT JOIN stock s ON b.id = s.bpdas_id AND s.program_type IN ('Reguler', 'bibitgratis')
                WHERE b.is_active = 1";
        
        $countSql = "SELECT COUNT(DISTINCT b.id) as total
                     FROM {$this->table} b
                     INNER JOIN provinces p ON b.province_id = p.id
                     LEFT JOIN stock s ON b.id = s.bpdas_id AND s.program_type IN ('Reguler', 'bibitgratis')
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
        
        // Filter by specific BPDAS
        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND b.id = ?";
            $countSql .= " AND b.id = ?";
            $params[] = $filters['bpdas_id'];
        }
        
        // Filter by specific Nursery
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND s.nursery_id = ?";
            $countSql .= " AND s.nursery_id = ?";
            $params[] = $filters['nursery_id'];
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
                LEFT JOIN stock s ON b.id = s.bpdas_id AND s.program_type IN ('Reguler', 'bibitgratis')
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
                COUNT(DISTINCT b.province_id) as total_provinces,
                COUNT(DISTINCT s.seedling_type_id) as total_seedling_types,
                COALESCE(SUM(s.quantity), 0) as total_stock,
                (SELECT COUNT(*) FROM nurseries WHERE is_active = 1) as total_nurseries,
                (SELECT COUNT(*) FROM requests) as total_requests,
                (SELECT SUM(COALESCE((SELECT NULLIF(SUM(quantity), 0) FROM request_items ri WHERE ri.request_id = requests.id), quantity, 0)) 
                 FROM requests WHERE status IN ('delivered', 'completed')) as total_distributed
                FROM {$this->table} b
                LEFT JOIN stock s ON b.id = s.bpdas_id AND s.program_type IN ('Reguler', 'bibitgratis')
                WHERE b.is_active = 1";
        
        return $this->queryOne($sql);
    }

    
    /**
     * Get stock by province for analytics
     * 
     * @return array
     */
    public function getStockByProvince($programType = null) {
        $sql = "SELECT p.name as province_name,
                SUM(s.quantity) as total_stock
                FROM provinces p
                INNER JOIN {$this->table} b ON p.id = b.province_id
                LEFT JOIN stock s ON b.id = s.bpdas_id";
        
        $params = [];
        if ($programType) {
            $sql .= " AND s.program_type = ?";
            $params[] = $programType;
        }
        
        $sql .= " WHERE b.is_active = 1
                GROUP BY p.id, p.name
                HAVING total_stock > 0
                ORDER BY total_stock DESC";
        
        return $this->query($sql, $params);
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
