<?php
/**
 * Province Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class Province extends Model {
    protected $table = 'provinces';
    
    /**
     * Get all provinces ordered by name
     * 
     * @return array
     */
    public function getAllOrdered() {
        return $this->all([], 'name ASC');
    }
    
    /**
     * Get provinces with BPDAS count
     * 
     * @return array
     */
    public function getWithBPDASCount() {
        $sql = "SELECT p.*, COUNT(b.id) as bpdas_count
                FROM {$this->table} p
                LEFT JOIN bpdas b ON p.id = b.province_id AND b.is_active = 1
                GROUP BY p.id
                ORDER BY p.name ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Get province with statistics
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithStatistics($id) {
        $sql = "SELECT p.*,
                COUNT(DISTINCT b.id) as bpdas_count,
                COUNT(DISTINCT s.seedling_type_id) as seedling_types_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} p
                LEFT JOIN bpdas b ON p.id = b.province_id AND b.is_active = 1
                LEFT JOIN stock s ON b.id = s.bpdas_id
                WHERE p.id = ?
                GROUP BY p.id
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Search provinces
     * 
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table}
                WHERE name LIKE ? OR code LIKE ?
                ORDER BY name ASC
                LIMIT 10";
        
        $searchTerm = "%$keyword%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Get provinces for autocomplete
     * 
     * @param string $term
     * @return array
     */
    public function autocomplete($term) {
        $sql = "SELECT id, name, code
                FROM {$this->table}
                WHERE name LIKE ? OR code LIKE ?
                ORDER BY name ASC
                LIMIT 10";
        
        $searchTerm = "%$term%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Get province by code
     * 
     * @param string $code
     * @return array|null
     */
    public function findByCode($code) {
        return $this->findBy(['code' => $code]);
    }
}
