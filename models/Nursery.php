<?php
/**
 * Nursery Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class Nursery extends Model {
    protected $table = 'nurseries';
    
    /**
     * Get all nurseries by BPDAS ID
     * 
     * @param int $bpdasId
     * @return array
     */
    public function getByBPDAS($bpdasId) {
        $sql = "SELECT n.*, b.name as bpdas_name 
                FROM {$this->table} n
                INNER JOIN bpdas b ON n.bpdas_id = b.id
                WHERE n.bpdas_id = ? AND n.is_active = 1 
                ORDER BY n.name ASC";
        
        return $this->query($sql, [$bpdasId]);
    }

    /**
     * Get nursery with BPDAS info
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithBPDAS($id) {
        $sql = "SELECT n.*, b.name as bpdas_name, p.name as province_name
                FROM {$this->table} n
                INNER JOIN bpdas b ON n.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE n.id = ?";
        
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Create default nursery for BPDAS
     * 
     * @param int $bpdasId
     * @param string $bpdasName
     * @return int Nursery ID
     */
    public function createDefault($bpdasId, $bpdasName) {
        $nurseryName = "Persemaian " . str_replace('BPDAS ', '', $bpdasName) . " (Default)";
        
        // Check if exists
        $existing = $this->findBy(['bpdas_id' => $bpdasId, 'name' => $nurseryName]);
        if ($existing) {
            return $existing['id'];
        }

        return $this->create([
            'bpdas_id' => $bpdasId,
            'name' => $nurseryName,
            'is_active' => 1
        ]);
    }
}
