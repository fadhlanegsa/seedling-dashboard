<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">MUTASI OUT BIBIT SETENGAH JADI (BO)</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan Bibit</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mutasi Keluar</li>
            </ol>
        </nav>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('seedling-admin/store-mutation') ?>" method="POST" id="mutationForm">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-top-primary">
                    <div class="card-body p-4">
                        
                        <!-- Header Info -->
                        <div class="row form-group mb-4">
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-primary">Kode Mutasi (BO)</label>
                                <input type="text" name="mutation_code" class="form-control bg-light font-weight-bold shadow-none" value="<?= $mutationCode ?>" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-primary">Tanggal Mutasi</label>
                                <input type="date" name="mutation_date" class="form-control font-weight-bold" value="<?= $today ?>" required>
                            </div>
                        </div>

                        <hr>

                        <!-- Source Type Selection -->
                        <div class="form-group mb-4 text-center">
                            <label class="d-block small font-weight-bold text-dark text-uppercase mb-3">Pilih Sumber Bibit Asal</label>
                            <div class="btn-group btn-group-toggle shadow-sm w-100" data-toggle="buttons">
                                <label class="btn btn-outline-success py-3 font-weight-bold active" onclick="setSourceType('PE')">
                                    <input type="radio" name="source_type" value="PE" checked> <i class="fas fa-seedling mr-2"></i> HASIL PENYAPIHAN (PE)
                                </label>
                                <label class="btn btn-outline-info py-3 font-weight-bold" onclick="setSourceType('ET')">
                                    <input type="radio" name="source_type" value="ET"> <i class="fas fa-cut mr-2"></i> ENTRESS (ET)
                                </label>
                            </div>
                        </div>

                        <!-- Source Selection Area -->
                        <div class="bg-light p-4 rounded border mb-4 shadow-sm">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-danger text-uppercase">Pilih Batch Bibit (Anakan)</label>
                                <div class="input-group">
                                    <input type="text" id="display_source_name" class="form-control border-danger font-weight-bold" placeholder="Klik tombol cari untuk memilih batch bibit..." readonly required>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger px-4" type="button" onclick="openSourceModal()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="source_id" name="source_id" required>
                                <input type="hidden" id="origin_location" name="origin_location">
                            </div>
                            
                            <!-- Origin Location Info -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="small text-muted mb-0">Lokasi Asal Aktif</label>
                                    <div id="origin_location_label" class="font-weight-bold text-dark">-</div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <label class="small text-muted mb-0">Sisa Stok Terakhir</label>
                                    <div class="h5 font-weight-bold text-primary mb-0"><span id="display_max_stock">0</span> <small>pcs</small></div>
                                </div>
                            </div>
                        </div>

                        <!-- Mutation Logic Area -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Jumlah Mutasi</label>
                                    <input type="number" step="1" name="quantity" id="quantity" class="form-control form-control-lg border-primary font-weight-bold text-primary" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Keterangan Mutasi</label>
                                    <select name="mutation_type" id="mutation_type" class="form-control form-control-lg font-weight-bold border-warning text-warning shadow-none" required>
                                        <option value="MATI" class="text-danger">MATI</option>
                                        <option value="NAIK KELAS" class="text-success">NAIK KELAS (BIBIT JADI)</option>
                                        <option value="TRANSFER" class="text-info">TRANSFER (PINDAH AREA)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 shadow-none">
                            <label class="small font-weight-bold text-muted" id="location_label">Lokasi Tujuan / Lokasi Akhir</label>
                            <input type="text" name="target_location" id="target_location" class="form-control" placeholder="Contoh: OGA, Blok C, AHA 2, atau Distribusi Masyarakat">
                            <small id="location_hint" class="text-muted"></small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-muted">Mandor</label>
                                    <input type="text" name="mandor" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-muted">Pelaksana / Manager</label>
                                    <input type="text" name="manager" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted">Notes (Catatan Tambahan)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Masukan alasan mutasi jika diperlukan..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <a href="<?= url('seedling-admin') ?>" class="btn btn-light border px-5 py-2 font-weight-bold shadow-sm mr-2 text-muted">Batal</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm">Simpan Transaksi</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Pencarian Sumber Bibit (PE/ET) -->
<div class="modal fade" id="sourceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-primary"><i class="fas fa-search mr-2"></i> Pilih Batch Bibit &nbsp;<span id="modal_type_label" class="badge badge-success shadow-sm">PE</span></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-white border-bottom">
                    <input type="text" id="searchSource" class="form-control form-control-lg border-primary shadow-sm" placeholder="Cari kode produksi atau nama bibit...">
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="modalSourceTable">
                        <thead class="bg-light sticky-top shadow-sm">
                            <tr>
                                <th>Kode Batch</th>
                                <th>Jenis Bibit</th>
                                <th>Lokasi Aktif</th>
                                <th class="text-right">Sisa Stok</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modalSourceBody">
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
    let currentSourceType = 'PE';
    let sourceData = [];
    let maxStockActive = 0;

    const qtyInput = document.getElementById('quantity');
    const sourceIdInput = document.getElementById('source_id');
    const displaySourceName = document.getElementById('display_source_name');
    const displayMaxStock = document.getElementById('display_max_stock');
    const originLocationLabel = document.getElementById('origin_location_label');

    window.setSourceType = function(type) {
        if(currentSourceType === type) return;
        currentSourceType = type;
        
        // Reset selections
        sourceIdInput.value = '';
        displaySourceName.value = '';
        displayMaxStock.innerText = '0';
        originLocationLabel.innerText = '-';
        qtyInput.value = '';
        qtyInput.max = 0;

        // Modal UX
        document.getElementById('modal_type_label').innerText = type;
        document.getElementById('modal_type_label').className = (type === 'PE') ? 'badge badge-success shadow-sm' : 'badge badge-info shadow-sm';
    };

    window.openSourceModal = function() {
        $('#sourceModal').modal('show');
        const modalBody = document.getElementById('modalSourceBody');
        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data ' + currentSourceType + '...</td></tr>';
        
        fetch(`<?= url('seedling-admin/get-mutation-sources-ajax') ?>?type=${currentSourceType}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    sourceData = data.data;
                    renderSourceTable(sourceData);
                } else {
                    modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data.</td></tr>';
                }
            });
    };

    function renderSourceTable(data) {
        const modalBody = document.getElementById('modalSourceBody');
        modalBody.innerHTML = '';
        
        if(data.length === 0) {
            modalBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada stok ${currentSourceType} yang tersedia.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const code = (currentSourceType === 'PE') ? item.weaning_code : item.entres_code;
            const stock = parseFloat(item.remaining_stock);
            
            modalBody.innerHTML += `
                <tr>
                    <td class="font-weight-bold">${code}</td>
                    <td>${item.seed_name}</td>
                    <td><span class="badge badge-light border">${item.location || '-'}</span></td>
                    <td class="text-right font-weight-bold text-primary">${stock.toLocaleString('id-ID')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="selectSource(${item.id})"> Pilih </button>
                    </td>
                </tr>
            `;
        });
    }

    window.selectSource = function(id) {
        const item = sourceData.find(x => x.id == id);
        if(!item) return;

        const code = (currentSourceType === 'PE') ? item.weaning_code : item.entres_code;
        maxStockActive = parseFloat(item.remaining_stock);

        sourceIdInput.value = item.id;
        displaySourceName.value = `${code} - ${item.seed_name}`;
        displayMaxStock.innerText = maxStockActive.toLocaleString('id-ID');
        originLocationLabel.innerText = item.location || '-';
        document.getElementById('origin_location').value = item.location || '-';
        
        qtyInput.max = maxStockActive;
        qtyInput.value = '';

        $('#sourceModal').modal('hide');
    };

    // Dynamic location label based on mutation type
    const mutationType = document.getElementById('mutation_type');
    const locationLabel = document.getElementById('location_label');
    const locationHint = document.getElementById('location_hint');
    const locationInput = document.getElementById('target_location');

    function updateLocationLabel() {
        const val = mutationType.value;
        if (val === 'MATI') {
            locationLabel.textContent = 'Lokasi Kejadian (Opsional)';
            locationInput.placeholder = 'Contoh: GHA Blok A, Bedeng No. 3';
            locationHint.textContent = 'Catat di area mana kematian bibit ini terjadi.';
        } else if (val === 'NAIK KELAS') {
            locationLabel.textContent = 'Tujuan / Keterangan Kelulusan';
            locationInput.placeholder = 'Contoh: Siap Distribusi, OGA Utara';
            locationHint.textContent = 'Bibit ini akan otomatis masuk ke Stok Bibit Jadi (Reguler).';
        } else if (val === 'TRANSFER') {
            locationLabel.textContent = 'Lokasi Tujuan Transfer (Wajib)';
            locationInput.placeholder = 'Contoh: AHA 2, OGA Blok C';
            locationHint.textContent = 'Bibit masih dihitung di stok. Hanya lokasi yang berubah.';
        }
    }

    mutationType.addEventListener('change', updateLocationLabel);
    updateLocationLabel(); // Run on page load

    qtyInput.addEventListener('change', function() {
        if (parseFloat(this.value) > maxStockActive) {
            alert(`Stok tidak mencukupi!\nMaksimal tersedia: ${maxStockActive}`);
            this.value = maxStockActive;
        }
    });

    // Modal Search
    document.getElementById('searchSource').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        const filtered = sourceData.filter(x => {
            const code = (currentSourceType === 'PE') ? x.weaning_code : x.entres_code;
            return code.toLowerCase().includes(q) || x.seed_name.toLowerCase().includes(q);
        });
        renderSourceTable(filtered);
    });
});
</script>
