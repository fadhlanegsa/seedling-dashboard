<?php
/**
 * Stock Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class Stock extends Model {
    protected $table = 'stock';
    
    /**
     * Get stock by BPDAS (Aggregated for multiple nurseries)
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getAggregatedByBPDAS($bpdasId) {
        $sql = "SELECT s.seedling_type_id, s.program_type, SUM(s.quantity) as quantity, 
                st.name as seedling_name, st.scientific_name, st.category
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                LEFT JOIN nurseries n ON s.nursery_id = n.id
                WHERE s.bpdas_id = ? OR n.bpdas_id = ?
                GROUP BY s.seedling_type_id, s.program_type, st.id, st.name, st.scientific_name, st.category
                ORDER BY st.name ASC, s.program_type ASC";
        
        return $this->query($sql, [$bpdasId, $bpdasId]);
    }
    
    /**
     * Get stock by Nursery
     * 
     * @param int $nurseryId
     * @return array
     */
    public function getByNursery($nurseryId) {
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category,
                n.name as nursery_name
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                LEFT JOIN nurseries n ON s.nursery_id = n.id
                WHERE s.nursery_id = ?
                ORDER BY st.name ASC";
        
        return $this->query($sql, [$nurseryId]);
    }
    
    /**
     * Legacy getter (kept for compatibility if needed, but beware of duplicate rows)
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getByBPDAS($bpdasId) {
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category,
                n.name as nursery_name
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                LEFT JOIN nurseries n ON s.nursery_id = n.id
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
    public function findByBPDASAndSeedling($bpdasId, $seedlingTypeId, $programType = 'Reguler') {
        return $this->findBy([
            'bpdas_id' => $bpdasId,
            'seedling_type_id' => $seedlingTypeId,
            'program_type' => $programType
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
    public function updateOrCreate($bpdasId, $seedlingTypeId, $quantity, $notes = null, $programType = 'Reguler') {
        $existing = $this->findByBPDASAndSeedling($bpdasId, $seedlingTypeId, $programType);
        
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
            $data['program_type'] = $programType;
            return $this->create($data);
        }
    }
    
    /**
     * Get top seedling types by stock
     * 
     * @param int $limit
     * @return array
     */
    public function getTopSeedlingTypes($limit = 10, $programType = null) {
        $sql = "SELECT st.name as seedling_name, st.category,
                SUM(s.quantity) as total_stock
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id";
                
        $params = [];
        if ($programType) {
            $sql .= " WHERE s.program_type = ?";
            $params[] = $programType;
        }
        
        $sql .= " GROUP BY st.id, st.name, st.category
                ORDER BY total_stock DESC
                LIMIT ?";
        $params[] = (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
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
    public function getTotalNationalStock($programType = null) {
        $sql = "SELECT SUM(quantity) as total FROM {$this->table}";
        if ($programType) {
            $sql .= " WHERE program_type = ?";
            $result = $this->queryOne($sql, [$programType]);
        } else {
            $result = $this->queryOne($sql);
        }
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Get stock statistics for BPDAS
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getBPDASStatistics($bpdasId, $programType = null) {
        $sql = "SELECT 
                COUNT(DISTINCT seedling_type_id) as total_types,
                COALESCE(SUM(quantity), 0) as total_stock,
                COALESCE(SUM(quantity), 0) as total_quantity,
                MAX(last_update_date) as last_update
                FROM {$this->table}
                WHERE bpdas_id = ?";
                
        $params = [$bpdasId];
        
        if ($programType) {
            $sql .= " AND program_type = ?";
            $params[] = $programType;
        }
        
        $result = $this->queryOne($sql, $params);
        
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
    public function decreaseStock($bpdasId, $seedlingTypeId, $quantity, $programType = 'Reguler') {
        $stock = $this->findByBPDASAndSeedling($bpdasId, $seedlingTypeId, $programType);
        
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
        
        if (!empty($filters['program_type'])) {
            $sql .= " AND s.program_type = ?";
            $params[] = $filters['program_type'];
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
            b.name as bpdas_name, p.name as province_name, n.name as nursery_name
            FROM {$this->table} s
            INNER JOIN seedling_types st ON s.seedling_type_id = st.id
            INNER JOIN bpdas b ON s.bpdas_id = b.id
            INNER JOIN provinces p ON b.province_id = p.id
            LEFT JOIN nurseries n ON s.nursery_id = n.id
            WHERE 1=1";
    
    $countSql = "SELECT COUNT(*) as total
                 FROM {$this->table} s
                 INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                 INNER JOIN bpdas b ON s.bpdas_id = b.id
                 INNER JOIN provinces p ON b.province_id = p.id
                 LEFT JOIN nurseries n ON s.nursery_id = n.id
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
        
        if (!empty($filters['nursery_id'])) {
            $sql .= " AND s.nursery_id = ?";
            $countSql .= " AND s.nursery_id = ?";
            $params[] = $filters['nursery_id'];
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

        if (!empty($filters['month'])) {
            $sql .= " AND MONTH(s.last_update_date) = ?";
            $countSql .= " AND MONTH(s.last_update_date) = ?";
            $params[] = $filters['month'];
        }

        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(s.last_update_date) = ?";
            $countSql .= " AND YEAR(s.last_update_date) = ?";
            $params[] = $filters['year'];
        }
        
        if (!empty($filters['program_type'])) {
            $sql .= " AND s.program_type = ?";
            $countSql .= " AND s.program_type = ?";
            $params[] = $filters['program_type'];
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

        // Calculate total quantity sum
        $sumSql = "SELECT SUM(s.quantity) as total_quantity
                  FROM {$this->table} s
                  INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                  INNER JOIN bpdas b ON s.bpdas_id = b.id
                  INNER JOIN provinces p ON b.province_id = p.id
                  LEFT JOIN nurseries n ON s.nursery_id = n.id
                  WHERE 1=1";
        
        // Apply the same filters as count query
        if (!empty($filters['province_id'])) $sumSql .= " AND b.province_id = ?";
        if (!empty($filters['bpdas_id'])) $sumSql .= " AND s.bpdas_id = ?";
        if (!empty($filters['nursery_id'])) $sumSql .= " AND s.nursery_id = ?";
        if (!empty($filters['seedling_type_id'])) $sumSql .= " AND s.seedling_type_id = ?";
        if (!empty($filters['category'])) $sumSql .= " AND st.category = ?";
        if (!empty($filters['month'])) $sumSql .= " AND MONTH(s.last_update_date) = ?";
        if (!empty($filters['year'])) $sumSql .= " AND YEAR(s.last_update_date) = ?";
        if (!empty($filters['program_type'])) $sumSql .= " AND s.program_type = ?";

        $sumStmt = $this->db->prepare($sumSql);
        $sumStmt->execute($params);
        $totalQuantity = $sumStmt->fetch()['total_quantity'] ?? 0;
        
        return [
            'data' => $data,
            'total' => $total,
            'total_quantity' => $totalQuantity,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
    /**
     * Get stock by Nursery
     * 
     * @param int $nurseryId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByNurseryPaginated($nurseryId, $page = 1, $perPage = ITEMS_PER_PAGE, $programType = null, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, st.name as seedling_name, st.scientific_name, st.category
                FROM {$this->table} s
                INNER JOIN seedling_types st ON s.seedling_type_id = st.id
                WHERE s.nursery_id = ?";
                
        $params = [$nurseryId];
        if ($programType) {
            $sql .= " AND s.program_type = ?";
            $params[] = $programType;
        }
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND s.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        if (!empty($filters['month'])) {
            $sql .= " AND MONTH(s.last_update_date) = ?";
            $params[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(s.last_update_date) = ?";
            $params[] = $filters['year'];
        }
        
        $sql .= " ORDER BY st.name ASC";
        if ($perPage !== 'all') {
            $sql .= " LIMIT ? OFFSET ?";
        }
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nursery_id = ?";
        $countParams = [$nurseryId];
        if ($programType) {
            $countSql .= " AND program_type = ?";
            $countParams[] = $programType;
        }
        if (!empty($filters['seedling_type_id'])) {
            $countSql .= " AND seedling_type_id = ?";
            $countParams[] = $filters['seedling_type_id'];
        }
        if (!empty($filters['month'])) {
            $countSql .= " AND MONTH(last_update_date) = ?";
            $countParams[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $countSql .= " AND YEAR(last_update_date) = ?";
            $countParams[] = $filters['year'];
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        if ($perPage !== 'all') {
            $stmt->bindValue(count($params) + 1, (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        // Execute count query
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
        // Calculate total quantity sum
        $sumSql = "SELECT SUM(quantity) as total_quantity FROM {$this->table} WHERE nursery_id = ?";
        if ($programType) {
            $sumSql .= " AND program_type = ?";
        }
        if (!empty($filters['seedling_type_id'])) {
            $sumSql .= " AND seedling_type_id = ?";
        }
        if (!empty($filters['month'])) {
            $sumSql .= " AND MONTH(last_update_date) = ?";
        }
        if (!empty($filters['year'])) {
            $sumSql .= " AND YEAR(last_update_date) = ?";
        }
        $sumStmt = $this->db->prepare($sumSql);
        $sumStmt->execute($countParams);
        $totalQuantity = $sumStmt->fetch()['total_quantity'] ?? 0;
        
        return [
            'data' => $data,
            'total' => $total,
            'total_quantity' => $totalQuantity,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ($perPage === 'all') ? 1 : ceil($total / $perPage)
        ];
    }

    /**
     * Check if stock exists for Nursery and seedling type
     * 
     * @param int $nurseryId
     * @param int $seedlingTypeId
     * @return array|null
     */
    public function findByNurseryAndSeedling($nurseryId, $seedlingTypeId, $programType = 'Reguler') {
        return $this->findBy([
            'nursery_id' => $nurseryId,
            'seedling_type_id' => $seedlingTypeId,
            'program_type' => $programType
        ]);
    }

    /**
     * Update or create stock for Nursery
     * 
     * @param int $nurseryId
     * @param int $seedlingTypeId
     * @param int $quantity
     * @param string $notes
     * @return bool
     */
    public function updateOrCreateNurseryStock($nurseryId, $seedlingTypeId, $quantity, $notes = null, $programType = 'Reguler') {
        $existing = $this->findByNurseryAndSeedling($nurseryId, $seedlingTypeId, $programType);
        
        // Get BPDAS ID from Nursery via direct query
        $nurseryStmt = $this->db->prepare("SELECT bpdas_id FROM nurseries WHERE id = ? LIMIT 1");
        $nurseryStmt->execute([$nurseryId]);
        $nursery = $nurseryStmt->fetch();
        $bpdasId = $nursery ? $nursery['bpdas_id'] : null;

        $data = [
            'quantity' => $quantity,
            'last_update_date' => date('Y-m-d'),
            'notes' => $notes,
            'bpdas_id' => $bpdasId // Keep maintaining bpdas_id for easier aggregation for now
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['nursery_id'] = $nurseryId;
            $data['seedling_type_id'] = $seedlingTypeId;
            $data['program_type'] = $programType;
            return $this->create($data);
        }
    }

    /**
     * Get stock summary for a specific nursery
     * 
     * @param int $nurseryId
     * @return array
     */
    public function getNurseryStockSummary($nurseryId) {
        $sql = "SELECT 
                COUNT(DISTINCT seedling_type_id) as total_types,
                COALESCE(SUM(quantity), 0) as total_quantity,
                MAX(last_update_date) as last_update
                FROM {$this->table}
                WHERE nursery_id = ?";
        
        return $this->queryOne($sql, [$nurseryId]);
    }

    /**
     * Decrease stock quantity from specific nursery
     * 
     * @param int $nurseryId
     * @param int $seedlingTypeId
     * @param int $quantity
     * @return bool
     */
    public function decreaseStockFromNursery($nurseryId, $seedlingTypeId, $quantity, $programType = 'Reguler') {
        $stock = $this->findByNurseryAndSeedling($nurseryId, $seedlingTypeId, $programType);
        
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
     * Upsert stock from API Push
     * 
     * @param int $bpdasId
     * @param int $nurseryId
     * @param int $seedlingTypeId
     * @param int $quantity
     * @param string|null $notes
     * @param string|null $lastSyncTimestamp
     * @return bool|int
     */
    public function upsertApiStock($bpdasId, $nurseryId, $seedlingTypeId, $quantity, $notes = null, $lastSyncTimestamp = null, $programType = 'Reguler') {
        // Default update date is current time
        $updateDate = $lastSyncTimestamp ? date('Y-m-d H:i:s', strtotime($lastSyncTimestamp)) : date('Y-m-d H:i:s');
        
        $data = [
            'quantity' => (int)$quantity,
            'last_update_date' => $updateDate,
            'notes' => $notes
        ];
        
        $existing = $this->findByNurseryAndSeedling($nurseryId, $seedlingTypeId, $programType);
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['nursery_id'] = $nurseryId;
            $data['bpdas_id'] = $bpdasId;
            $data['seedling_type_id'] = $seedlingTypeId;
            $data['program_type'] = $programType;
            return $this->create($data);
        }
    }
}
