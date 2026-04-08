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
        $sql = "SELECT h.*, s.sowing_code, 
                m.name as seed_name, m.unit as seed_unit
                FROM {$this->table} h
                JOIN seed_sowings s ON h.sowing_id = s.id
                JOIN bahan_baku_master m ON s.seed_item_id = m.id";
        
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

        $sql .= " ORDER BY h.created_at DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
