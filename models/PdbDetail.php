<?php
/**
 * PdbDetail Model — Langkah 2 (Detail per Jenis Bibit)
 * Tabel: pdb_isian_detail
 * Dashboard Stok Bibit Persemaian Indonesia
 */

require_once CORE_PATH . 'Model.php';

class PdbDetail extends Model {
    protected $table = 'pdb_isian_detail';

    /**
     * Ambil semua detail bibit milik sebuah master, lengkap dengan nama bibit.
     *
     * @param int $masterId
     * @return array
     */
    public function getByMaster($masterId) {
        $sql = "SELECT d.*, st.name AS seedling_name, st.scientific_name, st.category
                FROM {$this->table} d
                INNER JOIN seedling_types st ON d.seedling_type_id = st.id
                WHERE d.master_id = ?
                ORDER BY st.name ASC";
        return $this->query($sql, [$masterId]);
    }

    /**
     * Cari satu baris detail berdasarkan (master, jenis bibit).
     *
     * @param int $masterId
     * @param int $seedlingTypeId
     * @return array|null
     */
    public function findByMasterAndType($masterId, $seedlingTypeId) {
        return $this->findBy(['master_id' => $masterId, 'seedling_type_id' => $seedlingTypeId]);
    }

    /**
     * Simpan (insert / update) detail untuk (master, jenis bibit).
     * UNIQUE (master_id, seedling_type_id) menjaga tidak ada duplikat —
     * input ulang jenis bibit yang sama akan meng-update baris lama.
     *
     * @param int   $masterId
     * @param int   $seedlingTypeId
     * @param array $data  Kolom input + hasil kalkulasi
     * @return int|bool
     */
    public function saveDetail($masterId, $seedlingTypeId, $data) {
        $existing = $this->findByMasterAndType($masterId, $seedlingTypeId);

        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        }

        $data['master_id']        = $masterId;
        $data['seedling_type_id'] = $seedlingTypeId;
        return $this->create($data);
    }

    /**
     * Rekap detail (flat) untuk admin & export Excel.
     * Filter opsional per pelaku usaha dan/atau per tahun.
     *
     * @param int|null $userId
     * @param int|null $year
     * @return array
     */
    public function getRecapDetails($userId = null, $year = null) {
        $sql = "SELECT d.*, st.name AS seedling_name, st.scientific_name,
                       u.full_name, u.username,
                       m.periode_tahun, m.biaya_produksi_per_batang_c12
                FROM {$this->table} d
                INNER JOIN pdb_isian_master m ON d.master_id = m.id
                INNER JOIN users u ON m.user_id = u.id
                INNER JOIN seedling_types st ON d.seedling_type_id = st.id
                WHERE 1=1";
        $params = [];

        if ($userId) { $sql .= " AND m.user_id = ?";       $params[] = $userId; }
        if ($year)   { $sql .= " AND m.periode_tahun = ?"; $params[] = $year; }

        $sql .= " ORDER BY u.full_name ASC, st.name ASC";
        return $this->query($sql, $params);
    }
}
