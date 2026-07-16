<?php
/**
 * PDB — Langkah 2: Form Detail per Jenis Bibit (Detail)
 * Pelaku Usaha memilih jenis bibit (Select2) lalu mengisi 4 kolom input.
 * Harga final dihitung otomatis (preview live + server-side, ceil kelipatan 50).
 * Nilai C12 ditarik otomatis dari master (tidak diinput ulang di form).
 */
$c12 = (float) ($master['biaya_produksi_per_batang_c12'] ?? 0);
?>

<!-- Select2 (CDN) — layout dashboard tidak memuatnya secara global -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="mb-0"><i class="fas fa-seedling text-success"></i> Input PDB — Detail Bibit</h1>
    <span class="badge badge-pill badge-primary p-2">Langkah 2 dari 2</span>
</div>

<!-- Step indicator -->
<div class="card mb-3">
    <div class="card-body py-2 d-flex align-items-center flex-wrap">
        <span class="badge badge-success badge-pill mr-2"><i class="fas fa-check"></i></span>
        <a href="<?= url('pdb/langkah1?year=' . $year) ?>" class="mr-3">Biaya Operasional</a>
        <i class="fas fa-arrow-right text-muted mr-3"></i>
        <span class="badge badge-primary badge-pill mr-2">2</span>
        <strong class="mr-3">Detail Bibit</strong>
        <span class="ml-auto text-muted small">Periode: <strong><?= $year ?></strong></span>
    </div>
</div>

<?php if ($flash = $this->getFlash()): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
        <?= $flash['message'] ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- FORM INPUT BIBIT -->
    <div class="col-lg-5">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-white border-bottom border-success">
                <h6 class="m-0 font-weight-bold text-success text-uppercase"><i class="fas fa-plus-circle"></i> Tambah / Ubah Bibit</h6>
            </div>
            <div class="card-body">
                <form action="<?= url('pdb/store-langkah2') ?>" method="POST" id="langkah2Form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="year" value="<?= $year ?>">

                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Bibit</label>
                        <select name="seedling_type_id" id="seedlingSelect" class="form-control" required>
                            <option value="">-- Cari &amp; pilih jenis bibit --</option>
                            <?php foreach ($seedlingTypes as $st): ?>
                                <option value="<?= $st['id'] ?>">
                                    <?= htmlspecialchars($st['name']) ?><?= !empty($st['scientific_name']) ? ' (' . htmlspecialchars($st['scientific_name']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Jika bibit sudah pernah diinput, data lama akan diperbarui.</small>
                    </div>

                    <!-- 4 kolom input mandiri (hijau) -->
                    <div class="form-group">
                        <label class="font-weight-bold text-success">Harga Benih (Rp)</label>
                        <input type="text" name="harga_benih" id="hargaBenih" class="form-control border-success"
                               inputmode="decimal" placeholder="mis. 150000" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-success">Berat 1000 Butir (gr)</label>
                        <input type="text" name="berat_1000_butir" id="berat1000" class="form-control border-success"
                               inputmode="decimal" placeholder="mis. 250" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="font-weight-bold text-success">Daya Kecambah (%)</label>
                            <input type="text" name="daya_kecambah" id="dayaKecambah" class="form-control border-success"
                                   inputmode="decimal" placeholder="0 - 100" required>
                        </div>
                        <div class="form-group col-6">
                            <label class="font-weight-bold text-success">Bibit Jadi (%)</label>
                            <input type="text" name="bibit_jadi" id="bibitJadi" class="form-control border-success"
                                   inputmode="decimal" placeholder="0 - 100" required>
                        </div>
                    </div>

                    <!-- Preview harga final (live) -->
                    <div class="p-3 rounded bg-light border mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Jumlah bibit jadi (estimasi)</span>
                            <span class="font-weight-bold" id="prevBibitJadi">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Harga benih / bibit jadi</span>
                            <span class="font-weight-bold" id="prevHargaButir">Rp 0</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Harga Final / batang</span>
                            <span class="h4 mb-0 font-weight-bold text-success" id="prevHargaFinal">Rp 0</span>
                        </div>
                        <div class="text-muted x-small mt-1">Sudah dibulatkan ke atas kelipatan 50.</div>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-save"></i> Simpan Bibit
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- DAFTAR BIBIT TERSIMPAN -->
    <div class="col-lg-7">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary text-uppercase"><i class="fas fa-list"></i> Bibit Tersimpan (<?= count($details) ?>)</h6>
                <span class="small text-muted">C12: <strong class="text-success">Rp <?= formatNumber(round($c12)) ?></strong>/batang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th>Jenis Bibit</th>
                                <th class="text-right">Harga Benih</th>
                                <th class="text-center">% Kecambah</th>
                                <th class="text-center">% Jadi</th>
                                <th class="text-right">Bibit Jadi</th>
                                <th class="text-right">Harga Final</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="detailBody">
                            <?php if (!empty($details)): ?>
                                <?php foreach ($details as $d): ?>
                                    <tr id="detailRow-<?= $d['id'] ?>">
                                        <td><strong><?= htmlspecialchars($d['seedling_name']) ?></strong></td>
                                        <td class="text-right">Rp <?= formatNumber($d['harga_benih']) ?></td>
                                        <td class="text-center"><?= rtrim(rtrim(number_format($d['daya_kecambah'] * 100, 2, ',', '.'), '0'), ',') ?>%</td>
                                        <td class="text-center"><?= rtrim(rtrim(number_format($d['bibit_jadi'] * 100, 2, ',', '.'), '0'), ',') ?>%</td>
                                        <td class="text-right"><?= formatNumber($d['jml_bibit_jadi']) ?></td>
                                        <td class="text-right font-weight-bold text-success">Rp <?= formatNumber($d['harga_bibit_per_batang_final']) ?></td>
                                        <td class="text-center">
                                            <i class="fas fa-trash text-danger" role="button" title="Hapus"
                                               onclick="deleteDetail(<?= $d['id'] ?>)"></i>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="emptyDetailRow">
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada bibit. Tambahkan di form sebelah kiri.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="<?= cspNonce() ?>">
// Catatan: jQuery dimuat di akhir <body> oleh layout dashboard, sehingga
// seluruh inisialisasi dibungkus DOMContentLoaded (jQuery pasti sudah ada),
// dan Select2 dimuat dinamis setelah jQuery tersedia.
document.addEventListener('DOMContentLoaded', function () {
    // C12 ditarik dari master (server) — dipakai untuk preview harga final
    var C12 = <?= json_encode($c12) ?>;

    // ── Select2 dengan pencarian (load dinamis agar jQuery sudah siap) ─────────
    function initSelect2() {
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#seedlingSelect').select2({
                placeholder: '-- Cari & pilih jenis bibit --',
                width: '100%',
                allowClear: true
            });
        }
    }
    if (window.jQuery) {
        if (jQuery.fn.select2) {
            initSelect2();
        } else {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            s.onload = initSelect2;
            document.head.appendChild(s);
        }
    }
    // Jika jQuery tidak tersedia, <select> tetap berfungsi sebagai dropdown biasa.

    // ── Parser angka (mirror parseDecimal server) ─────────────────────────────
    function parseNum(str) {
        str = String(str == null ? '' : str).trim();
        if (str === '') return 0;
        var lastComma = str.lastIndexOf(',');
        var lastDot   = str.lastIndexOf('.');
        if (lastComma !== -1 && lastDot !== -1) {
            if (lastComma > lastDot) { str = str.replace(/\./g, '').replace(',', '.'); }
            else { str = str.replace(/,/g, ''); }
        } else if (lastComma !== -1) {
            str = str.replace(',', '.');
        }
        var n = parseFloat(str.replace(/[^0-9.\-]/g, ''));
        return isNaN(n) ? 0 : n;
    }
    function rupiah(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

    // ── Preview harga final (rumus identik dengan backend) ────────────────────
    function preview() {
        var hargaBenih = parseNum(document.getElementById('hargaBenih').value);
        var berat      = parseNum(document.getElementById('berat1000').value);
        var daya       = Math.max(0, Math.min(100, parseNum(document.getElementById('dayaKecambah').value))) / 100;
        var jadi       = Math.max(0, Math.min(100, parseNum(document.getElementById('bibitJadi').value))) / 100;

        var jmlBenihPerKg    = berat > 0 ? (1000000 / berat) : 0;
        var jmlBerkecambah   = jmlBenihPerKg * daya;
        var jmlBibitJadi     = jmlBerkecambah * jadi;
        var hargaBenihButir  = jmlBibitJadi > 0 ? (hargaBenih / jmlBibitJadi) : 0;
        var hargaMentah      = hargaBenihButir + C12;
        var hargaFinal       = jmlBibitJadi > 0 ? Math.ceil(hargaMentah / 50) * 50 : 0;

        document.getElementById('prevBibitJadi').textContent  = Math.round(jmlBibitJadi).toLocaleString('id-ID');
        document.getElementById('prevHargaButir').textContent = rupiah(hargaBenihButir);
        document.getElementById('prevHargaFinal').textContent = rupiah(hargaFinal);
    }

    ['hargaBenih', 'berat1000', 'dayaKecambah', 'bibitJadi'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', preview);
    });
    preview();

    // ── Hapus detail (AJAX) ───────────────────────────────────────────────────
    window.deleteDetail = function (id) {
        if (!confirm('Hapus bibit ini dari daftar?')) return;
        fetch('<?= url('pdb/delete-detail/') ?>' + id, { method: 'POST' })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success) {
                    var row = document.getElementById('detailRow-' + id);
                    if (row) row.remove();
                } else {
                    alert('Gagal: ' + res.message);
                }
            })
            .catch(function (err) { alert('Terjadi kesalahan: ' + err); });
    };
})();
</script>
