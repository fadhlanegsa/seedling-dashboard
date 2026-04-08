<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow mb-4 border-bottom-primary">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold text-uppercase"><i class="fas fa-blender mr-2"></i> PENCAMPURAN MEDIA TANAM</h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <!-- Card Body -->
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-admin/store-media-mixing') ?>" method="POST" id="mediaMixingForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        
                        <!-- Ingredient Table Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row">
                                        <i class="fas fa-plus"></i> Tambah Bahan
                                    </button>
                                </div>
                                <div class="text-warning font-weight-bold small text-uppercase">BAHAN BAKU</div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="ingredients-table">
                                    <thead class="bg-light text-center small text-uppercase font-weight-bold">
                                        <tr>
                                            <th width="200">Kategori</th>
                                            <th>Nama Bahan Baku</th>
                                            <th width="150">Jumlah</th>
                                            <th width="100">Satuan</th>
                                            <th width="50">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ingredients-body">
                                        <!-- Rows added via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label font-weight-bold text-primary small text-uppercase">Pengambil Bahan Baku</label>
                            <div class="col-sm-10">
                                <input type="text" name="picker_name" class="form-control form-control-sm border-left-primary" placeholder="Nama staf yang mengambil bahan dari gudang">
                            </div>
                        </div>

                        <hr>

                        <div class="row mt-4">
                            <!-- Left Details -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Kode Produksi</label>
                                    <input type="text" name="production_code" class="form-control bg-light font-weight-bold" value="<?= $productionCode ?>" readonly>
                                </div>
                                <div class="form-group mb-3 text-secondary">
                                    <label class="small font-weight-bold">Mandor</label>
                                    <input type="text" name="foreman" class="form-control form-control-sm" placeholder="Nama mandor">
                                </div>
                                <div class="form-group mb-3 text-secondary">
                                    <label class="small font-weight-bold">Pelaksana / Manager</label>
                                    <input type="text" name="manager" class="form-control form-control-sm" placeholder="Nama pimpinan">
                                </div>
                            </div>

                            <!-- Center Details -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Tanggal</label>
                                    <input type="date" name="production_date" class="form-control" value="<?= $today ?>" required>
                                </div>
                            </div>

                            <!-- Right Details (Total) -->
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-primary">Total Produksi</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="total_production" class="form-control form-control-lg border-primary text-center font-weight-bold" style="background-color: #ffffcc;" placeholder="0.00" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light font-weight-bold">m3</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Keterangan</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Catatan tambahan hasil pencampuran..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4 pt-3 border-top justify-content-end">
                            <div class="col-auto">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary mr-2 py-2 px-4 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary font-weight-bold py-2 px-5 shadow-sm">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Template Row -->
<template id="ingredient-row-template">
    <tr>
        <td>
            <select class="form-control form-control-sm category-select shadow-none">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category'] ?>"><?= $cat['category'] ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="item_id[]" class="form-control form-control-sm item-select shadow-none" required disabled>
                <option value="">-- Pilih Bahan --</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-right ingredient-qty" placeholder="0.00" required>
            <div class="text-danger small stock-error" style="display:none; font-size: 10px;">Stok tidak mencukupi!</div>
        </td>
        <td class="text-center align-middle">
            <span class="badge badge-light unit-label">-</span><br>
            <small class="text-muted d-block" style="font-size: 9px;">Stok: <span class="stock-label">0</span></small>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row p-0">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<style>
    .border-left-primary {
        border-left: 0.3rem solid #4e73df !important;
    }
    .form-control-sm { border-radius: 0.2rem; }
    #ingredients-table th { background-color: #f8f9fc; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const body = document.getElementById('ingredients-body');
    const template = document.getElementById('ingredient-row-template').innerHTML;
    const btnAdd = document.getElementById('btn-add-row');

    function addRow() {
        const div = document.createElement('tbody');
        div.innerHTML = template;
        const row = div.querySelector('tr');
        body.appendChild(row);

        const categorySelect = row.querySelector('.category-select');
        const itemSelect = row.querySelector('.item-select');
        const unitLabel = row.querySelector('.unit-label');
        const stockLabel = row.querySelector('.stock-label');
        const qtyInput = row.querySelector('.ingredient-qty');
        const errorMsg = row.querySelector('.stock-error');
        const btnRemove = row.querySelector('.btn-remove-row');

        function validateStock() {
            const selected = itemSelect.options[itemSelect.selectedIndex];
            const stock = parseFloat(selected ? selected.dataset.stock : 0);
            const qty = parseFloat(qtyInput.value || 0);

            if (qty > stock) {
                errorMsg.style.display = 'block';
                qtyInput.classList.add('is-invalid');
            } else {
                errorMsg.style.display = 'none';
                qtyInput.classList.remove('is-invalid');
            }
        }

        // Category change logic
        categorySelect.addEventListener('change', function() {
            const category = this.value;
            if (!category) {
                itemSelect.innerHTML = '<option value="">-- Pilih Bahan --</option>';
                itemSelect.disabled = true;
                unitLabel.textContent = '-';
                stockLabel.textContent = '0';
                return;
            }

            // Fetch items
            fetch(`<?= url('seedling-admin/get-items-by-category') ?>?category=${encodeURIComponent(category)}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        itemSelect.innerHTML = '<option value="">-- Pilih Bahan --</option>';
                        res.data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            opt.dataset.unit = item.unit;
                            opt.dataset.stock = item.stock;
                            itemSelect.appendChild(opt);
                        });
                        itemSelect.disabled = false;
                    }
                });
        });

        // Item change logic (unit update)
        itemSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            unitLabel.textContent = opt.dataset.unit || '-';
            stockLabel.textContent = opt.dataset.stock || '0';
            validateStock();
        });

        qtyInput.addEventListener('input', validateStock);

        // Remove logic
        btnRemove.addEventListener('click', function() {
            row.remove();
        });
    }

    // Add initial row
    addRow();

    btnAdd.addEventListener('click', addRow);
});
</script>
