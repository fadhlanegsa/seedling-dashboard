<?php
/**
 * PdbMaster Model — Langkah 1 (Biaya Operasional / Profil)
 * Tabel: pdb_isian_master
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class PdbMaster extends Model {
    protected $table = 'pdb_isian_master';

    /**
     * Ambil master milik seorang pelaku usaha untuk tahun tertentu.
     *
     * @param int $userId
     * @param int $year
     * @return array|null
     */
    public function findByUserYear($userId, $year) {
        return $this->findBy(['user_id' => $userId, 'periode_tahun' => $year]);
    }

    /**
     * Simpan (insert / update) master untuk kombinasi (user, tahun).
     * Karena ada UNIQUE (user_id, periode_tahun), satu pelaku hanya punya
     * satu master per tahun — submit berikutnya akan meng-update.
     *
     * @param int   $userId
     * @param int   $year
     * @param array $data  Kolom biaya + C12 (tanpa user_id/periode_tahun)
     * @return int|bool     ID master, atau false bila gagal
     */
    public function saveForUserYear($userId, $year, $data) {
        $existing = $this->findByUserYear($userId, $year);

        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        }

        $data['user_id']       = $userId;
        $data['periode_tahun'] = $year;
        return $this->create($data);
    }

    /**
     * Rekap untuk admin: daftar master + nama pelaku + jumlah jenis bibit.
     * Filter opsional per pelaku usaha dan/atau per tahun.
     *
     * @param int|null $userId
     * @param int|null $year
     * @return array
     */
    public function getRecap($userId = null, $year = null) {
        $sql = "SELECT m.*, u.full_name, u.username,
                       (SELECT COUNT(*) FROM pdb_isian_detail d WHERE d.master_id = m.id) AS jml_bibit
                FROM {$this->table} m
                INNER JOIN users u ON m.user_id = u.id
                WHERE 1=1";
        $params = [];

        if ($userId) { $sql .= " AND m.user_id = ?";        $params[] = $userId; }
        if ($year)   { $sql .= " AND m.periode_tahun = ?";  $params[] = $year; }

        $sql .= " ORDER BY m.periode_tahun DESC, u.full_name ASC";
        return $this->query($sql, $params);
    }

    /**
     * Daftar tahun periode yang sudah ada (untuk dropdown filter admin).
     *
     * @return array
     */
    public function getDistinctYears() {
        return $this->query(
            "SELECT DISTINCT periode_tahun FROM {$this->table} ORDER BY periode_tahun DESC"
        );
    }
}
