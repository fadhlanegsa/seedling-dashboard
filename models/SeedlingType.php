<?php
/**
 * SeedlingType Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class SeedlingType extends Model {
    protected $table = 'seedling_types';
    
    /**
     * Get all active seedling types
     * 
     * @return array
     */
    public function getAllActive() {
        return $this->all(['is_active' => 1], 'name ASC');
    }
    
    /**
     * Get seedling types by category
     * 
     * @param string $category
     * @return array
     */
    public function getByCategory($category) {
        return $this->all(['category' => $category, 'is_active' => 1], 'name ASC');
    }
    
    /**
     * Search seedling types
     * 
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_active = 1 
                AND (name LIKE ? OR scientific_name LIKE ?)
                ORDER BY name ASC
                LIMIT 20";
        
        $searchTerm = "%$keyword%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Get seedling types with stock availability
     * 
     * @return array
     */
    public function getWithStockAvailability() {
        $sql = "SELECT st.*, 
                COUNT(DISTINCT s.bpdas_id) as bpdas_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} st
                LEFT JOIN stock s ON st.id = s.seedling_type_id
                WHERE st.is_active = 1
                GROUP BY st.id
                ORDER BY st.name ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Get seedling type with stock details
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithStockDetails($id) {
        $sql = "SELECT st.*, 
                COUNT(DISTINCT s.bpdas_id) as bpdas_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} st
                LEFT JOIN stock s ON st.id = s.seedling_type_id
                WHERE st.id = ?
                GROUP BY st.id
                LIMIT 1";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Get seedling types with pagination
     *
     * @param int $page
     * @param int $perPage
     * @param string $category Filter by category (optional)
     * @param string $search Search by name/scientific name (optional). When set, all
     *                       matching rows are returned at once instead of being split
     *                       across pages.
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE, $category = null, $search = null) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT st.*,
                COUNT(DISTINCT s.bpdas_id) as bpdas_count,
                SUM(s.quantity) as total_stock
                FROM {$this->table} st
                LEFT JOIN stock s ON st.id = s.seedling_type_id";

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $where = [];
        $whereCount = [];
        $params = [];

        if ($category) {
            $where[] = "st.category = ?";
            $whereCount[] = "category = ?";
            $params[] = $category;
        }

        if ($search) {
            $where[] = "(st.name LIKE ? OR st.scientific_name LIKE ?)";
            $whereCount[] = "(name LIKE ? OR scientific_name LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
            $countSql .= " WHERE " . implode(' AND ', $whereCount);
        }

        $sql .= " GROUP BY st.id ORDER BY st.name ASC";

        $showAll = !empty($search);
        if (!$showAll) {
            $sql .= " LIMIT ? OFFSET ?";
        }

        $stmt = $this->db->prepare($sql);
        $bindIndex = 1;
        foreach ($params as $value) {
            $stmt->bindValue($bindIndex++, $value);
        }
        if (!$showAll) {
            $stmt->bindValue($bindIndex++, (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue($bindIndex++, (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll();

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        return [
            'data' => $data,
            'total' => $total,
            'page' => $showAll ? 1 : $page,
            'perPage' => $showAll ? max((int)$total, 1) : $perPage,
            'totalPages' => $showAll ? 1 : ceil($total / $perPage)
        ];
    }
    
    /**
     * Get categories with counts
     * 
     * @return array
     */
    public function getCategoriesWithCounts() {
        $sql = "SELECT category, COUNT(*) as count
                FROM {$this->table}
                WHERE is_active = 1
                GROUP BY category
                ORDER BY category ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Check if seedling type name exists
     * 
     * @param string $name
     * @param int $excludeId
     * @return bool
     */
    public function nameExists($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Get total active seedling types count
     * 
     * @return int
     */
    public function getActiveCount() {
        return $this->count(['is_active' => 1]);
    }
    
    /**
     * Get seedling types for autocomplete
     * 
     * @param string $term Search term
     * @return array
     */
    public function autocomplete($term) {
        $sql = "SELECT id, name, scientific_name, category
                FROM {$this->table}
                WHERE is_active = 1 
                AND (name LIKE ? OR scientific_name LIKE ?)
                ORDER BY name ASC
                LIMIT 10";
        
        $searchTerm = "%$term%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
}
