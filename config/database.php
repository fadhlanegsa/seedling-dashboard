<?php
/**
 * Database Connection Configuration
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            logError("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    /**
     * Get singleton instance of Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     * 
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Execute a query and return results
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|bool
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logError("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Execute a query and return single row
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return array|bool
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logError("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Execute an insert/update/delete query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return bool
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            logError("Execute Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Get last inserted ID
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Get row count from last query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return int
     */
    public function count($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            logError("Count Error: " . $e->getMessage() . " | SQL: " . $sql);
            return 0;
        }
    }
}
