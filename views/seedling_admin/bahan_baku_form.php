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
                <div class="card-body px-3 px-md-5 py-4">
                    <form action="<?= url('seedling-admin/store-bahan-baku') ?>" method="POST" id="bahanBakuForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                        
                        <!-- Form PWA Style -->
                        <div class="form-wrapper">
                            
                            <!-- Header Transaksi -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="font-weight-bold text-muted small px-1 mb-1">TANGGAL MASUK</label>
                                    <input type="date" name="transaction_date" class="form-control font-weight-bold border-0 shadow-sm" value="<?= $today ?>" required style="background: #f8f9fa;">
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold text-muted small px-1 mb-1">ID TRANSAKSI</label>
                                    <input type="text" name="transaction_id" class="form-control font-weight-bold border-0 shadow-sm" value="<?= $transactionId ?>" readonly style="background: #e9ecef;">
                                </div>
                            </div>

                            <!-- STANDAR 1: Tap-able Cards untuk Jenis Bahan Baku -->
                            <div class="form-group mb-4 mt-4">
                                <label class="font-weight-bold text-muted small px-1 mb-2">JENIS BAHAN BAKU <span class="text-danger">*</span></label>
                                <div class="row" style="margin: -5px;">
                                    <?php foreach ($categories as $index => $cat): ?>
                                    <div class="col-6 col-md-4" style="padding: 5px;">
                                        <input type="radio" class="btn-check" name="category" id="cat_<?= $index ?>" value="<?= $cat['category'] ?>" autocomplete="off" required>
                                        <label class="radio-card w-100 p-2 h-100 d-flex flex-column align-items-center justify-content-center text-center" for="cat_<?= $index ?>" style="min-height: 80px;">
                                            <span class="font-weight-bold" style="font-size: 0.85rem;"><?= $cat['category'] ?></span>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted small px-1">NAMA BAHAN BAKU <span class="text-danger">*</span></label>
                                <select name="item_id" id="item_id" class="form-control custom-select shadow-sm font-weight-bold" required disabled style="height: 50px;">
                                    <option value="">-- Pilih Material --</option>
                                </select>
                            </div>

                            <div class="form-group mb-4" id="seed-source-group" style="display: none;">
                                <label class="font-weight-bold text-muted small px-1">SUMBER BENIH <small>(Opsional)</small></label>
                                <select name="seed_source_id" id="seed_source_id" class="form-control custom-select shadow-sm" style="height: 50px;">
                                    <option value="">-- Pilih Sumber Benih --</option>
                                    <?php if(isset($seedSources)): foreach ($seedSources as $source): ?>
                                        <option value="<?= $source['id'] ?>"><?= htmlspecialchars($source['seed_source_name']) ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>

                            <!-- STANDAR 2: Optimasi Input Angka -->
                            <div class="form-group mb-4 mt-4">
                                <label class="font-weight-bold text-muted small px-1">JUMLAH ( <span id="unit-label" class="text-primary font-weight-bold">kg</span> ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg shadow-sm overflow-hidden" style="border-radius: 12px; border: 1px solid #ced4da;">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-light border-0 px-4 h-100 d-flex align-items-center justify-content-center" type="button" onclick="ubahQty(-1)" style="width: 70px; font-size: 1.5rem; font-weight: bold; color: var(--ppth-green);">-</button>
                                    </div>
                                    <!-- Menggunakan step="any" dan inputmode="decimal" karena qty bahan baku bisa desimal -->
                                    <input type="number" step="any" inputmode="decimal" name="quantity" id="qty_input" class="form-control border-0 text-center font-weight-bold shadow-none" 
                                           placeholder="0" onfocus="this.select()" required style="background: white; height: 70px; font-size: 2rem; color: var(--ppth-green);">
                                    <div class="input-group-append">
                                        <button class="btn btn-light border-0 px-4 h-100 d-flex align-items-center justify-content-center" type="button" onclick="ubahQty(1)" style="width: 70px; font-size: 1.5rem; font-weight: bold; color: var(--ppth-green);">+</button>
                                    </div>
                                </div>
                            </div>

                            <!-- STANDAR 4: Minimalist Form (Accordion) untuk Data Tambahan -->
                            <div class="form-group mb-4">
                                <div class="accordion shadow-sm border-0 overflow-hidden" id="accordionOptional" style="border-radius: 12px;">
                                    <div class="card border-0">
                                        <div class="card-header bg-white p-0" id="headingNotes">
                                            <button class="btn btn-block text-left font-weight-bold text-muted p-3 collapsed d-flex align-items-center justify-content-between btn-accordion-custom" type="button" data-toggle="collapse" data-target="#collapseNotes" aria-expanded="false" aria-controls="collapseNotes" style="box-shadow: none;">
                                                <span><i class="fas fa-list-alt mr-2" style="color: var(--ppth-green);"></i> Data Tambahan (Opsional)</span>
                                                <i class="fas fa-chevron-down small"></i>
                                            </button>
                                        </div>
                                        <div id="collapseNotes" class="collapse" aria-labelledby="headingNotes" data-parent="#accordionOptional">
                                            <div class="card-body bg-light p-3 border-top">
                                                
                                                <div class="form-group mb-3">
                                                    <label class="small font-weight-bold text-muted">Keterangan / Catatan</label>
                                                    <textarea name="notes" class="form-control border-0 shadow-sm" rows="2" placeholder="Tambahkan catatan..."></textarea>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group mb-3">
                                                            <label class="small font-weight-bold text-muted">Pengirim</label>
                                                            <input type="text" name="sender" class="form-control border-0 shadow-sm" placeholder="Instansi/Orang">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-3">
                                                            <label class="small font-weight-bold text-muted">Penerima</label>
                                                            <input type="text" name="receiver" class="form-control border-0 shadow-sm" placeholder="Staf penerima">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold text-muted">Mandor / Supervisor</label>
                                                            <input type="text" name="foreman" class="form-control border-0 shadow-sm" placeholder="Nama mandor">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold text-muted">Pelaksana / Manager</label>
                                                            <input type="text" name="manager" class="form-control border-0 shadow-sm" placeholder="Nama pimpinan">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- STANDAR 3: Sticky Bottom Bar -->
                        <div class="sticky-bottom-bar">
                            <div class="container p-0">
                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="d-flex justify-content-between align-items-center" style="gap: 10px;">
                                            <button type="reset" class="btn btn-light border btn-lg font-weight-bold shadow-sm" style="width: 25%; border-radius: 50px;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button type="submit" class="btn btn-lg font-weight-bold shadow d-flex align-items-center justify-content-center text-white" style="width: 75%; border-radius: 50px; background-color: var(--ppth-green); border-color: var(--ppth-green);">
                                                <i class="fas fa-check mr-2"></i> SIMPAN
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                    <span class="badge badge-primary badge-pill"><?= $pagination['total'] ?? 0 ?> Total Entri</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small">
                            <thead class="thead-light text-uppercase x-small font-weight-bold text-muted">
                                <tr>
                                    <th class="pl-3">ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Nama Bahan</th>
                                    <th>Sumber Benih</th>
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
                                        <td><span class="text-muted"><?= htmlspecialchars($row['seed_source_name'] ?? '-') ?></span></td>
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
                <?php if (!empty($pagination) && $pagination['totalPages'] > 1): ?>
                <div class="card-footer bg-light py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted font-italic">
                            Menampilkan <?= count($recentBahanBaku) ?> dari <?= $pagination['total'] ?> transaksi
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Previous -->
                                <li class="page-item <?= $pagination['page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('seedling-admin/bahan-baku-form?page=' . ($pagination['page'] - 1)) ?>" tabindex="-1">Sebelumnya</a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php 
                                $start = max(1, $pagination['page'] - 2);
                                $end = min($pagination['totalPages'], $pagination['page'] + 2);
                                
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="'.url('seedling-admin/bahan-baku-form?page=1').'">1</a></li>';
                                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                
                                for ($i = $start; $i <= $end; $i++): 
                                ?>
                                    <li class="page-item <?= $i == $pagination['page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= url('seedling-admin/bahan-baku-form?page=' . $i) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($end < $pagination['totalPages']): ?>
                                    <?php if ($end < $pagination['totalPages'] - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; ?>
                                    <li class="page-item"><a class="page-link" href="<?= url('seedling-admin/bahan-baku-form?page=' . $pagination['totalPages']) ?>"><?= $pagination['totalPages'] ?></a></li>
                                <?php endif; ?>
                                
                                <!-- Next -->
                                <li class="page-item <?= $pagination['page'] >= $pagination['totalPages'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= url('seedling-admin/bahan-baku-form?page=' . ($pagination['page'] + 1)) ?>">Berikutnya</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
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
    /* Tema Warna PPTH (Hijau Earth-tone) */
    :root {
        --ppth-green: #2e7d32;
        --ppth-green-light: #e8f5e9;
        --ppth-green-dark: #1b5e20;
    }
    .bg-warning-light { background-color: #fffef2; border-color: #ffeeba; }
    .text-gray-700 { color: #4e4e4e !important; }
    .border-bottom-primary { border-bottom: 0.3rem solid #4e73df !important; }
    .border-top-primary { border-top: 4px solid #4e73df !important; }
    .btn-xs { font-size: 0.7rem; }
    .x-small { font-size: 0.72rem; }

    /* 1. Styling untuk Tap-able Cards (Kompatibel dengan Bootstrap 4) */
    .btn-check {
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }
    
    .radio-card {
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 2px solid var(--ppth-green);
        background-color: white;
        color: var(--ppth-green);
        cursor: pointer;
        margin: 0;
    }
    
    .radio-card:hover {
        background-color: var(--ppth-green-light);
    }

    .btn-check:checked + .radio-card {
        background-color: var(--ppth-green) !important;
        color: white !important;
        border-color: var(--ppth-green) !important;
        box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
    }

    .radio-card:active {
        transform: scale(0.95);
    }
    
    /* 2. Styling untuk Input Angka */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* 3. Wrapper untuk Sticky Bottom Bar */
    .sticky-bottom-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        z-index: 1030;
        padding: 1rem;
        padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0));
    }

    /* Kustomisasi styling Accordion */
    .btn-accordion-custom:not(.collapsed) {
        background-color: var(--ppth-green-light);
        color: var(--ppth-green) !important;
    }
    
    /* Margin bawah ekstra untuk scroll */
    .form-wrapper {
        padding-bottom: 180px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== Input Form: Category → Item AJAX =====
    const categoryRadios = document.querySelectorAll('input[name="category"]');
    const itemSelect = document.getElementById('item_id');
    const unitLabel = document.getElementById('unit-label');

    categoryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const category = this.value;
            const seedSourceGroup = document.getElementById('seed-source-group');
            
            if (category === 'BENIH') {
                seedSourceGroup.style.display = 'block';
            } else {
                seedSourceGroup.style.display = 'none';
                document.getElementById('seed_source_id').value = '';
            }

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
    });

    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        unitLabel.textContent = selectedOption.dataset.unit || 'kg';
    });

    // JS Logic untuk merubah ikon chevron pada Accordion
    $('#accordionOptional').on('show.bs.collapse', function () {
        $(this).find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }).on('hide.bs.collapse', function () {
        $(this).find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // JS Logic untuk tombol - / +
    window.ubahQty = function(delta) {
        const input = document.getElementById('qty_input');
        let val = parseFloat(input.value) || 0;
        val += delta;
        if (val < 0) val = 0; 
        input.value = val;
    };

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
