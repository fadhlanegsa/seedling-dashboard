<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">PENABURAN BENIH</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan Bibit</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penaburan Benih</li>
            </ol>
        </nav>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('seedling-admin/store-seed-sowing') ?>" method="POST" id="sowingForm">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="row">
            <!-- LEFT COLUMN: Polybags & Materials (Dynamic Tables) -->
            <div class="col-lg-7">
                <!-- Polybag Isi Media Tanam Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom border-warning">
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-sm btn-light font-weight-bold" onclick="openPolybagModal()">Tambah</button>
                        </div>
                        <h6 class="m-0 font-weight-bold text-warning text-uppercase">POLYBAG ISI MEDIA TANAM</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="polybagTable">
                                <thead class="bg-light small text-dark font-weight-bold">
                                    <tr>
                                        <th width="30%" class="text-center">Kode</th>
                                        <th width="40%">Jenis Polybag</th>
                                        <th width="20%" class="text-center">Jumlah</th>
                                        <th width="10%" class="text-center">Satuan</th>
                                        <th width="40px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyPolybagRow">
                                        <td colspan="5" class="text-center text-muted py-3">Klik tombol Tambah untuk memilih polybag isi media (PB)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bahan Baku Pendukung Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom border-warning">
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-sm btn-light font-weight-bold" onclick="openMaterialModal()">Tambah</button>
                        </div>
                        <h6 class="m-0 font-weight-bold text-warning text-uppercase">BAHAN BAKU PENDUKUNG</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="materialTable">
                                <thead class="bg-light small text-dark font-weight-bold">
                                    <tr>
                                        <th width="60%">Nama Bahan Baku</th>
                                        <th width="20%" class="text-center">Jumlah</th>
                                        <th width="20%" class="text-center">Satuan</th>
                                        <th width="40px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyMaterialRow">
                                        <td colspan="4" class="text-center text-muted py-3">Opsional: Tambahkan pupuk, obat, atau bahan pendukung lainnya</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Seed Info & Metadata -->
            <div class="col-lg-5">
                <div class="card shadow mb-4 border-top-primary">
                    <div class="card-body">
                        
                        <div class="row form-group">
                            <div class="col-6">
                                <label class="small font-weight-bold text-info">Tanggal</label>
                                <input type="date" name="sowing_date" class="form-control font-weight-bold" value="<?= $today ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="small font-weight-bold text-info">Kode</label>
                                <input type="text" name="sowing_code" class="form-control bg-light font-weight-bold shadow-none" value="<?= $sowingCode ?>" readonly required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-danger text-uppercase mb-3">JENIS & NAMA BENIH</h6>
                        
                        <div class="form-group">
                            <label class="small text-muted">Kategori</label>
                            <select class="form-control bg-light shadow-none" disabled>
                                <option>Benih</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small text-muted">Arahkan Benih (Pilih dari Master Data)</label>
                            <select name="seed_item_id" id="seed_item_id" class="form-control select2" required>
                                <option value="">-- Pilih Jenis Benih --</option>
                                <?php foreach ($seeds as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-unit="<?= $s['unit'] ?>" data-stock="<?= $s['stock'] ?>">
                                        <?= $s['name'] ?> (Stok: <?= number_format($s['stock'], 2) ?> <?= $s['unit'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row form-group">
                            <div class="col-8">
                                <label class="small text-muted">Jumlah Benih (Digunakan)</label>
                                <input type="number" step="0.01" name="seed_quantity" id="seed_quantity" class="form-control font-weight-bold text-danger" required>
                            </div>
                            <div class="col-4">
                                <label class="small text-muted">Satuan</label>
                                <input type="text" id="seed_unit_display" class="form-control bg-light text-center shadow-none" readonly>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="small text-muted">Mandor</label>
                            <input type="text" name="mandor" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label class="small text-muted">Pelaksana / Manager</label>
                            <input type="text" name="manager" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label class="small text-muted">Keterangan</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <a href="<?= url('seedling-admin') ?>" class="btn btn-light border px-4 py-2 font-weight-bold shadow-sm mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Polybag PB -->
<div class="modal fade" id="polybagModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-primary"><i class="fas fa-fill-drip mr-2"></i> Pilih Polybag Isi Media Tanam (PB)</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="modalPolybagTable">
                        <thead class="bg-primary text-white small">
                            <tr>
                                <th>Kode PB</th>
                                <th>Tanggal</th>
                                <th>Jenis Polybag</th>
                                <th class="text-right">Sisa Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small text-dark" id="modalPolybagBody">
                            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Material BB -->
<div class="modal fade" id="materialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-warning"><i class="fas fa-cubes mr-2"></i> Pilih Bahan Baku Pendukung</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="modalMaterialTable">
                        <thead class="bg-warning text-dark small font-weight-bold">
                            <tr>
                                <th>Nama Bahan Baku</th>
                                <th>Kategori</th>
                                <th class="text-right">Sisa Stok</th>
                                <th>Satuan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small text-dark" id="modalMaterialBody">
                            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-top-primary { border-top: 4px solid var(--primary-color) !important; }
    .border-warning { border-color: #f6c23e !important; }
    
    .table-sm th, .table-sm td {
        vertical-align: middle;
    }
    .qty-input {
        width: 100px;
        text-align: right;
        font-weight: bold;
    }
    .remove-btn {
        cursor: pointer;
        color: #e74a3b;
    }
    .remove-btn:hover {
        color: #be2617;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------------------------------------------------------
    // SEED LOGIC
    // ---------------------------------------------------------
    const seedSelect = $('#seed_item_id');
    const seedQtyInput = document.getElementById('seed_quantity');
    const seedUnitDisplay = document.getElementById('seed_unit_display');
    let maxSeedStock = 0;

    seedSelect.on('change', function() {
        const option = $(this).find('option:selected');
        if (option.val()) {
            seedUnitDisplay.value = option.data('unit');
            maxSeedStock = parseFloat(option.data('stock'));
            seedQtyInput.max = maxSeedStock;
            
            // if current input is higher than stock, reset it
            if (parseFloat(seedQtyInput.value) > maxSeedStock) {
                seedQtyInput.value = maxSeedStock;
                alert('Stok '+option.text()+' tidak mencukupi (Max: '+maxSeedStock+')');
            }
        } else {
            seedUnitDisplay.value = '';
            maxSeedStock = 0;
            seedQtyInput.max = "";
        }
    });

    seedQtyInput.addEventListener('input', function() {
        if (this.value && parseFloat(this.value) > maxSeedStock && seedSelect.val()) {
            alert('Stok Benih tidak mencukupi! Sisa stok hanya: ' + maxSeedStock);
            this.value = maxSeedStock;
        }
    });

    // ---------------------------------------------------------
    // POLYBAG LOGIC (Left Modal 1)
    // ---------------------------------------------------------
    let polybagData = [];
    const polybagIdsSelected = new Set();
    const polybagTbody = document.querySelector('#polybagTable tbody');
    const emptyPolyRow = document.getElementById('emptyPolybagRow');

    window.openPolybagModal = function() {
        $('#polybagModal').modal('show');
        const modalBody = document.getElementById('modalPolybagBody');
        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>';

        fetch('<?= url('seedling-admin/get-filled-bags-stock-ajax') ?>')
            .then(res => res.json())
            .then(data => {
                modalBody.innerHTML = '';
                if(data.success && data.data.length > 0) {
                    polybagData = data.data;
                    data.data.forEach(pb => {
                        if(polybagIdsSelected.has(pb.id.toString())) return; // skip selected

                        modalBody.innerHTML += `
                            <tr>
                                <td class="font-weight-bold text-info">${pb.filling_code}</td>
                                <td>${new Date(pb.filling_date).toLocaleDateString('id-ID')}</td>
                                <td>${pb.bag_name}</td>
                                <td class="text-right font-weight-bold text-primary">${parseFloat(pb.remaining_stock).toLocaleString('id-ID')}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPolybag(${pb.id})">
                                        <i class="fas fa-plus"></i> Pilih
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    if (modalBody.innerHTML === '') {
                        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Semua PB yang tersedia sudah dipilih</td></tr>';
                    }
                } else {
                    modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Tidak ada stok PB yang tersedia. Silakan lakukan Pengisian Kantong terlebih dahulu.</td></tr>';
                }
            });
    };

    window.addPolybag = function(id) {
        const pb = polybagData.find(p => p.id == id);
        if(!pb) return;

        polybagIdsSelected.add(id.toString());
        $('#polybagModal').modal('hide');

        if(emptyPolyRow) emptyPolyRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = `polyRow-${pb.id}`;
        tr.innerHTML = `
            <td class="font-weight-bold text-info border-right">
                ${pb.filling_code}
                <input type="hidden" name="bag_filling_id[]" value="${pb.id}">
            </td>
            <td>${pb.bag_name}</td>
            <td class="p-1">
                <input type="number" step="1" name="bag_qty[]" class="form-control form-control-sm qty-input mx-auto" 
                       max="${pb.remaining_stock}" placeholder="Max ${pb.remaining_stock}" required onchange="validateRowStock(this, ${pb.remaining_stock}, 'PB')">
                <div class="text-center text-xs text-muted mt-1">Stok: ${parseFloat(pb.remaining_stock).toLocaleString('id-ID')}</div>
            </td>
            <td class="text-center">${pb.bag_unit}</td>
            <td class="text-center">
                <i class="fas fa-times remove-btn bg-light border rounded p-1" onclick="removePolybagRow(${pb.id})"></i>
            </td>
        `;
        polybagTbody.appendChild(tr);
    };

    window.removePolybagRow = function(id) {
        polybagIdsSelected.delete(id.toString());
        document.getElementById(`polyRow-${id}`).remove();
        if(polybagIdsSelected.size === 0 && emptyPolyRow) emptyPolyRow.style.display = 'table-row';
    };


    // ---------------------------------------------------------
    // MATERIAL LOGIC (Left Modal 2)
    // ---------------------------------------------------------
    let materialData = [];
    const materialIdsSelected = new Set();
    const materialTbody = document.querySelector('#materialTable tbody');
    const emptyMatRow = document.getElementById('emptyMaterialRow');

    window.openMaterialModal = function() {
        $('#materialModal').modal('show');
        const modalBody = document.getElementById('modalMaterialBody');
        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>';

        fetch('<?= url('seedling-admin/get-materials-stock-ajax') ?>')
            .then(res => res.json())
            .then(data => {
                modalBody.innerHTML = '';
                if(data.success && data.data.length > 0) {
                    materialData = data.data;
                    data.data.forEach(mat => {
                        if(materialIdsSelected.has(mat.id.toString())) return; 

                        modalBody.innerHTML += `
                            <tr>
                                <td class="font-weight-bold">${mat.name}</td>
                                <td>${mat.category}</td>
                                <td class="text-right font-weight-bold text-warning">${parseFloat(mat.current_stock).toLocaleString('id-ID')}</td>
                                <td>${mat.unit}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="addMaterial(${mat.id})">
                                        <i class="fas fa-plus"></i> Pilih
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    if (modalBody.innerHTML === '') {
                        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Semua bahan pendukung sudah dipilih</td></tr>';
                    }
                } else {
                    modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada stok bahan baku pendukung.</td></tr>';
                }
            });
    };

    window.addMaterial = function(id) {
        const mat = materialData.find(m => m.id == id);
        if(!mat) return;

        materialIdsSelected.add(id.toString());
        $('#materialModal').modal('hide');

        if(emptyMatRow) emptyMatRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = `matRow-${mat.id}`;
        tr.innerHTML = `
            <td class="font-weight-bold border-right">
                ${mat.name}
                <input type="hidden" name="material_item_id[]" value="${mat.id}">
            </td>
            <td class="p-1">
                <input type="number" step="0.01" name="material_qty[]" class="form-control form-control-sm qty-input mx-auto" 
                       max="${mat.current_stock}" placeholder="Max ${mat.current_stock}" required onchange="validateRowStock(this, ${mat.current_stock}, 'Bahan')">
                <div class="text-center text-xs text-muted mt-1">Stok: ${parseFloat(mat.current_stock).toLocaleString('id-ID')}</div>
            </td>
            <td class="text-center">${mat.unit}</td>
            <td class="text-center">
                <i class="fas fa-times remove-btn bg-light border rounded p-1" onclick="removeMaterialRow(${mat.id})"></i>
            </td>
        `;
        materialTbody.appendChild(tr);
    };

    window.removeMaterialRow = function(id) {
        materialIdsSelected.delete(id.toString());
        document.getElementById(`matRow-${id}`).remove();
        if(materialIdsSelected.size === 0 && emptyMatRow) emptyMatRow.style.display = 'table-row';
    };

    // Generic row validation
    window.validateRowStock = function(input, maxVal, label) {
        if(parseFloat(input.value) > maxVal) {
            alert('Stok tidak mencukupi! Maksimal ' + label + ' yang bisa dipakai: ' + maxVal);
            input.value = maxVal;
        }
    };

    // Form Submission Validation
    document.getElementById('sowingForm').addEventListener('submit', function(e) {
        if (polybagIdsSelected.size === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 Polybag Isi Media Tanam (PB) dari tabel kiri atas.');
        }
    });

});
</script>
