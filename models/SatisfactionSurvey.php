<?php
/**
 * Satisfaction Survey Model
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class SatisfactionSurvey extends Model {
    protected $table = 'satisfaction_surveys';

    /**
     * Get survey by request id
     *
     * @param int $requestId
     * @return array|null
     */
    public function getByRequestId($requestId) {
        return $this->findBy(['request_id' => $requestId]);
    }

    /**
     * Create a new survey submission
     *
     * @param array $data
     * @return int|bool
     */
    public function createSurvey($data) {
        return $this->create([
            'request_id' => $data['request_id'],
            'user_id'    => $data['user_id'],
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null
        ]);
    }

    /**
     * Find the most recent request belonging to the user that has already
     * been submitted (i.e. exists in requests table) but has no survey yet.
     *
     * @param int $userId
     * @return array|null
     */
    public function getPendingSurveyRequestForUser($userId) {
        $sql = "SELECT r.id, r.request_number, r.created_at
                FROM requests r
                LEFT JOIN {$this->table} s ON s.request_id = r.id
                WHERE r.user_id = ? AND s.id IS NULL
                ORDER BY r.created_at DESC
                LIMIT 1";

        return $this->queryOne($sql, [$userId]);
    }

    /**
     * Overall average rating and total submitted surveys
     *
     * @param int|null $bpdasId Scope to a single BPDAS (optional)
     * @return array
     */
    public function getOverallStats($bpdasId = null) {
        $sql = "SELECT COUNT(*) as total, COALESCE(AVG(s.rating), 0) as average
                FROM {$this->table} s
                INNER JOIN requests r ON s.request_id = r.id
                WHERE 1=1";
        $params = [];

        if ($bpdasId) {
            $sql .= " AND r.bpdas_id = ?";
            $params[] = $bpdasId;
        }

        $result = $this->queryOne($sql, $params);

        return [
            'total'   => (int)($result['total'] ?? 0),
            'average' => round((float)($result['average'] ?? 0), 2)
        ];
    }

    /**
     * Rating distribution (count per star)
     *
     * @param int|null $bpdasId Scope to a single BPDAS (optional)
     * @return array
     */
    public function getRatingDistribution($bpdasId = null) {
        $sql = "SELECT s.rating, COUNT(*) as total
                FROM {$this->table} s
                INNER JOIN requests r ON s.request_id = r.id
                WHERE 1=1";
        $params = [];

        if ($bpdasId) {
            $sql .= " AND r.bpdas_id = ?";
            $params[] = $bpdasId;
        }

        $sql .= " GROUP BY s.rating ORDER BY s.rating DESC";

        $rows = $this->query($sql, $params);

        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($rows as $row) {
            $distribution[(int)$row['rating']] = (int)$row['total'];
        }

        return $distribution;
    }

    /**
     * Best testimonials (5-star, with a non-empty comment) for the public landing page
     *
     * @param int $limit
     * @return array
     */
    public function getTopTestimonials($limit = 10) {
        $sql = "SELECT s.rating, s.comment, s.created_at, u.full_name,
                r.id as request_id, r.request_number, r.updated_at as delivery_date,
                b.name as bpdas_name,
                st.name as single_seedling_name,
                (SELECT GROUP_CONCAT(DISTINCT st2.name SEPARATOR ', ')
                 FROM request_items ri
                 INNER JOIN seedling_types st2 ON ri.seedling_type_id = st2.id
                 WHERE ri.request_id = r.id) as multi_seedling_names
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN requests r ON s.request_id = r.id
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                LEFT JOIN seedling_types st ON r.seedling_type_id = st.id
                WHERE s.rating = 5 AND s.comment IS NOT NULL AND TRIM(s.comment) != ''
                ORDER BY s.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $testimonials = $stmt->fetchAll();

        foreach ($testimonials as &$t) {
            $t['seedling_name'] = $t['single_seedling_name'] ?: ($t['multi_seedling_names'] ?: 'Bibit Reguler');
        }
        unset($t);

        return $testimonials;
    }

    /**
     * Paginated survey list for admin recap page
     *
     * @param int $page
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function paginate($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, u.full_name as user_name, r.request_number, b.name as bpdas_name
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN requests r ON s.request_id = r.id
                INNER JOIN bpdas b ON r.bpdas_id = b.id
                WHERE 1=1";

        $countSql = "SELECT COUNT(*) as total
                FROM {$this->table} s
                INNER JOIN requests r ON s.request_id = r.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['rating'])) {
            $sql .= " AND s.rating = ?";
            $countSql .= " AND s.rating = ?";
            $params[] = $filters['rating'];
        }

        if (!empty($filters['bpdas_id'])) {
            $sql .= " AND r.bpdas_id = ?";
            $countSql .= " AND r.bpdas_id = ?";
            $params[] = $filters['bpdas_id'];
        }

        $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";

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
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
