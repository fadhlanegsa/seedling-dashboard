<?php
/**
 * News Model
 * Handles Kabar Kehutanan news data operations
 */
require_once CORE_PATH . 'Model.php';

class News extends Model {

    protected $table = 'news';

    /**
     * Get all news, optionally filtered by source_type.
     * Joins with bpdas to include BPDAS name.
     */
    public function getAll($sourceType = null, $limit = null) {
        $sql = "SELECT n.*, b.name AS bpdas_name
                FROM news n
                LEFT JOIN bpdas b ON n.bpdas_id = b.id";
        $params = [];

        if ($sourceType) {
            $sql .= " WHERE n.source_type = ?";
            $params[] = $sourceType;
        }

        $sql .= " ORDER BY n.published_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return $this->query($sql, $params);
    }

    /**
     * Get news by source type (pusat or bpdas)
     */
    public function getBySourceType($sourceType) {
        return $this->getAll($sourceType);
    }

    /**
     * Get news posted by a specific BPDAS
     */
    public function getByBPDAS($bpdasId) {
        $sql = "SELECT n.*, b.name AS bpdas_name
                FROM news n
                LEFT JOIN bpdas b ON n.bpdas_id = b.id
                WHERE n.bpdas_id = ?
                ORDER BY n.published_at DESC";
        return $this->query($sql, [$bpdasId]);
    }

    /**
     * Get single news item with BPDAS name
     */
    public function getById($id) {
        $sql = "SELECT n.*, b.name AS bpdas_name
                FROM news n
                LEFT JOIN bpdas b ON n.bpdas_id = b.id
                WHERE n.id = ?
                LIMIT 1";
        $result = $this->query($sql, [(int)$id]);
        return $result ? $result[0] : null;
    }

    /**
     * Create a new news record
     */
    public function createNews($data) {
        return $this->create($data);
    }

    /**
     * Delete a news record and its image
     */
    public function deleteNews($id) {
        // Get image filename before deleting
        $news = $this->find($id);
        if ($news && !empty($news['image_filename'])) {
            $imagePath = UPLOAD_PATH . 'news/' . $news['image_filename'];
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }
        return $this->delete($id);
    }

    /**
     * Count news by source type
     */
    public function countBySource($sourceType) {
        $sql = "SELECT COUNT(*) as total FROM news WHERE source_type = ?";
        $result = $this->query($sql, [$sourceType]);
        return $result ? (int)$result[0]['total'] : 0;
    }
}
