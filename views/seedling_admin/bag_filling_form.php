<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow mb-4 border-bottom-info">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-info text-white">
                    <h6 class="m-0 font-weight-bold text-uppercase text-white"><i class="fas fa-fill-drip mr-2"></i> PENGISIAN KANTONG BIBIT</h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <!-- Card Body -->
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-admin/store-bag-filling') ?>" method="POST" id="bagFillingForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        
                        <div class="row mb-5">
                            <!-- Left: Bag Selection -->
                            <div class="col-md-7 border-right pr-4">
                                <h6 class="font-weight-bold text-danger mb-3">Kantong Bibit</h6>
                                <div class="form-group mb-3">
                                    <select name="bag_item_id" id="bag_item_id" class="form-control form-control-lg border-danger shadow-none" required style="background-color: #ffffdd;">
                                        <option value="">-- Pilih Kantong Bibit --</option>
                                        <?php foreach ($bags as $bag): ?>
                                            <option value="<?= $bag['id'] ?>" data-stock="<?= $bag['stock'] ?>" data-unit="<?= $bag['unit'] ?>">
                                                <?= $bag['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-info">Jumlah Digunakan</label>
                                            <input type="number" step="0.01" name="bag_quantity" id="bag_quantity" class="form-control" placeholder="0.00" required>
                                            <div class="invalid-feedback stock-error-msg" style="display: none;">Stok tidak mencukupi!</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">Stok Tersedia</label>
                                            <div class="input-group">
                                                <input type="text" id="bag_stock_display" class="form-control bg-light" readonly value="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-light small unit-label">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Metadata -->
                            <div class="col-md-5 pl-4">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-muted">Tanggal</label>
                                        <input type="date" name="filling_date" class="form-control form-control-sm" value="<?= $today ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-muted">Kode</label>
                                        <input type="text" name="filling_code" class="form-control form-control-sm bg-light font-weight-bold text-primary" value="<?= $fillingCode ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted">Mandor</label>
                                    <input type="text" name="mandor" class="form-control form-control-sm" placeholder="Nama mandor lapangan">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted">Pelaksana / Manager</label>
                                    <input type="text" name="manager" class="form-control form-control-sm" placeholder="Nama pimpinan">
                                </div>
                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold text-muted">Keterangan</label>
                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="...">
                                </div>
                            </div>
                        </div>

                        <!-- Media Tanam Section -->
                        <div class="card mb-4 border-0 bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-info mr-3 shadow-sm" data-toggle="modal" data-target="#mediaModal">
                                            <i class="fas fa-plus mr-1"></i> Tambah Media
                                        </button>
                                        <h6 class="m-0 font-weight-bold text-dark text-uppercase" style="letter-spacing: 1px;">MEDIA TANAM</h6>
                                    </div>
                                </div>
                                
                                <div class="table-responsive bg-white rounded shadow-sm">
                                    <table class="table table-bordered table-sm mb-0" id="media-table">
                                        <thead class="bg-dark text-white text-center small">
                                            <tr>
                                                <th width="200">Kode Produksi</th>
                                                <th>Bahan / Ingredients</th>
                                                <th width="150">Qty Digunakan (m3)</th>
                                                <th width="50">#</th>
                                            </tr>
                                        </thead>
                                        <tbody id="media-body">
                                            <!-- Rows added via Modal selection -->
                                            <tr class="empty-row">
                                                <td colspan="4" class="text-center py-4 text-muted small">Belum ada media tanam yang dipilih. Klik "Tambah" untuk memilih dari stok MT.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="row align-items-center mt-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <h5 class="m-0 font-weight-bold text-primary mr-3">JUMLAH PRODUKSI</h5>
                                    <div class="input-group" style="width: 200px;">
                                        <input type="number" step="0.01" name="total_production" class="form-control form-control-lg border-primary text-center font-weight-bold" placeholder="0.00" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light text-primary font-weight-bold">Bags</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary px-4 py-2 mr-2">Batal</a>
                                <button type="submit" class="btn btn-info px-5 py-2 font-weight-bold shadow">SIMPAN DATA</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Media Tanam -->
<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold">PILIH MEDIA TANAM</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom d-flex justify-content-end align-items-center">
                    <label class="small font-weight-bold mr-3 mb-0">Cari:</label>
                    <input type="text" id="modalSearch" class="form-control form-control-sm w-25 border-info" placeholder="Ketik kode/tanggal...">
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover table-striped mb-0 text-center small" id="stockTable">
                        <thead class="bg-white sticky-top">
                            <tr class="text-uppercase text-muted">
                                <th>Kode Produksi</th>
                                <th>Tanggal</th>
                                <th class="text-primary">Stok (m3)</th>
                                <th class="text-left">Bahan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="stock-list-body">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-between py-2">
                <small class="text-danger font-italic">* Double Klik pada baris untuk mengonfirmasi pemilihan.</small>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-info { background-color: #36b9cc !important; }
    .border-bottom-info { border-bottom: 0.3rem solid #36b9cc !important; }
    .text-info { color: #36b9cc !important; }
    .border-info { border-color: #36b9cc !important; }
    .sticky-top { top: 0; z-index: 1020; box-shadow: 0 1px 0 #dee2e6; }
    #stockTable tr { cursor: pointer; transition: 0.2s; }
    #stockTable tr:hover { background-color: #e3f2fd; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bagSelect = document.getElementById('bag_item_id');
    const bagStockDisplay = document.getElementById('bag_stock_display');
    const unitLabel = document.querySelector('.unit-label');
    const mediaBody = document.getElementById('media-body');
    const stockListBody = document.getElementById('stock-list-body');
    const modalSearch = document.getElementById('modalSearch');

    const bagQty = document.getElementById('bag_quantity');
    const bagError = document.querySelector('.stock-error-msg');

    // Handle Bag Selection Stock Display
    bagSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            bagStockDisplay.value = selected.dataset.stock;
            unitLabel.textContent = selected.dataset.unit;
            validateBagStock();
        } else {
            bagStockDisplay.value = '0';
            unitLabel.textContent = '-';
        }
    });

    bagQty.addEventListener('input', validateBagStock);

    function validateBagStock() {
        const stock = parseFloat(bagStockDisplay.value || 0);
        const qty = parseFloat(bagQty.value || 0);
        if (qty > stock) {
            bagError.style.display = 'block';
            bagQty.classList.add('is-invalid');
        } else {
            bagError.style.display = 'none';
            bagQty.classList.remove('is-invalid');
        }
    }

    // Handle Modal Open - Fetch MT Stock
    $('#mediaModal').on('show.bs.modal', function () {
        fetchStock();
    });

    function fetchStock() {
        fetch('<?= url('seedling-admin/get-media-stock-ajax') ?>')
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    renderStockList(res.data);
                }
            });
    }

    function renderStockList(data) {
        stockListBody.innerHTML = '';
        if (data.length === 0) {
            stockListBody.innerHTML = '<tr><td colspan="5" class="py-5">Belum ada stok media yang tersedia.</td></tr>';
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="font-weight-bold text-dark">${item.production_code}</td>
                <td>${item.production_date}</td>
                <td class="font-weight-bold text-primary">${parseFloat(item.remaining_stock).toFixed(2)}</td>
                <td class="text-left font-italic text-muted small">${item.ingredients || '-'}</td>
                <td>${item.notes || '-'}</td>
            `;
            
            // Double click to select
            tr.addEventListener('dblclick', function() {
                addMediaToTable(item);
                $('#mediaModal').modal('hide');
            });

            stockListBody.appendChild(tr);
        });
    }

    function addMediaToTable(item) {
        // Remove empty row if present
        const empty = mediaBody.querySelector('.empty-row');
        if (empty) empty.remove();

        // Avoid duplicates
        if (mediaBody.querySelector(`input[value="${item.id}"]`)) {
            alert('Media ini sudah ada di tabel.');
            return;
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center font-weight-bold">
                ${item.production_code}
                <input type="hidden" name="media_production_id[]" value="${item.id}">
            </td>
            <td class="small text-muted">${item.ingredients}</td>
            <td>
                <input type="number" step="0.01" name="media_quantity[]" class="form-control form-control-sm text-center media-qty-input" 
                       placeholder="Maks: ${parseFloat(item.remaining_stock).toFixed(2)}" data-max="${item.remaining_stock}" required>
                <div class="text-danger small stock-warning" style="display:none;">Stok tidak mencukupi!</div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger btn-remove-media"><i class="fas fa-trash"></i></button>
            </td>
        `;

        const qtyInput = tr.querySelector('.media-qty-input');
        const warning = tr.querySelector('.stock-warning');

        qtyInput.addEventListener('input', function() {
            const max = parseFloat(this.dataset.max);
            const val = parseFloat(this.value || 0);
            if (val > max) {
                warning.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                warning.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });

        tr.querySelector('.btn-remove-media').addEventListener('click', function() {
            tr.remove();
            if (mediaBody.children.length === 0) {
                mediaBody.innerHTML = '<tr class="empty-row"><td colspan="4" class="text-center py-4 text-muted small">Belum ada media tanam yang dipilih. Klik "Tambah" untuk memilih dari stok MT.</td></tr>';
            }
        });

        mediaBody.appendChild(tr);
    }

    // Modal Search Filter
    modalSearch.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const rows = stockListBody.querySelectorAll('tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
});
</script>
