<?php /** Edit Form: Media Mixing Production */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e; border-left-width:4px!important;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div>
                    <strong>Mode Edit Aktif</strong> — Mengedit produksi <code><?= htmlspecialchars($data['production_code']) ?></code>. Semua bahan lama akan dihapus dan diganti bahan baru.
                </div>
            </div>

            <div class="card shadow mb-4" style="border-top:4px solid #f6c23e;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-success text-white">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-edit mr-2"></i> EDIT PRODUKSI MEDIA TANAM — <?= htmlspecialchars($data['production_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-edit/update-media-mixing/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <!-- Bahan Table -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold text-primary mb-0">Komposisi Bahan</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row">
                                    <i class="fas fa-plus"></i> Tambah Bahan
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="ingredients-table">
                                    <thead class="bg-dark text-white text-center small text-uppercase font-weight-bold">
                                        <tr>
                                            <th width="200">Kategori</th>
                                            <th>Nama Bahan Baku</th>
                                            <th width="150">Jumlah</th>
                                            <th width="100">Satuan</th>
                                            <th width="50">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ingredients-body">
                                        <?php foreach ($currentItems as $ci): ?>
                                        <tr class="existing-row">
                                            <td><input type="text" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($ci['category'] ?? '-') ?>" readonly></td>
                                            <td>
                                                <input type="hidden" name="item_id[]" value="<?= $ci['item_id'] ?>">
                                                <input type="text" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($ci['item_name'] ?? 'Item #' . $ci['item_id']) ?>" readonly>
                                            </td>
                                            <td><input type="number" step="0.01" name="quantity[]" class="form-control form-control-sm" value="<?= $ci['quantity'] ?>" required></td>
                                            <td class="text-center align-middle"><span class="badge badge-light"><?= htmlspecialchars($ci['unit'] ?? '-') ?></span></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger btn-remove-row p-0"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Kode Produksi</label>
                                    <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['production_code']) ?>" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Mandor</label>
                                    <input type="text" name="mandor" class="form-control form-control-sm" value="<?= htmlspecialchars($data['foreman'] ?? '') ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Pelaksana / Manager</label>
                                    <input type="text" name="manager" class="form-control form-control-sm" value="<?= htmlspecialchars($data['manager'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Tanggal Produksi</label>
                                    <input type="date" name="production_date" class="form-control" value="<?= $data['production_date'] ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Pengambil Bahan</label>
                                    <input type="text" name="location" class="form-control form-control-sm" value="<?= htmlspecialchars($data['picker_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-primary">Total Produksi</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="total_production" class="form-control form-control-lg border-primary text-center font-weight-bold" style="background-color:#ffffcc;" value="<?= $data['total_production'] ?>" required>
                                        <div class="input-group-append"><span class="input-group-text bg-light font-weight-bold">m3</span></div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Keterangan</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                                </div>
                                <!-- AUDIT TRAIL -->
                                <div class="form-group mb-3 p-3 bg-warning-soft rounded border border-warning">
                                    <label class="small font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-1"></i> Alasan Edit <span class="text-danger">*</span></label>
                                    <textarea name="edit_reason" class="form-control border-danger" rows="3" required placeholder="Wajib diisi. Contoh: Koreksi jumlah bahan, kesalahan input, dll..."></textarea>
                                    <small class="text-danger">Wajib untuk Audit Trail.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top justify-content-end">
                            <div class="col-auto">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary mr-2 py-2 px-4">Batal</a>
                                <button type="submit" class="btn btn-warning font-weight-bold py-2 px-5 text-dark"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="ingredient-row-template">
    <tr>
        <td>
            <select class="form-control form-control-sm category-select shadow-none">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($items as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category'] ?? '') ?>"><?= htmlspecialchars($cat['category'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="item_id[]" class="form-control form-control-sm item-select shadow-none" required disabled>
                <option value="">-- Pilih Bahan --</option>
            </select>
        </td>
        <td><input type="number" step="0.01" name="quantity[]" class="form-control form-control-sm text-right" placeholder="0.00" required></td>
        <td class="text-center align-middle"><span class="badge badge-light unit-label">-</span></td>
        <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-link text-danger btn-remove-row p-0"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<style>
.bg-warning-soft { background-color: #fff9e6; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const body = document.getElementById('ingredients-body');
    const template = document.getElementById('ingredient-row-template').innerHTML;
    document.getElementById('btn-add-row').addEventListener('click', () => addRow());

    // Remove buttons on existing rows
    body.querySelectorAll('.btn-remove-row').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('tr').remove());
    });

    function addRow() {
        const div = document.createElement('tbody');
        div.innerHTML = template;
        const row = div.querySelector('tr');
        body.appendChild(row);

        const catSel = row.querySelector('.category-select');
        const itemSel = row.querySelector('.item-select');
        const unitLbl = row.querySelector('.unit-label');

        catSel.addEventListener('change', function() {
            const cat = this.value;
            if (!cat) { itemSel.innerHTML = '<option value="">-- Pilih Bahan --</option>'; itemSel.disabled = true; return; }
            fetch(`<?= url('seedling-admin/get-items-by-category') ?>?category=${encodeURIComponent(cat)}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        itemSel.innerHTML = '<option value="">-- Pilih Bahan --</option>';
                        res.data.forEach(i => {
                            const o = document.createElement('option');
                            o.value = i.id; o.textContent = i.name;
                            o.dataset.unit = i.unit; itemSel.appendChild(o);
                        });
                        itemSel.disabled = false;
                    }
                });
        });
        itemSel.addEventListener('change', () => { const o = itemSel.options[itemSel.selectedIndex]; unitLbl.textContent = o.dataset.unit || '-'; });
        row.querySelector('.btn-remove-row').addEventListener('click', () => row.remove());
    }
});
</script>
