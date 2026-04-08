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

<style>
    .bg-warning-light {
        background-color: #fffef2;
        border-color: #ffeeba;
    }
    .text-gray-700 {
        color: #4e4e4e !important;
    }
    .border-bottom-primary {
        border-bottom: 0.3rem solid #4e73df !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

        // Fetch items via AJAX
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
                } else {
                    alert('Gagal mengambil data material');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi');
            });
    });

    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const unit = selectedOption.dataset.unit || 'kg';
        unitLabel.textContent = unit;
    });
});
</script>
