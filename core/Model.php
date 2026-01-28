<?php
/**
 * Base Model Class
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once __DIR__ . '/../config/database.php';

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all records
     * 
     * @param array $conditions WHERE conditions
     * @param string $orderBy ORDER BY clause
     * @param int $limit LIMIT
     * @param int $offset OFFSET
     * @return array
     */
    public function all($conditions = [], $orderBy = null, $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logError("Model All Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logError("Model Find Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find record by conditions
     * 
     * @param array $conditions
     * @return array|null
     */
    public function findBy($conditions) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logError("Model FindBy Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Insert new record
     * 
     * @param array $data
     * @return int|bool Last insert ID or false
     */
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute(array_values($data))) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            logError("Model Create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $set = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . 
               " WHERE {$this->primaryKey} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("Model Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete record
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            logError("Model Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records
     * 
     * @param array $conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)$result['total'];
        } catch (PDOException $e) {
            logError("Model Count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Execute custom query
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logError("Model Query Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Execute custom query and return single row
     * 
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logError("Model QueryOne Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }
}
