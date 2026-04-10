<?php
class SeedlingHarvest extends Model {
    protected $table = 'seedling_harvests';

    /**
     * Generate next Harvest Code (PA-YYYYMMXXX)
     * @return string
     */
    public function generateHarvestCode() {
        $prefix = 'PA-' . date('Ym');
        $sql = "SELECT harvest_code FROM {$this->table} 
                WHERE harvest_code LIKE '{$prefix}%' 
                ORDER BY harvest_code DESC LIMIT 1";
        $result = $this->query($sql);
        
        if (empty($result)) {
            return $prefix . '001';
        }
        
        // Extract the last 3 digits
        $lastCode = $result[0]['harvest_code'];
        $lastNumber = intval(substr($lastCode, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $nextNumber;
    }

    /**
     * Get recent harvests for the Dashboard
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentHarvests($limit = 10, $filters = []) {
        $sql = "SELECT h.*, s.sowing_code, h.location,
                m.name as seed_name, 'pcs' as seed_unit,
                (h.harvested_quantity - COALESCE(w.used_stock, 0) - COALESCE(e.entres_stock, 0)) as remaining_stock
                FROM {$this->table} h
                JOIN seed_sowings s ON h.sowing_id = s.id
                JOIN bahan_baku_master m ON s.seed_item_id = m.id
                LEFT JOIN (
                    SELECT harvest_id, SUM(weaned_quantity) as used_stock
                    FROM seedling_weanings
                    GROUP BY harvest_id
                ) w ON h.id = w.harvest_id
                LEFT JOIN (
                    SELECT harvest_id, SUM(used_quantity) as entres_stock
                    FROM seedling_entres
                    GROUP BY harvest_id
                ) e ON h.id = e.harvest_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "h.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "h.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " HAVING remaining_stock > 0 ORDER BY h.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Get available harvests (sisa stok anakan) that have not been fully weaned
     * @param array $filters
     * @return array
     */
    public function getAvailableHarvests($filters = []) {
        $sql = "SELECT h.id, h.harvest_code, h.harvest_date, h.harvested_quantity as total_initial,
                h.location, m.name as seed_name, 'pcs' as seed_unit,
                COALESCE(w.used_stock, 0) as weaned_stock,
                COALESCE(e.entres_stock, 0) as used_entres,
                (h.harvested_quantity - COALESCE(w.used_stock, 0) - COALESCE(e.entres_stock, 0)) as remaining_stock
                FROM {$this->table} h
                JOIN seed_sowings s ON h.sowing_id = s.id
                JOIN bahan_baku_master m ON s.seed_item_id = m.id
                LEFT JOIN (
                    SELECT harvest_id, SUM(weaned_quantity) as used_stock
                    FROM seedling_weanings
                    GROUP BY harvest_id
                ) w ON h.id = w.harvest_id
                LEFT JOIN (
                    SELECT harvest_id, SUM(used_quantity) as entres_stock
                    FROM seedling_entres
                    GROUP BY harvest_id
                ) e ON h.id = e.harvest_id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['nursery_id'])) {
            $where[] = "h.nursery_id = ?";
            $params[] = $filters['nursery_id'];
        }
        
        if (!empty($filters['bpdas_id'])) {
            $where[] = "h.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " HAVING remaining_stock > 0 ORDER BY h.harvest_date ASC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get specific harvest details with current stock
     * @param int $id
     * @return array|null
     */
    public function getHarvestDetails($id) {
        $sql = "SELECT h.id, h.harvest_code, h.harvest_date, h.harvested_quantity,
                h.location, m.name as seed_name, m.scientific_name as seed_scientific_name, 'pcs' as seed_unit,
                (h.harvested_quantity - COALESCE(w.used_stock, 0) - COALESCE(e.entres_stock, 0)) as remaining_stock
                FROM {$this->table} h
                JOIN seed_sowings s ON h.sowing_id = s.id
                JOIN bahan_baku_master m ON s.seed_item_id = m.id
                LEFT JOIN (
                    SELECT harvest_id, SUM(weaned_quantity) as used_stock
                    FROM seedling_weanings
                    GROUP BY harvest_id
                ) w ON h.id = w.harvest_id
                LEFT JOIN (
                    SELECT harvest_id, SUM(used_quantity) as entres_stock
                    FROM seedling_entres
                    GROUP BY harvest_id
                ) e ON h.id = e.harvest_id
                WHERE h.id = ?";
                
        return $this->queryOne($sql, [$id]);
    }
}
