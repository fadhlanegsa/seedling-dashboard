<?php
/**
 * User Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Find user by username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        return $this->findBy(['username' => $username]);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        return $this->findBy(['email' => $email]);
    }
    
    /**
     * Authenticate user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array|bool User data or false
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (username = ? OR email = ?) AND is_active = 1 
                LIMIT 1";
        
        $user = $this->queryOne($sql, [$username, $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Remove password from returned data
            unset($user['password']);
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Register new user
     * 
     * @param array $data User data
     * @return int|bool User ID or false
     */
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'public';
        }
        
        return $this->create($data);
    }
    
    /**
     * Get user with BPDAS information
     * 
     * @param int $id User ID
     * @return array|null
     */
    public function getUserWithBPDAS($id) {
        $sql = "SELECT u.*, b.name as bpdas_name, b.province_id, p.name as province_name
                FROM {$this->table} u
                LEFT JOIN bpdas b ON u.bpdas_id = b.id
                LEFT JOIN provinces p ON b.province_id = p.id
                WHERE u.id = ?
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Get all users with pagination
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param string $role Filter by role (optional)
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE, $role = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT u.*, b.name as bpdas_name 
                FROM {$this->table} u
                LEFT JOIN bpdas b ON u.bpdas_id = b.id";
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} u";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE u.role = ?";
            $countSql .= " WHERE u.role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        
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
     * Check if username exists
     * 
     * @param string $username
     * @param int $excludeId Exclude user ID (for updates)
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email
     * @param int $excludeId Exclude user ID (for updates)
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Change password
     * 
     * @param int $userId User ID
     * @param string $newPassword New password
     * @return bool
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Get users by role
     * 
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        return $this->all(['role' => $role], 'full_name ASC');
    }
    
    /**
     * Get BPDAS users
     * 
     * @return array
     */
    public function getBPDASUsers() {
        $sql = "SELECT u.*, b.name as bpdas_name, p.name as province_name
                FROM {$this->table} u
                INNER JOIN bpdas b ON u.bpdas_id = b.id
                INNER JOIN provinces p ON b.province_id = p.id
                WHERE u.role = 'bpdas' AND u.is_active = 1
                ORDER BY b.name ASC";
        
        return $this->query($sql);
    }
}
