<?php
/**
 * API Key Model
 */
require_once CORE_PATH . 'Model.php';

class ApiKey extends Model {
    protected $table = 'api_keys';
    
    /**
     * Find API key data by key string
     * 
     * @param string $keyString
     * @return array|false
     */
    public function findByKey($keyString) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE key_string = ? LIMIT 1");
        $stmt->execute([$keyString]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
