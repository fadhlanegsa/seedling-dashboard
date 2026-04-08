<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">PEMANENAN SEMAI</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan Bibit</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pemanenan Semai</li>
                </ol>
            </nav>
        </div>

        <?php if ($flash = $this->getFlash()): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
                <?= $flash['message'] ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="card shadow border-top-primary">
            <div class="card-body p-5">
                <form action="<?= url('seedling-admin/store-harvesting') ?>" method="POST" id="harvestForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <!-- Header Tanggal & Kode -->
                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-md-6 d-flex align-items-center">
                            <label class="text-info font-weight-bold mr-3 mb-0" style="min-width: 70px;">Tanggal</label>
                            <input type="date" name="harvest_date" class="form-control" value="<?= $today ?>" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-3 mt-md-0">
                            <label class="text-info font-weight-bold mr-3 mb-0" style="min-width: 60px;">Kode</label>
                            <input type="text" name="harvest_code" class="form-control bg-light font-weight-bold" value="<?= $harvestCode ?>" readonly required>
                        </div>
                    </div>

                    <!-- Pilihan Semai -->
                    <h6 class="font-weight-bold text-danger mb-3">SEMAI</h6>
                    
                    <div class="input-group mb-3 shadow-sm">
                        <input type="text" id="display_seed_name" class="form-control bg-warning text-dark font-weight-bold font-italic" placeholder="Pilih bibit yang sudah disemai..." readonly required style="background-color: #fff9c4 !important;">
                        <div class="input-group-append">
                            <button class="btn btn-secondary border px-4 font-weight-bold" type="button" onclick="openSowingModal()">Pilih</button>
                        </div>
                    </div>
                    
                    <input type="hidden" id="sowing_id" name="sowing_id" required>

                    <!-- Informasi Meta Semai -->
                    <div class="row mb-5 pl-4 ml-2 border-left border-warning">
                        <div class="col-12 mb-2 d-flex align-items-center">
                            <label class="text-muted small mr-3 mb-0" style="min-width: 120px;">Tanggal Tabur</label>
                            <input type="text" id="display_sowing_date" class="form-control form-control-sm bg-light w-50" readonly>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <label class="text-muted small mr-3 mb-0" style="min-width: 120px;">Kode Produksi</label>
                            <input type="text" id="display_sowing_code" class="form-control form-control-sm bg-light w-50" readonly>
                        </div>
                    </div>

                    <!-- Input Hasil -->
                    <div class="form-group row align-items-center mb-5">
                        <label class="col-sm-6 col-form-label font-weight-bold text-primary text-md-right h6 mb-0 text-uppercase">Jumlah Anakan Yang Dihasilkan</label>
                        <div class="col-sm-6">
                            <input type="number" step="1" name="harvested_quantity" class="form-control font-weight-bold text-lg" required>
                        </div>
                    </div>

                    <hr>

                    <!-- Metadata -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-muted small">Mandor</label>
                        <div class="col-sm-9">
                            <input type="text" name="mandor" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-muted small">Pelaksana/Manager</label>
                        <div class="col-sm-9">
                            <input type="text" name="manager" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-muted small">Keterangan</label>
                        <div class="col-sm-9">
                            <input type="text" name="notes" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <a href="<?= url('seedling-admin') ?>" class="btn btn-light border px-4 py-2 font-weight-bold shadow-sm mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pencarian Semai -->
<div class="modal fade" id="sowingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h6 class="modal-title font-weight-bold text-primary"><i class="fas fa-search mr-2"></i> Pilih Benih Yang Akan Dipanen</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="modalSowingTable">
                        <thead class="bg-primary text-white small" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Nama Benih</th>
                                <th>Tanggal Tabur</th>
                                <th>Kode Produksi (PC)</th>
                            </tr>
                        </thead>
                        <tbody class="small text-dark" id="modalSowingBody">
                            <tr><td colspan="3" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Optional search box inside modal -->
            <div class="modal-footer bg-light p-2 justify-content-start">
                <input type="text" id="sowingSearch" class="form-control form-control-sm w-50" placeholder="Cari benih...">
            </div>
        </div>
    </div>
</div>

<style>
    .border-top-primary { border-top: 5px solid var(--primary-color) !important; }
    #modalSowingTable tbody tr { cursor: pointer; }
    #modalSowingTable tbody tr:hover { background-color: #e3f2fd; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let sowingData = [];

    window.openSowingModal = function() {
        $('#sowingModal').modal('show');
        const modalBody = document.getElementById('modalSowingBody');
        modalBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat riwayat tabur...</td></tr>';

        fetch('<?= url('seedling-admin/get-sowings-ajax') ?>')
            .then(res => res.json())
            .then(data => {
                modalBody.innerHTML = '';
                if(data.success && data.data.length > 0) {
                    sowingData = data.data;
                    renderSowingTable(sowingData);
                } else {
                    modalBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Tidak ada data penaburan benih (PC) yang tersedia.</td></tr>';
                }
            });
    };

    function renderSowingTable(dataToRender) {
        const modalBody = document.getElementById('modalSowingBody');
        modalBody.innerHTML = '';
        dataToRender.forEach(sw => {
            const dateStr = new Date(sw.sowing_date).toLocaleDateString('id-ID');
            modalBody.innerHTML += `
                <tr ondblclick="selectSowing(${sw.id})">
                    <td class="font-weight-bold text-dark">${sw.seed_name}</td>
                    <td>${dateStr}</td>
                    <td class="text-muted">${sw.sowing_code}</td>
                </tr>
            `;
        });
    }

    // Modal Search Filter
    document.getElementById('sowingSearch').addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        const filtered = sowingData.filter(sw => 
            sw.seed_name.toLowerCase().includes(q) || 
            sw.sowing_code.toLowerCase().includes(q)
        );
        renderSowingTable(filtered);
    });

    window.selectSowing = function(id) {
        const sw = sowingData.find(s => s.id == id);
        if(!sw) return;

        // Set Values
        document.getElementById('sowing_id').value = id;
        document.getElementById('display_seed_name').value = sw.seed_name;
        document.getElementById('display_sowing_date').value = new Date(sw.sowing_date).toLocaleDateString('id-ID');
        document.getElementById('display_sowing_code').value = sw.sowing_code;

        $('#sowingModal').modal('hide');
    };
});
</script>
