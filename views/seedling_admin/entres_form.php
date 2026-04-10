<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">ENTRES (ET)</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan Bibit</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penggunaan Entres</li>
            </ol>
        </nav>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('seedling-admin/store-entres') ?>" method="POST" id="entresForm">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="row">
            <!-- LEFT COLUMN: Materials (Dynamic Tables) -->
            <div class="col-lg-7">
                <!-- Bahan Baku Pendukung Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom border-warning">
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-sm btn-light font-weight-bold" onclick="openMaterialModal()">Tambah</button>
                        </div>
                        <h6 class="m-0 font-weight-bold text-warning text-uppercase">BAHAN BAKU ENTRES</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="materialTable">
                                <thead class="bg-light small text-dark font-weight-bold">
                                    <tr>
                                        <th width="20%">Kategori</th>
                                        <th width="40%">Nama Bahan Baku</th>
                                        <th width="20%" class="text-center">Jumlah</th>
                                        <th width="20%" class="text-center">Satuan</th>
                                        <th width="40px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyMaterialRow">
                                        <td colspan="5" class="text-center text-muted py-3">Klik tombol Tambah untuk memilih bahan baku entres</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Harvest Info & Metadata -->
            <div class="col-lg-5">
                <div class="card shadow mb-4 border-top-primary">
                    <div class="card-body">
                        
                        <div class="row form-group">
                            <div class="col-6">
                                <label class="small font-weight-bold text-info">Kode Produksi</label>
                                <input type="text" name="entres_code" class="form-control bg-light font-weight-bold shadow-none" value="<?= $entresCode ?>" readonly required>
                            </div>
                            <div class="col-6">
                                <label class="small font-weight-bold text-info">Tanggal</label>
                                <input type="date" name="entres_date" class="form-control font-weight-bold" value="<?= $today ?>" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-success text-uppercase mb-3">BIBIT PENYAPIHAN (PE)</h6>
                        
                        <div class="input-group mb-3 shadow-sm border-left border-success border-width-3">
                            <input type="text" id="display_weaning_name" class="form-control bg-white font-weight-bold text-success" placeholder="Pilih bibit hasil penyapihan (PE)..." readonly required>
                            <div class="input-group-append">
                                <button class="btn btn-success border px-4 font-weight-bold" type="button" onclick="openWeaningModal()"><i class="fas fa-caret-down"></i></button>
                            </div>
                        </div>
                        
                        <input type="hidden" id="weaning_id" name="weaning_id" required>

                        <div class="row mb-3 pl-3 ml-1 border-left border-warning">
                            <div class="col-6 mt-2">
                                <label class="small text-info mb-1">Jumlah Pemakaian</label>
                                <input type="number" step="1" name="used_quantity" id="used_quantity" class="form-control form-control-sm font-weight-bold text-primary border-primary border" placeholder="0" required>
                                <small class="text-muted" style="font-size: 0.6rem;">Jumlah batang yang di-okulasi</small>
                            </div>
                            <div class="col-6 mt-2">
                                <label class="small text-info mb-1">Tersedia (Stok PE)</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" id="max_weaning_stock" class="form-control form-control-sm font-weight-bold text-muted bg-light" readonly>
                                    <span class="ml-2 small text-muted" id="display_weaning_unit">pcs</span>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <label class="small text-info mb-1"><i class="fas fa-map-marker-alt text-warning mr-1"></i>Lokasi Asal Bibit</label>
                                <input type="text" id="display_weaning_location" class="form-control form-control-sm bg-light font-weight-bold" readonly placeholder="(pilih bibit untuk melihat lokasi)">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="small text-muted">Mandor</label>
                            <input type="text" name="mandor" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="small text-muted">Pelaksana / Manager</label>
                            <input type="text" name="manager" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="small text-muted">Lokasi</label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Blok 2, Bedeng A">
                        </div>
                        
                        <div class="form-group">
                            <label class="small text-muted">Keterangan</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="1"></textarea>
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

<!-- Modal Pencarian Bibit PE -->
<div class="modal fade" id="weaningModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-success"><i class="fas fa-search mr-2"></i> Pilih Bibit Penyapihan (PE) &nbsp;<span class="badge badge-success shadow-sm">Stok Tersedia</span></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-white border-bottom">
                    <input type="text" id="searchWeaning" class="form-control form-control-lg border-success shadow-sm" placeholder="Ketik jenis bibit atau kode produksi PE...">
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="modalWeaningTable">
                        <thead class="bg-light sticky-top shadow-sm">
                            <tr>
                                <th>Kode Produksi (PE)</th>
                                <th>Jenis Bibit</th>
                                <th>Tgl Produksi</th>
                                <th>Lokasi</th>
                                <th class="text-right">Awal</th>
                                <th class="text-right text-success">Sisa Stok</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modalWeaningBody">
                            <!-- Data populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pencarian Bahan Baku Entres -->
<div class="modal fade" id="materialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-warning"><i class="fas fa-search mr-2"></i> Pilih Bahan Baku Entres &nbsp;<span class="badge badge-warning shadow-sm">Stok Tersedia</span></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-white border-bottom">
                    <input type="text" id="searchMaterial" class="form-control form-control-lg border-warning shadow-sm" placeholder="Cari nama bahan baku...">
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="modalMaterialTable">
                        <thead class="bg-light sticky-top shadow-sm">
                            <tr>
                                <th>Nama Bahan Baku</th>
                                <th>Kategori</th>
                                <th class="text-right">Stok</th>
                                <th>Satuan</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modalMaterialBody">
                            <!-- Data populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    function validateRowStock(input, maxStock, label) {
        if (parseFloat(input.value) > maxStock) {
            alert(`Stok ${label} tidak mencukupi!\nMaksimal: ${maxStock}`);
            input.value = maxStock;
        } else if (parseFloat(input.value) <= 0) {
            alert(`Jumlah penggunaan tidak valid!`);
            input.value = '';
        }
    }
    window.validateRowStock = validateRowStock;

    // ---------------------------------------------------------
    // WEANING LOGIC (Right Column)
    // ---------------------------------------------------------
    let weaningData = [];
    let maxWeaningStock = 0;
    const usedQtyInput = document.getElementById('used_quantity');

    window.openWeaningModal = function() {
        $('#weaningModal').modal('show');
        const modalBody = document.getElementById('modalWeaningBody');
        modalBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data bibit PE...</td></tr>';
        
        fetch('<?= url('seedling-admin/get-weanings-ajax') ?>')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    weaningData = data.data;
                    renderWeaningTable(weaningData);
                } else {
                    modalBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data.</td></tr>';
                }
            });
    };

    function renderWeaningTable(data) {
        const modalBody = document.getElementById('modalWeaningBody');
        modalBody.innerHTML = '';
        
        if(data.length === 0) {
            modalBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada stok bibit penyapihan (PE) yang tersedia.</td></tr>';
            return;
        }

        data.forEach(w => {
            const lokasi = w.location ? `<span class="badge badge-secondary">${w.location}</span>` : '<span class="text-muted">-</span>';
            const totalInitial = parseFloat(w.total_initial).toLocaleString('id-ID');
            const totalRemaining = parseFloat(w.remaining_stock).toLocaleString('id-ID');
            modalBody.innerHTML += `
                <tr>
                    <td class="font-weight-bold text-success">${w.weaning_code}</td>
                    <td class="font-weight-bold">${w.seed_name}</td>
                    <td>${new Date(w.weaning_date).toLocaleDateString('id-ID')}</td>
                    <td>${lokasi}</td>
                    <td class="text-right text-muted">${totalInitial}</td>
                    <td class="text-right font-weight-bold text-success">${totalRemaining}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-success shadow-sm" onclick="selectWeaning(${w.id})">
                            Pilih
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    // Modal Search Filter
    document.getElementById('searchWeaning').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        const filtered = weaningData.filter(w => 
            w.seed_name.toLowerCase().includes(q) || 
            w.weaning_code.toLowerCase().includes(q)
        );
        renderWeaningTable(filtered);
    });

    window.selectWeaning = function(id) {
        const w = weaningData.find(item => item.id == id);
        if(!w) return;

        // Set values
        document.getElementById('weaning_id').value = id;
        document.getElementById('display_weaning_name').value = `${w.weaning_code} - ${w.seed_name}`;
        
        maxWeaningStock = parseFloat(w.remaining_stock);
        document.getElementById('max_weaning_stock').value = maxWeaningStock;
        document.getElementById('display_weaning_location').value = w.location || '-';

        // Set validation
        usedQtyInput.max = maxWeaningStock;
        usedQtyInput.value = '';

        $('#weaningModal').modal('hide');
    };

    // Validation for used quantity
    usedQtyInput.addEventListener('change', function() {
        validateRowStock(this, maxWeaningStock, 'Bibit PE');
    });

    // ---------------------------------------------------------
    // MATERIAL LOGIC (Left Modal)
    // ---------------------------------------------------------
    let materialData = [];
    const materialIdsSelected = new Set();
    const materialTbody = document.querySelector('#materialTable tbody');
    const emptyMatRow = document.getElementById('emptyMaterialRow');

    window.openMaterialModal = function() {
        $('#materialModal').modal('show');
        const modalBody = document.getElementById('modalMaterialBody');
        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>';

        fetch('<?= url('seedling-admin/get-entres-materials-ajax') ?>')
            .then(res => res.json())
            .then(data => {
                if(data.success && data.data.length > 0) {
                    materialData = data.data;
                    renderMaterialTable(materialData);
                } else {
                    modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada bahan baku ber-kategori "ENTRESS". Silakan tambahkan di menu Tambah Barang -> Master Data terlebih dahulu.</td></tr>';
                }
            });
    };

    function renderMaterialTable(data) {
        const modalBody = document.getElementById('modalMaterialBody');
        modalBody.innerHTML = '';
        let count = 0;

        data.forEach(mat => {
            if(materialIdsSelected.has(mat.id.toString())) return; 
            count++;

            modalBody.innerHTML += `
                <tr>
                    <td class="font-weight-bold">${mat.name}</td>
                    <td>${mat.category}</td>
                    <td class="text-right font-weight-bold text-warning">${parseFloat(mat.current_stock).toLocaleString('id-ID')}</td>
                    <td>${mat.unit}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="addMaterial(${mat.id})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        if (count === 0) {
            modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Semua bahan entres sudah dipilih ke dalam form</td></tr>';
        }
    }

    document.getElementById('searchMaterial').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        const filtered = materialData.filter(mat => 
            mat.name.toLowerCase().includes(q)
        );
        renderMaterialTable(filtered);
    });

    window.addMaterial = function(id) {
        const mat = materialData.find(m => m.id == id);
        if(!mat) return;

        materialIdsSelected.add(id.toString());
        $('#materialModal').modal('hide');

        if(emptyMatRow) emptyMatRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = `matRow-${mat.id}`;
        tr.innerHTML = `
            <td>${mat.category}</td>
            <td class="font-weight-bold border-right">
                ${mat.name}
                <input type="hidden" name="mat_id[]" value="${mat.id}">
            </td>
            <td class="p-1">
                <input type="number" step="0.01" name="mat_qty[]" class="form-control form-control-sm qty-input mx-auto" 
                       max="${mat.current_stock}" placeholder="Max ${mat.current_stock}" required onchange="validateRowStock(this, ${mat.current_stock}, 'Bahan')">
                <div class="text-center text-xs text-muted mt-1">Stok: ${parseFloat(mat.current_stock).toLocaleString('id-ID')}</div>
            </td>
            <td class="text-center">${mat.unit}</td>
            <td class="text-center">
                <i class="fas fa-times remove-btn bg-light border rounded p-1" style="cursor: pointer;" onclick="removeMaterialRow(${mat.id})"></i>
            </td>
        `;
        materialTbody.appendChild(tr);
    };

    window.removeMaterialRow = function(id) {
        materialIdsSelected.delete(id.toString());
        document.getElementById(`matRow-${id}`).remove();
        if(materialIdsSelected.size === 0 && emptyMatRow) emptyMatRow.style.display = 'table-row';
    };
});
</script>
