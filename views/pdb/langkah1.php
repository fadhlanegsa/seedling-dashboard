<?php
/**
 * PDB — Langkah 1: Form Biaya Operasional (Master)
 * Pelaku Usaha menginput rincian Biaya A (Tetap) & Biaya B (Tidak Tetap)
 * serta Target Produksi. Nilai C12 dihitung otomatis (live + server-side).
 */
$c12   = $master['biaya_produksi_per_batang_c12'] ?? 0;
$rowsA = !empty($rincian['a']) ? $rincian['a'] : [['nama' => '', 'nominal' => '']];
$rowsB = !empty($rincian['b']) ? $rincian['b'] : [['nama' => '', 'nominal' => '']];
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="mb-0"><i class="fas fa-coins text-warning"></i> Input PDB — Biaya Operasional</h1>
    <span class="badge badge-pill badge-primary p-2">Langkah 1 dari 2</span>
</div>

<!-- Step indicator -->
<div class="card mb-3">
    <div class="card-body py-2 d-flex align-items-center">
        <span class="badge badge-primary badge-pill mr-2">1</span>
        <strong class="mr-3">Biaya Operasional</strong>
        <i class="fas fa-arrow-right text-muted mr-3"></i>
        <span class="badge badge-secondary badge-pill mr-2">2</span>
        <span class="text-muted">Detail Bibit</span>
    </div>
</div>

<?php if ($flash = $this->getFlash()): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
        <?= $flash['message'] ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<form action="<?= url('pdb/store-langkah1') ?>" method="POST" id="langkah1Form">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

    <!-- Periode -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="form-row align-items-end">
                <div class="col-md-4">
                    <label class="font-weight-bold">Periode Tahun</label>
                    <select name="periode_tahun" id="periodeTahun" class="form-control">
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Mengganti tahun akan memuat data periode tersebut.</small>
                </div>
                <?php if ($master): ?>
                    <div class="col-md-8 text-md-right mt-2 mt-md-0">
                        <span class="badge badge-info p-2">
                            <i class="fas fa-info-circle"></i>
                            Data periode <?= $year ?> sudah ada — perubahan akan menimpa data lama.
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- BIAYA A (TETAP) -->
        <div class="col-lg-6">
            <div class="card mb-3 h-100">
                <div class="card-header bg-white border-bottom border-primary d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary text-uppercase">
                        <i class="fas fa-lock"></i> Biaya A — Tetap
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('A')">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" id="tableA">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th style="width:55%">Uraian (ATK, Honor, Mandor, dll)</th>
                                <th style="width:38%" class="text-right">Nominal (Rp)</th>
                                <th style="width:7%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rowsA as $r): ?>
                                <tr>
                                    <td><input type="text" name="biaya_a_nama[]" class="form-control form-control-sm" value="<?= htmlspecialchars($r['nama'] ?? '') ?>" placeholder="mis. Honor mandor"></td>
                                    <td><input type="text" name="biaya_a_nominal[]" class="form-control form-control-sm text-right nominal" value="<?= htmlspecialchars($r['nominal'] ?? '') ?>" inputmode="numeric" placeholder="0"></td>
                                    <td class="text-center"><i class="fas fa-times text-danger remove-row" role="button" onclick="removeRow(this)"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-right">Total Biaya A</th>
                                <th class="text-right text-primary" id="totalA_label">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- BIAYA B (TIDAK TETAP) -->
        <div class="col-lg-6">
            <div class="card mb-3 h-100">
                <div class="card-header bg-white border-bottom border-warning d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning text-uppercase">
                        <i class="fas fa-unlock"></i> Biaya B — Tidak Tetap
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="addRow('B')">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" id="tableB">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th style="width:55%">Uraian (Media, Polybag, Pupuk, Upah, dll)</th>
                                <th style="width:38%" class="text-right">Nominal (Rp)</th>
                                <th style="width:7%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rowsB as $r): ?>
                                <tr>
                                    <td><input type="text" name="biaya_b_nama[]" class="form-control form-control-sm" value="<?= htmlspecialchars($r['nama'] ?? '') ?>" placeholder="mis. Polybag"></td>
                                    <td><input type="text" name="biaya_b_nominal[]" class="form-control form-control-sm text-right nominal" value="<?= htmlspecialchars($r['nominal'] ?? '') ?>" inputmode="numeric" placeholder="0"></td>
                                    <td class="text-center"><i class="fas fa-times text-danger remove-row" role="button" onclick="removeRow(this)"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-right">Total Biaya B</th>
                                <th class="text-right text-warning" id="totalB_label">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TARGET & HASIL C12 -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="form-row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold">Target Produksi Total (batang)</label>
                    <input type="text" name="target_produksi_total" id="targetProduksi" class="form-control"
                           inputmode="numeric" value="<?= htmlspecialchars($master['target_produksi_total'] ?? '') ?>"
                           placeholder="mis. 50000" required>
                    <small class="text-muted">Total batang produksi keseluruhan (pembagi).</small>
                </div>
                <div class="col-md-8 mb-2">
                    <div class="p-3 rounded bg-light border">
                        <div class="row text-center">
                            <div class="col-4 border-right">
                                <div class="small text-muted">Total (A + B)</div>
                                <div class="h5 mb-0 font-weight-bold" id="totalAB_label">Rp 0</div>
                            </div>
                            <div class="col-4 border-right">
                                <div class="small text-muted">Target Produksi</div>
                                <div class="h5 mb-0 font-weight-bold" id="target_label">0</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted">Biaya Produksi / Batang (C12)</div>
                                <div class="h4 mb-0 font-weight-bold text-success" id="c12_label">Rp <?= formatNumber(round($c12)) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="fas fa-save"></i> Simpan &amp; Lanjut ke Langkah 2
        </button>
    </div>
</form>

<style>
    .remove-row { cursor: pointer; }
    .page-header h1 { font-size: 1.5rem; }
    #tableA td, #tableB td, #tableA th, #tableB th { vertical-align: middle; padding: .4rem .5rem; }
</style>

<script nonce="<?= cspNonce() ?>">
(function () {
    // Parser angka yang mengikuti logika parseDecimal() di server:
    // separator paling kanan dianggap desimal.
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

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function sumColumn(tableId) {
        var total = 0;
        document.querySelectorAll('#' + tableId + ' .nominal').forEach(function (inp) {
            total += parseNum(inp.value);
        });
        return total;
    }

    window.recalc = function () {
        var totalA = sumColumn('tableA');
        var totalB = sumColumn('tableB');
        var target = parseNum(document.getElementById('targetProduksi').value);
        var totalAB = totalA + totalB;
        var c12 = target > 0 ? (totalAB / target) : 0;   // guard division by zero

        document.getElementById('totalA_label').textContent  = rupiah(totalA);
        document.getElementById('totalB_label').textContent  = rupiah(totalB);
        document.getElementById('totalAB_label').textContent = rupiah(totalAB);
        document.getElementById('target_label').textContent  = Math.round(target).toLocaleString('id-ID');
        document.getElementById('c12_label').textContent     = rupiah(c12);
    };

    // Tambah baris rincian
    window.addRow = function (which) {
        var tbody = document.querySelector('#table' + which + ' tbody');
        var nameAttr = which === 'A' ? 'biaya_a' : 'biaya_b';
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" name="' + nameAttr + '_nama[]" class="form-control form-control-sm" placeholder="Uraian"></td>' +
            '<td><input type="text" name="' + nameAttr + '_nominal[]" class="form-control form-control-sm text-right nominal" inputmode="numeric" placeholder="0"></td>' +
            '<td class="text-center"><i class="fas fa-times text-danger remove-row" role="button" onclick="removeRow(this)"></i></td>';
        tbody.appendChild(tr);
    };

    // Hapus baris (sisakan minimal 1)
    window.removeRow = function (icon) {
        var tr = icon.closest('tr');
        var tbody = tr.parentNode;
        if (tbody.querySelectorAll('tr').length > 1) {
            tr.remove();
        } else {
            tr.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        }
        recalc();
    };

    // Recalc pada setiap input (event delegation)
    document.getElementById('langkah1Form').addEventListener('input', function (e) {
        if (e.target.classList.contains('nominal') || e.target.id === 'targetProduksi') {
            recalc();
        }
    });

    // Ganti tahun -> reload dengan prefill periode tsb
    document.getElementById('periodeTahun').addEventListener('change', function () {
        window.location = '<?= url('pdb/langkah1') ?>?year=' + this.value;
    });

    // Validasi ringan sebelum submit
    document.getElementById('langkah1Form').addEventListener('submit', function (e) {
        var target = parseNum(document.getElementById('targetProduksi').value);
        if (target <= 0) {
            e.preventDefault();
            alert('Target Produksi Total harus lebih dari 0 (dipakai sebagai pembagi C12).');
        }
    });

    recalc();
})();
</script>
