<?php
/**
 * SeedSource Model
 * Handles database operations for Direktori Sumber Benih Nasional
 */

require_once CORE_PATH . 'Model.php';

class SeedSource extends Model {
    
    /**
     * Get all seed sources with optional filters
     * 
     * @param array $filters Optional filters (province_id, seedling_type_id, seed_class)
     * @return array
     */
    public function getAll($filters = []) {
        $sql = "SELECT ss.*, 
                       p.name as province_name,
                       st.name as seedling_type_name
                FROM seed_sources ss
                LEFT JOIN provinces p ON ss.province_id = p.id
                LEFT JOIN seedling_types st ON ss.seedling_type_id = st.id
                WHERE ss.is_active = 1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['province_id'])) {
            $sql .= " AND ss.province_id = ?";
            $params[] = $filters['province_id'];
        }
        
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND ss.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        if (!empty($filters['seed_class'])) {
            $sql .= " AND ss.seed_class LIKE ?";
            $params[] = '%' . $filters['seed_class'] . '%';
        }
        
        if (!empty($filters['owner_name'])) {
            $sql .= " AND ss.owner_name LIKE ?";
            $params[] = '%' . $filters['owner_name'] . '%';
        }
        
        $sql .= " ORDER BY ss.seed_source_name ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get seed source by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT ss.*, 
                       p.name as province_name,
                       st.name as seedling_type_name,
                       st.scientific_name as seedling_scientific_name
                FROM seed_sources ss
                LEFT JOIN provinces p ON ss.province_id = p.id
                LEFT JOIN seedling_types st ON ss.seedling_type_id = st.id
                WHERE ss.id = ? AND ss.is_active = 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Create new seed source
     * 
     * @param array $data
     * @return int|false Last insert ID or false on failure
     */
    public function create($data) {
        $sql = "INSERT INTO seed_sources (
                    seed_source_name, local_name, botanical_name,
                    area_hectares, seed_class, location,
                    latitude, longitude,
                    owner_name, owner_phone, ownership_type,
                    certificate_number, certificate_date, certificate_validity,
                    tree_count, flowering_season, fruiting_season,
                    production_estimate_per_year, seed_quantity_estimate,
                    utilization, province_id, seedling_type_id
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";
        
        $params = [
            $data['seed_source_name'],
            $data['local_name'] ?? null,
            $data['botanical_name'] ?? null,
            $data['area_hectares'] ?? null,
            $data['seed_class'] ?? null,
            $data['location'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['owner_name'] ?? null,
            $data['owner_phone'] ?? null,
            $data['ownership_type'] ?? null,
            $data['certificate_number'] ?? null,
            $data['certificate_date'] ?? null,
            $data['certificate_validity'] ?? null,
            $data['tree_count'] ?? null,
            $data['flowering_season'] ?? null,
            $data['fruiting_season'] ?? null,
            $data['production_estimate_per_year'] ?? null,
            $data['seed_quantity_estimate'] ?? null,
            $data['utilization'] ?? null,
            $data['province_id'],
            $data['seedling_type_id'] ?? null
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            logError("SeedSource Create Error: " . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Update seed source
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE seed_sources SET
                    seed_source_name = ?,
                    local_name = ?,
                    botanical_name = ?,
                    area_hectares = ?,
                    seed_class = ?,
                    location = ?,
                    latitude = ?,
                    longitude = ?,
                    owner_name = ?,
                    owner_phone = ?,
                    ownership_type = ?,
                    certificate_number = ?,
                    certificate_date = ?,
                    certificate_validity = ?,
                    tree_count = ?,
                    flowering_season = ?,
                    fruiting_season = ?,
                    production_estimate_per_year = ?,
                    seed_quantity_estimate = ?,
                    utilization = ?,
                    province_id = ?,
                    seedling_type_id = ?
                WHERE id = ? AND is_active = 1";
        
        $params = [
            $data['seed_source_name'],
            $data['local_name'] ?? null,
            $data['botanical_name'] ?? null,
            $data['area_hectares'] ?? null,
            $data['seed_class'] ?? null,
            $data['location'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['owner_name'] ?? null,
            $data['owner_phone'] ?? null,
            $data['ownership_type'] ?? null,
            $data['certificate_number'] ?? null,
            $data['certificate_date'] ?? null,
            $data['certificate_validity'] ?? null,
            $data['tree_count'] ?? null,
            $data['flowering_season'] ?? null,
            $data['fruiting_season'] ?? null,
            $data['production_estimate_per_year'] ?? null,
            $data['seed_quantity_estimate'] ?? null,
            $data['utilization'] ?? null,
            $data['province_id'],
            $data['seedling_type_id'] ?? null,
            $id
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("SeedSource Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete seed source (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "UPDATE seed_sources SET is_active = 0 WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            logError("SeedSource Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all seed sources for map display (returns GeoJSON-friendly format)
     * 
     * @param array $filters Optional filters
     * @return array
     */
    public function getAllForMap($filters = []) {
        $sql = "SELECT ss.id, ss.seed_source_name, ss.local_name, ss.location,
                       ss.latitude, ss.longitude, ss.owner_name, ss.owner_phone,
                       ss.seed_class, ss.certificate_number,
                       p.name as province_name,
                       st.name as seedling_type_name
                FROM seed_sources ss
                LEFT JOIN provinces p ON ss.province_id = p.id
                LEFT JOIN seedling_types st ON ss.seedling_type_id = st.id
                WHERE ss.is_active = 1 
                  AND ss.latitude IS NOT NULL 
                  AND ss.longitude IS NOT NULL";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['province_id'])) {
            $sql .= " AND ss.province_id = ?";
            $params[] = $filters['province_id'];
        }
        
        if (!empty($filters['seedling_type_id'])) {
            $sql .= " AND ss.seedling_type_id = ?";
            $params[] = $filters['seedling_type_id'];
        }
        
        if (!empty($filters['seed_class'])) {
            $sql .= " AND ss.seed_class LIKE ?";
            $params[] = '%' . $filters['seed_class'] . '%';
        }
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get seed sources by province
     * 
     * @param int $provinceId
     * @return array
     */
    public function getByProvince($provinceId) {
        return $this->getAll(['province_id' => $provinceId]);
    }
    
    /**
     * Get seed sources by seedling type
     * 
     * @param int $typeId
     * @return array
     */
    public function getBySeedlingType($typeId) {
        return $this->getAll(['seedling_type_id' => $typeId]);
    }
    
    /**
     * Get statistics
     * 
     * @return array
     */
    public function getStats() {
        // Total count
        $totalSql = "SELECT COUNT(*) as total FROM seed_sources WHERE is_active = 1";
        $total = $this->queryOne($totalSql);
        
        // By province
        $provinceSql = "SELECT p.name, COUNT(ss.id) as count
                        FROM provinces p
                        LEFT JOIN seed_sources ss ON p.id = ss.province_id AND ss.is_active = 1
                        GROUP BY p.id, p.name
                        HAVING count > 0
                        ORDER BY count DESC";
        $byProvince = $this->query($provinceSql);
        
        // By seedling type
        $typeSql = "SELECT st.name, COUNT(ss.id) as count
                    FROM seedling_types st
                    LEFT JOIN seed_sources ss ON st.id = ss.seedling_type_id AND ss.is_active = 1
                    GROUP BY st.id, st.name
                    HAVING count > 0
                    ORDER BY count DESC
                    LIMIT 10";
        $byType = $this->query($typeSql);
        
        return [
            'total' => $total['total'] ?? 0,
            'by_province' => $byProvince,
            'by_type' => $byType
        ];
    }
    
    /**
     * Search seed sources with multi-criteria
     * 
     * @param array $criteria Search criteria
     * @return array
     */
    public function search($criteria) {
        return $this->getAll($criteria);
    }
}
