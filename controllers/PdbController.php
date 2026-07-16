<?php
/**
 * PDB Controller — Modul Input Biaya Produksi Bibit (Pelaku Usaha)
 * Dashboard Stok Bibit Persemaian Indonesia
 *
 * Rute (via router default):
 *   pdb/langkah1            -> form Biaya Operasional (Langkah 1)
 *   pdb/store-langkah1      -> simpan master + hitung C12
 *   pdb/langkah2            -> form Detail Bibit (Langkah 2, Select2)
 *   pdb/store-langkah2      -> simpan detail + hitung harga final (ceil 50)
 *   pdb/delete-detail/{id}  -> hapus satu baris detail (milik sendiri)
 *
 * Semua rute hanya boleh diakses oleh role 'pelaku_usaha' dan hanya
 * menyentuh data milik akunnya sendiri (data isolation antar pelaku).
 */

require_once CORE_PATH . 'Controller.php';

class PdbController extends Controller {

    public function __construct() {
        parent::__construct();
        // Kunci seluruh modul untuk pelaku usaha saja
        $this->requireAuth('pelaku_usaha');
    }

    // =====================================================================
    //  LANGKAH 1 — BIAYA OPERASIONAL (MASTER)
    // =====================================================================

    /**
     * Tampilkan form Langkah 1. Jika sudah ada data untuk tahun terpilih,
     * form akan otomatis terisi (mode edit).
     */
    public function langkah1() {
        $user = currentUser();
        $year = $this->resolveYear();

        $masterModel = $this->model('PdbMaster');
        $master      = $masterModel->findByUserYear($user['id'], $year);

        // Prefill rincian item biaya (jika tersimpan)
        $rincian = ['a' => [], 'b' => []];
        if ($master && !empty($master['rincian_json'])) {
            $decoded = json_decode($master['rincian_json'], true);
            if (is_array($decoded)) {
                $rincian['a'] = $decoded['a'] ?? [];
                $rincian['b'] = $decoded['b'] ?? [];
            }
        }

        $this->render('pdb/langkah1', [
            'title'   => 'Input PDB — Langkah 1: Biaya Operasional',
            'master'  => $master,
            'year'    => $year,
            'years'   => $this->availableYears(),
            'rincian' => $rincian,
        ], 'dashboard');
    }

    /**
     * Simpan Langkah 1: jumlahkan rincian Biaya A & B, hitung C12, upsert master.
     */
    public function storeLangkah1() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('pdb/langkah1'); return; }
        if (!$this->validateCSRF()) { return; }

        $user = currentUser();
        $year = (int) $this->post('periode_tahun', date('Y'));

        // Jumlahkan rincian nominal di sisi server (jangan percaya total dari JS)
        list($totalA, $itemsA) = $this->sumRincian(
            $this->post('biaya_a_nama', []),
            $this->post('biaya_a_nominal', [])
        );
        list($totalB, $itemsB) = $this->sumRincian(
            $this->post('biaya_b_nama', []),
            $this->post('biaya_b_nominal', [])
        );

        $target = (int) round(parseDecimal($this->post('target_produksi_total', 0)));

        // Rumus C12 = (Total A + Total B) / Target Produksi  — guard division by zero
        $c12 = $target > 0 ? (($totalA + $totalB) / $target) : 0;

        $data = [
            'total_biaya_a'                 => $totalA,
            'total_biaya_b'                 => $totalB,
            'target_produksi_total'         => $target,
            'biaya_produksi_per_batang_c12' => round($c12, 2),
            'rincian_json'                  => json_encode(['a' => $itemsA, 'b' => $itemsB]),
        ];

        $masterModel = $this->model('PdbMaster');
        $masterId    = $masterModel->saveForUserYear($user['id'], $year, $data);

        if ($masterId) {
            $this->setFlash('success',
                'Biaya operasional tersimpan. Biaya produksi/batang (C12): <strong>Rp '
                . formatNumber(round($c12)) . '</strong>. Silakan lanjut mengisi detail bibit.');
            $this->redirect('pdb/langkah2?year=' . $year);
        } else {
            $this->setFlash('error', 'Gagal menyimpan biaya operasional. Silakan coba lagi.');
            $this->redirect('pdb/langkah1?year=' . $year);
        }
    }

    // =====================================================================
    //  LANGKAH 2 — DETAIL PER JENIS BIBIT (DETAIL)
    // =====================================================================

    /**
     * Tampilkan form Langkah 2 + daftar bibit yang sudah diinput.
     * Wajib sudah mengisi Langkah 1 lebih dulu.
     */
    public function langkah2() {
        $user = currentUser();
        $year = $this->resolveYear();

        $masterModel = $this->model('PdbMaster');
        $master      = $masterModel->findByUserYear($user['id'], $year);

        if (!$master) {
            $this->setFlash('warning',
                'Lengkapi dulu Langkah 1 (Biaya Operasional) untuk tahun ' . $year . '.');
            $this->redirect('pdb/langkah1?year=' . $year);
            return;
        }

        $seedlingModel = $this->model('SeedlingType');
        $detailModel   = $this->model('PdbDetail');

        $this->render('pdb/langkah2', [
            'title'         => 'Input PDB — Langkah 2: Detail Bibit',
            'master'        => $master,
            'year'          => $year,
            'seedlingTypes' => $seedlingModel->getAllActive(),
            'details'       => $detailModel->getByMaster($master['id']),
        ], 'dashboard');
    }

    /**
     * Simpan satu detail bibit: hitung kolom turunan + harga final (ceil 50),
     * lalu upsert. Form di-reset dengan redirect kembali ke Langkah 2.
     */
    public function storeLangkah2() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('pdb/langkah2'); return; }
        if (!$this->validateCSRF()) { return; }

        $user = currentUser();
        $year = (int) $this->post('year', date('Y'));

        $masterModel = $this->model('PdbMaster');
        $master      = $masterModel->findByUserYear($user['id'], $year);

        if (!$master) {
            $this->setFlash('error', 'Data master (Langkah 1) tidak ditemukan.');
            $this->redirect('pdb/langkah1?year=' . $year);
            return;
        }

        $seedlingTypeId = (int) $this->post('seedling_type_id');
        if ($seedlingTypeId <= 0) {
            $this->setFlash('error', 'Silakan pilih jenis bibit terlebih dahulu.');
            $this->redirect('pdb/langkah2?year=' . $year);
            return;
        }

        // Ambil input mentah (mendukung "0,70" atau "1.500" via parseDecimal)
        $hargaBenih      = parseDecimal($this->post('harga_benih', 0));
        $berat1000       = parseDecimal($this->post('berat_1000_butir', 0));
        $dayaKecambahPct = parseDecimal($this->post('daya_kecambah', 0));  // 0..100 (%)
        $bibitJadiPct    = parseDecimal($this->post('bibit_jadi', 0));     // 0..100 (%)

        $calc = $this->hitungHargaBibit(
            $hargaBenih, $berat1000, $dayaKecambahPct, $bibitJadiPct,
            (float) $master['biaya_produksi_per_batang_c12']
        );

        $detailModel = $this->model('PdbDetail');
        $ok = $detailModel->saveDetail($master['id'], $seedlingTypeId, $calc['row']);

        if ($ok) {
            $this->setFlash('success',
                'Bibit tersimpan. Harga final: <strong>Rp '
                . formatNumber($calc['harga_final']) . '/batang</strong>. Silakan input bibit berikutnya.');
        } else {
            $this->setFlash('error', 'Gagal menyimpan detail bibit.');
        }

        $this->redirect('pdb/langkah2?year=' . $year);
    }

    /**
     * Hapus satu baris detail. Dijaga: hanya boleh menghapus detail yang
     * masternya milik user yang sedang login.
     */
    public function deleteDetail($id = null) {
        $id   = (int) $id;
        $user = currentUser();

        $detailModel = $this->model('PdbDetail');
        $masterModel = $this->model('PdbMaster');

        $detail = $detailModel->find($id);
        if ($detail) {
            $master = $masterModel->find($detail['master_id']);
            if ($master && (int) $master['user_id'] === (int) $user['id']) {
                $detailModel->delete($id);
                $this->json(['success' => true, 'message' => 'Data bibit berhasil dihapus.']);
                return;
            }
        }

        $this->json(['success' => false, 'message' => 'Data tidak ditemukan atau bukan milik Anda.'], 404);
    }

    // =====================================================================
    //  HELPERS
    // =====================================================================

    /**
     * Rumus inti PDB (PRD Bagian 5). Seluruh pembagian dijaga dari nol.
     *
     * @return array{harga_final:int, row:array}
     */
    private function hitungHargaBibit($hargaBenih, $berat1000, $dayaKecambahPct, $bibitJadiPct, $c12) {
        // Normalisasi persen -> desimal, clamp ke rentang 0..1
        $dayaKecambah = max(0, min(100, $dayaKecambahPct)) / 100;
        $bibitJadi    = max(0, min(100, $bibitJadiPct)) / 100;

        // Kolom turunan (kuning)
        $jmlBenihPerKg       = $berat1000 > 0 ? (1000000 / $berat1000) : 0;
        $jmlBenihBerkecambah = $jmlBenihPerKg * $dayaKecambah;
        $jmlBibitJadi        = $jmlBenihBerkecambah * $bibitJadi;

        // Harga benih per bibit jadi (Kolom K) — guard division by zero
        $hargaBenihPerButir = $jmlBibitJadi > 0 ? ($hargaBenih / $jmlBibitJadi) : 0;

        // Harga mentah -> pembulatan KE ATAS kelipatan 50
        $hargaMentah = $hargaBenihPerButir + $c12;
        $hargaFinal  = (int) (ceil($hargaMentah / 50) * 50);

        return [
            'harga_final' => $hargaFinal,
            'row' => [
                'harga_benih'                  => round($hargaBenih, 2),
                'berat_1000_butir'             => round($berat1000, 2),
                'daya_kecambah'                => round($dayaKecambah, 4),
                'bibit_jadi'                   => round($bibitJadi, 4),
                'jml_benih_per_kg'             => (int) round($jmlBenihPerKg),
                'jml_benih_berkecambah'        => (int) round($jmlBenihBerkecambah),
                'jml_bibit_jadi'               => (int) round($jmlBibitJadi),
                'harga_benih_per_butir'        => round($hargaBenihPerButir, 2),
                'harga_bibit_per_batang_final' => $hargaFinal,
            ],
        ];
    }

    /**
     * Jumlahkan array rincian (nama[] + nominal[]) dan kembalikan
     * [total, daftar_item_bersih]. Baris kosong diabaikan.
     *
     * @return array{0:float,1:array}
     */
    private function sumRincian($namaArr, $nominalArr) {
        $total = 0.0;
        $items = [];

        if (!is_array($nominalArr)) {
            return [0.0, []];
        }

        foreach ($nominalArr as $i => $nominalRaw) {
            $nominal = parseDecimal($nominalRaw);
            $nama    = trim((string) ($namaArr[$i] ?? ''));

            // Lewati baris yang benar-benar kosong
            if ($nama === '' && $nominal == 0) {
                continue;
            }

            $total   += $nominal;
            $items[]  = ['nama' => sanitize($nama), 'nominal' => $nominal];
        }

        return [$total, $items];
    }

    /**
     * Tahun periode dari query string (?year=), divalidasi ke rentang wajar.
     */
    private function resolveYear() {
        $year = (int) $this->get('year', date('Y'));
        $min  = (int) date('Y') - 4;
        $max  = (int) date('Y') + 1;
        if ($year < $min || $year > $max) {
            $year = (int) date('Y');
        }
        return $year;
    }

    /**
     * Daftar tahun untuk dropdown (tahun depan s/d 4 tahun lalu).
     */
    private function availableYears() {
        $years = [];
        for ($y = (int) date('Y') + 1; $y >= (int) date('Y') - 4; $y--) {
            $years[] = $y;
        }
        return $years;
    }
}
