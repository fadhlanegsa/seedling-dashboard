<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4 border-bottom-primary">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> BAHAN BAKU IN</h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <!-- Card Body -->
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-admin/store-bahan-baku') ?>" method="POST" id="bahanBakuForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6 border-right">
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Tanggal Masuk</label>
                                    <div class="col-sm-7">
                                        <input type="date" name="transaction_date" class="form-control" value="<?= $today ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">ID Transaksi</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="transaction_id" class="form-control font-weight-bold bg-light" value="<?= $transactionId ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Jenis Bahan Baku</label>
                                    <div class="col-sm-7">
                                        <select name="category" id="category" class="form-control custom-select bg-warning-light" required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['category'] ?>"><?= $cat['category'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Nama Bahan Baku</label>
                                    <div class="col-sm-7">
                                        <select name="item_id" id="item_id" class="form-control custom-select" required disabled>
                                            <option value="">-- Pilih Material --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Jumlah ( <span id="unit-label">kg</span> )</label>
                                    <div class="col-sm-7">
                                        <input type="number" step="0.01" name="quantity" class="form-control" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Keterangan</label>
                                    <div class="col-sm-7">
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6 pl-md-5">
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Pengirim</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="sender" class="form-control" placeholder="Nama instansi/orang">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Penerima</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="receiver" class="form-control" placeholder="Nama staf penerima">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Mandor / Supervisor</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="foreman" class="form-control" placeholder="Nama mandor">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Pelaksana / Manager</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="manager" class="form-control" placeholder="Nama pimpinan">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4 pt-3 border-top justify-content-center">
                            <div class="col-md-6 text-center">
                                <button type="reset" class="btn btn-secondary btn-lg mr-3 px-5 shadow-sm">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm font-weight-bold">
                                    <i class="fas fa-check"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== RIWAYAT TRANSAKSI BAHAN BAKU ===== -->
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 border-top-primary">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> Riwayat Transaksi Bahan Baku IN</h6>
                    <span class="badge badge-primary badge-pill"><?= count($recentBahanBaku ?? []) ?> Entri</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small">
                            <thead class="thead-light text-uppercase x-small font-weight-bold text-muted">
                                <tr>
                                    <th class="pl-3">ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Nama Bahan</th>
                                    <th>Kategori</th>
                                    <th class="text-right">Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Pengirim</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentBahanBaku)): ?>
                                    <?php foreach ($recentBahanBaku as $row): ?>
                                    <tr>
                                        <td class="pl-3 font-weight-bold text-primary"><?= htmlspecialchars($row['transaction_id'] ?? '-') ?></td>
                                        <td><?= formatDate($row['transaction_date'] ?? $row['date'] ?? '-') ?></td>
                                        <td class="font-weight-bold"><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                                        <td><span class="badge badge-light border"><?= htmlspecialchars($row['item_category'] ?? '-') ?></span></td>
                                        <td class="text-right font-weight-bold text-success"><?= number_format($row['quantity'], 2) ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($row['item_unit'] ?? 'kg') ?></td>
                                        <td><?= htmlspecialchars($row['sender'] ?? $row['vendor_name'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="<?= url('seedling-edit/edit-bahan-baku/' . $row['id']) ?>" 
                                               class="btn btn-xs btn-outline-primary py-0 px-2 mr-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-xs btn-outline-danger py-0 px-2 btn-delete-bb" 
                                                    data-url="<?= url('seedling-edit/delete-bahan-baku/' . $row['id']) ?>"
                                                    data-title="<?= htmlspecialchars($row['transaction_id'] ?? 'transaksi ini') ?>"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada transaksi Bahan Baku IN
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Bahan Baku -->
<div class="modal fade" id="deleteBBModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteBBForm" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="delete_reason" id="deleteBBReasonHidden" value="">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Hapus transaksi <strong id="deleteBBTitle"></strong>?</p>
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle mr-1"></i> Stok akan otomatis disesuaikan kembali.
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-danger">Alasan Hapus <span class="text-danger">*</span></label>
                        <textarea id="deleteBBReason" class="form-control border-danger" rows="2" required placeholder="Contoh: Salah input, data duplikat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteBBBtn" class="btn btn-danger" disabled>
                        <i class="fas fa-trash mr-1"></i> Hapus Permanen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-warning-light { background-color: #fffef2; border-color: #ffeeba; }
    .text-gray-700 { color: #4e4e4e !important; }
    .border-bottom-primary { border-bottom: 0.3rem solid #4e73df !important; }
    .border-top-primary { border-top: 4px solid #4e73df !important; }
    .btn-xs { font-size: 0.7rem; }
    .x-small { font-size: 0.72rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== Input Form: Category → Item AJAX =====
    const categorySelect = document.getElementById('category');
    const itemSelect = document.getElementById('item_id');
    const unitLabel = document.getElementById('unit-label');

    categorySelect.addEventListener('change', function() {
        const category = this.value;
        if (!category) {
            itemSelect.innerHTML = '<option value="">-- Pilih Material --</option>';
            itemSelect.disabled = true;
            return;
        }
        fetch(`<?= url('seedling-admin/get-items-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    itemSelect.innerHTML = '<option value="">-- Pilih Material --</option>';
                    res.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        option.dataset.unit = item.unit;
                        itemSelect.appendChild(option);
                    });
                    itemSelect.disabled = false;
                }
            });
    });

    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        unitLabel.textContent = selectedOption.dataset.unit || 'kg';
    });

    // ===== Delete Bahan Baku Modal =====
    document.querySelectorAll('.btn-delete-bb').forEach(btn => {
        btn.addEventListener('click', function() {
            const url   = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            document.getElementById('deleteBBForm').setAttribute('action', url);
            document.getElementById('deleteBBTitle').textContent = title;
            document.getElementById('deleteBBReason').value = '';
            document.getElementById('deleteBBBtn').disabled = true;
            $('#deleteBBModal').modal('show');
        });
    });

    // Enable submit button only when reason is filled
    document.getElementById('deleteBBReason').addEventListener('input', function() {
        document.getElementById('deleteBBBtn').disabled = this.value.trim().length < 3;
    });

    // Sync textarea → hidden input before submit
    document.getElementById('deleteBBForm').addEventListener('submit', function() {
        document.getElementById('deleteBBReasonHidden').value = document.getElementById('deleteBBReason').value;
    });
});
</script>
