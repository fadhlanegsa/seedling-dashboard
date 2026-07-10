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
                                        <button class="btn btn-danger px-4" type="button" onclick="openSourceModal()" title="Cari Batch">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button class="btn btn-info px-3 d-none" type="button" id="btn_traceability" onclick="openTraceabilityModal()" title="Lacak Asal-Usul (Traceability)">
                                            <i class="fas fa-info-circle"></i> Lacak
                                        </button>
                                        <button class="btn btn-secondary px-3 d-none" type="button" id="btn_qr" onclick="openQRModal()" title="Cetak QR Code">
                                            <i class="fas fa-qrcode"></i> QR
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
                                        <option value="NAIK KELAS (REGULER)" class="text-success">NAIK KELAS (REGULER)</option>
                                        <option value="NAIK KELAS (FOLU)" class="text-primary">NAIK KELAS (FOLU)</option>
                                        <option value="NAIK KELAS (RHL)" class="text-info">NAIK KELAS (RHL)</option>
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
</div>

<!-- Modal Traceability -->
<div class="modal fade" id="traceabilityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-bottom-0">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-project-diagram mr-2"></i> Lacak Asal-Usul Bibit (Traceability)</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body bg-light p-4" id="traceabilityBody">
                <!-- Content injected via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-bottom-0">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-qrcode mr-2 text-success"></i> Cetak QR Code</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body bg-light p-4">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded border" id="qrTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active py-2 font-weight-bold" id="single-tab" data-toggle="pill" href="#singleQR" role="tab" aria-selected="true">
                            <i class="fas fa-ticket-alt mr-1"></i> Satu Per Satu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 font-weight-bold" id="batch-tab" data-toggle="pill" href="#batchQR" role="tab" aria-selected="false" onclick="initBatchTabFields()">
                            <i class="fas fa-layer-group mr-1"></i> Cetak Massal (Range)
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="qrTabsContent">
                    <!-- Single QR Tab -->
                    <div class="tab-pane fade show active text-center" id="singleQR" role="tabpanel">
                        <div id="qrcode" class="d-inline-block bg-white p-2 rounded shadow-sm mb-3"></div>
                        
                        <!-- Premium Index Selector -->
                        <div class="form-group px-4 mb-3">
                            <label class="small font-weight-bold text-muted d-block text-center mb-2">Pilih Nomor Index (Nomor Bibit)</label>
                            <div class="input-group justify-content-center mx-auto" style="max-width: 180px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-sm px-3 font-weight-bold" type="button" onclick="decrementIndex()">-</button>
                                </div>
                                <input type="number" id="qr_index_input" class="form-control text-center font-weight-bold" value="1" min="1" step="1" onchange="updateSingleQR()" style="max-width: 70px;">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-sm px-3 font-weight-bold" type="button" onclick="incrementIndex()">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded border mb-3 shadow-xs">
                            <h6 class="font-weight-bold text-success mb-1" id="qr_batch_code" style="letter-spacing: 0.5px;">-</h6>
                            <p class="small text-muted mb-0 font-weight-bold" id="qr_seed_name">-</p>
                        </div>
                        
                        <button class="btn btn-success btn-block py-2 shadow-sm font-weight-bold" onclick="printThermalQR()"><i class="fas fa-print mr-2"></i> Print Stiker</button>
                    </div>

                    <!-- Batch QR Tab -->
                    <div class="tab-pane fade" id="batchQR" role="tabpanel">
                        <div class="bg-white p-3 rounded border mb-3 shadow-xs">
                            <h6 class="font-weight-bold text-primary mb-2"><i class="fas fa-info-circle mr-1"></i> Informasi Cetak Massal</h6>
                            <p class="small text-muted mb-2">Gunakan fitur ini untuk mencetak stiker QR code dalam jumlah banyak sekaligus sesuai range index bibit.</p>
                            <div class="border-top pt-2">
                                <span class="small text-muted font-weight-bold d-block">Nama Bibit: <span id="batch_qr_seed_name" class="text-dark font-weight-bold">-</span></span>
                                <span class="small text-muted font-weight-bold d-block">Total Kapasitas Batch: <span id="batch_qr_stock" class="text-primary font-weight-bold">-</span> pcs</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Dari Index</label>
                                    <input type="number" id="batch_start_index" class="form-control form-control-sm font-weight-bold" value="1" min="1" onchange="validateBatchRange()">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Sampai Index</label>
                                    <input type="number" id="batch_end_index" class="form-control form-control-sm font-weight-bold" value="1" min="1" onchange="validateBatchRange()">
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-block py-2 shadow-sm font-weight-bold" onclick="printBatchThermalQR()"><i class="fas fa-print mr-2"></i> Cetak Massal Stiker</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSourceType = 'PE';
    let sourceData = [];
    let maxStockActive = 0;
    let activeTotalInitial = 0;

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
        document.getElementById('btn_traceability').classList.add('d-none');
        document.getElementById('btn_qr').classList.add('d-none');

        // Modal UX
        document.getElementById('modal_type_label').innerText = type;
        document.getElementById('modal_type_label').className = (type === 'PE') ? 'badge badge-success shadow-sm' : 'badge badge-info shadow-sm';
    };

    window.openSourceModal = function() {
        $('#sourceModal').modal('show');
        const modalBody = document.getElementById('modalSourceBody');
        modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data ' + currentSourceType + '...</td></tr>';
        
        fetch(`<?= url('seedling-admin/get-mutation-sources-ajax') ?>?type=${currentSourceType}`)
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error('Network response was not ok: ' + text); });
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return res.json();
                } else {
                    return res.text().then(text => { throw new Error('Response is not JSON: ' + text.substring(0, 100)); });
                }
            })
            .then(data => {
                if(data.success) {
                    sourceData = data.data || [];
                    renderSourceTable(sourceData);
                } else {
                    modalBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data.</td></tr>';
                }
            })
            .catch(err => {
                console.error("Fetch Error:", err);
                modalBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">
                    <i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan sistem: ${err.message}
                </td></tr>`;
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
            const rawCode = (currentSourceType === 'PE') ? item.weaning_code : item.entres_code;
            const code = item.smart_barcode || rawCode;
            const stock = parseFloat(item.remaining_stock);
            
            modalBody.innerHTML += `
                <tr>
                    <td class="font-weight-bold text-primary">${code}</td>
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

    let activeSmartBarcode = '';

    window.selectSource = function(id) {
        const item = sourceData.find(x => x.id == id);
        if(!item) return;

        const rawCode = (currentSourceType === 'PE') ? item.weaning_code : item.entres_code;
        const code = item.smart_barcode || rawCode;
        maxStockActive = parseFloat(item.remaining_stock);
        activeTotalInitial = parseInt(item.total_initial) || parseInt(item.remaining_stock) || 1;
        activeSmartBarcode = item.smart_barcode || '';

        sourceIdInput.value = item.id;
        displaySourceName.value = `${code} - ${item.seed_name}`;
        displayMaxStock.innerText = maxStockActive.toLocaleString('id-ID');
        originLocationLabel.innerText = item.location || '-';
        document.getElementById('origin_location').value = item.location || '-';
        
        qtyInput.max = maxStockActive;
        qtyInput.value = '';

        document.getElementById('btn_traceability').classList.remove('d-none');
        document.getElementById('btn_qr').classList.remove('d-none');

        $('#sourceModal').modal('hide');
    };

    let qrcode = null;

    // Helper: update last index segment of a smart barcode string
    function getSmartBarcodeWithIndex(baseBarcode, index) {
        if (!baseBarcode) return '';
        const parts = baseBarcode.split('-');
        if (parts.length < 8) return baseBarcode; // Fallback if invalid format
        parts[7] = index;
        return parts.join('-');
    }

    window.openQRModal = function() {
        const sourceId = sourceIdInput.value;
        if (!sourceId) return;
        
        const fullTitle = displaySourceName.value;
        const parts = fullTitle.split(' - ');
        const code = parts[0];
        const name = parts.slice(1).join(' - ');

        // Reset Pills tab state to Single
        $('#qrTabs a[href="#singleQR"]').tab('show');

        // Reset single index inputs
        const indexInput = document.getElementById('qr_index_input');
        if (indexInput) {
            indexInput.value = 1;
        }

        const displayCode = activeSmartBarcode || code;
        document.getElementById('qr_batch_code').innerText = displayCode;
        document.getElementById('qr_seed_name').innerText = name;

        // Generate URL (dynamic base URL) pointing to public/trace/{smart_barcode}
        const traceUrl = activeSmartBarcode 
            ? `<?= url('public/trace') ?>/${activeSmartBarcode}`
            : `<?= url('public/trace') ?>/${currentSourceType}/${sourceId}`;

        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = ''; // Clear previous

        qrcode = new QRCode(qrContainer, {
            text: traceUrl,
            width: 150,
            height: 150,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        $('#qrModal').modal('show');
    };

    window.decrementIndex = function() {
        const input = document.getElementById('qr_index_input');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
            updateSingleQR();
        }
    };

    window.incrementIndex = function() {
        const input = document.getElementById('qr_index_input');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
        updateSingleQR();
    };

    window.updateSingleQR = function() {
        const input = document.getElementById('qr_index_input');
        let index = parseInt(input.value) || 1;
        if (index < 1) {
            index = 1;
            input.value = 1;
        }
        
        const sourceId = sourceIdInput.value;
        if (!sourceId) return;

        const fullTitle = displaySourceName.value;
        const parts = fullTitle.split(' - ');
        const code = parts[0];
        const name = parts.slice(1).join(' - ');

        const updatedBarcode = getSmartBarcodeWithIndex(activeSmartBarcode || code, index);
        document.getElementById('qr_batch_code').innerText = updatedBarcode;

        // Generate trace URL pointing to public/trace/{smart_barcode}
        const traceUrl = updatedBarcode 
            ? `<?= url('public/trace') ?>/${updatedBarcode}`
            : `<?= url('public/trace') ?>/${currentSourceType}/${sourceId}`;

        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = ''; // Clear previous

        qrcode = new QRCode(qrContainer, {
            text: traceUrl,
            width: 150,
            height: 150,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    };

    window.initBatchTabFields = function() {
        const fullTitle = displaySourceName.value;
        const parts = fullTitle.split(' - ');
        const name = parts.slice(1).join(' - ');

        document.getElementById('batch_qr_seed_name').innerText = name;
        document.getElementById('batch_qr_stock').innerText = activeTotalInitial.toLocaleString('id-ID');
        
        document.getElementById('batch_start_index').value = 1;
        document.getElementById('batch_end_index').value = activeTotalInitial;
    };

    window.validateBatchRange = function() {
        const startInput = document.getElementById('batch_start_index');
        const endInput = document.getElementById('batch_end_index');
        
        let start = parseInt(startInput.value) || 1;
        let end = parseInt(endInput.value) || 1;
        
        if (start < 1) {
            start = 1;
            startInput.value = 1;
        }
        if (end < start) {
            end = start;
            endInput.value = start;
        }
    };

    // Helper: generate QR Code base64 image data synchronously using clean offline canvas approach
    function getQRBase64Sync(text) {
        const tempDiv = document.createElement('div');
        const qr = new QRCode(tempDiv, {
            text: text,
            width: 150,
            height: 150,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
        const canvas = tempDiv.querySelector('canvas');
        let src = '';
        if (canvas) {
            src = canvas.toDataURL('image/png');
        }
        return src;
    }

    window.printThermalQR = function() {
        const sourceId = sourceIdInput.value;
        if (!sourceId) return;
        
        const fullTitle = displaySourceName.value;
        const parts = fullTitle.split(' - ');
        const name = parts.slice(1).join(' - ');
        const date = new Date().toLocaleDateString('id-ID');
        
        // Grab updated QR image from DOM
        const qrImg = document.querySelector('#qrcode img').src;
        // Grab updated barcode text from DOM
        const displayCode = document.getElementById('qr_batch_code').innerText;
        
        const printWindow = window.open('', '_blank', 'width=400,height=400');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print QR Code</title>
                <style>
                    @page { margin: 0; size: 58mm 40mm; }
                    body { 
                        margin: 0; 
                        padding: 2mm; 
                        width: 54mm; 
                        height: 36mm;
                        font-family: Arial, sans-serif; 
                        box-sizing: border-box;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                    }
                    .title { font-weight: bold; font-size: 11px; margin-bottom: 2px; }
                    .barcode-container { margin: 3px 0; }
                    .barcode-container img { height: 20mm; width: 20mm; object-fit: contain; }
                    .detail { font-size: 9px; line-height: 1.2; font-family: monospace; font-weight: bold; letter-spacing: 0.5px; }
                    .date { font-size: 7px; color: #333; margin-top: 2px; }
                </style>
            </head>
            <body>
                <div class="barcode-container">
                    <img src="${qrImg}" />
                </div>
                <div class="detail">${displayCode}</div>
                <div class="date">Tgl: ${date} | ${name.substring(0, 25)}</div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };

    window.printBatchThermalQR = function() {
        const sourceId = sourceIdInput.value;
        if (!sourceId) return;
        
        const fullTitle = displaySourceName.value;
        const parts = fullTitle.split(' - ');
        const code = parts[0];
        const name = parts.slice(1).join(' - ');
        const date = new Date().toLocaleDateString('id-ID');

        const start = parseInt(document.getElementById('batch_start_index').value) || 1;
        const end = parseInt(document.getElementById('batch_end_index').value) || 1;

        if (end < start) {
            alert('Range index tidak valid!');
            return;
        }

        const totalLabels = end - start + 1;
        if (totalLabels > 500) {
            if (!confirm(`Anda akan mencetak ${totalLabels} stiker sekaligus. Ini mungkin memerlukan waktu beberapa saat. Lanjutkan?`)) {
                return;
            }
        }

        // Generate list of items to print
        let labelsHtml = '';
        const baseBarcode = activeSmartBarcode || code;

        for (let i = start; i <= end; i++) {
            const updatedBarcode = getSmartBarcodeWithIndex(baseBarcode, i);
            const traceUrl = updatedBarcode 
                ? `<?= url('public/trace') ?>/${updatedBarcode}`
                : `<?= url('public/trace') ?>/${currentSourceType}/${sourceId}`;
            
            const qrImgSrc = getQRBase64Sync(traceUrl);

            labelsHtml += `
                <div class="label-page">
                     <div class="barcode-container">
                         <img src="${qrImgSrc}" />
                     </div>
                     <div class="detail">${updatedBarcode}</div>
                     <div class="date">Tgl: ${date} | ${name.substring(0, 25)}</div>
                </div>
            `;
        }

        const printWindow = window.open('', '_blank', 'width=400,height=500');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Batch QR Code</title>
                <style>
                    @page { margin: 0; size: 58mm 40mm; }
                    body { 
                        margin: 0; 
                        padding: 0;
                        font-family: Arial, sans-serif; 
                        box-sizing: border-box;
                    }
                    .label-page {
                        width: 58mm; 
                        height: 40mm;
                        padding: 2mm; 
                        box-sizing: border-box;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        page-break-after: always;
                    }
                    .title { font-weight: bold; font-size: 11px; margin-bottom: 2px; }
                    .barcode-container { margin: 3px 0; }
                    .barcode-container img { height: 20mm; width: 20mm; object-fit: contain; }
                    .detail { font-size: 9px; line-height: 1.2; font-family: monospace; font-weight: bold; letter-spacing: 0.5px; }
                    .date { font-size: 7px; color: #333; margin-top: 2px; }
                </style>
            </head>
            <body>
                ${labelsHtml}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };

    window.openTraceabilityModal = function() {
        const sourceId = sourceIdInput.value;
        if (!sourceId) return;

        $('#traceabilityModal').modal('show');
        const modalBody = document.getElementById('traceabilityBody');
        modalBody.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info mb-3"></i><p class="text-muted">Melacak riwayat bibit...</p></div>';

        fetch(`<?= url('seedling-admin/get-batch-traceability-ajax') ?>?source_type=${currentSourceType}&source_id=${sourceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    renderTraceabilityData(data.data, displaySourceName.value);
                } else {
                    modalBody.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i> ${data.message || 'Data tidak ditemukan.'}</div>`;
                }
            })
            .catch(err => {
                modalBody.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan jaringan.</div>';
            });
    };

    function renderTraceabilityData(data, title) {
        const modalBody = document.getElementById('traceabilityBody');
        
        let html = `
            <h5 class="font-weight-bold text-dark mb-4 border-bottom pb-2">${title}</h5>
            <div class="row">
        `;

        // Kolom Kiri
        html += `<div class="col-md-6 mb-3">`;

        // 1. Sumber Benih
        if (data.seed_source) {
            html += `
                <div class="card shadow-sm border-left-primary mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">${data.seed_source.name}</div>
                        <div class="small text-muted">
                            Vendor: ${data.seed_source.vendor || '-'}<br>
                            Kab/Kota: ${data.seed_source.kabupaten || '-'}<br>
                            Sertifikat: ${data.seed_source.sertifikat || '-'}
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="card shadow-sm border-left-secondary mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-seedling mr-1"></i> Asal Benih / Genetik</div>
                        <div class="small text-muted">Tidak ada data sumber benih yang dilacak untuk batch ini.</div>
                    </div>
                </div>
            `;
        }

        // 2. Riwayat Penaburan
        if (data.sowing) {
            html += `
                <div class="card shadow-sm border-left-warning mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><i class="fas fa-hand-holding-water mr-1"></i> Riwayat Penaburan (PC)</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">${data.sowing.code}</div>
                        <div class="small text-muted">
                            Tanggal: ${data.sowing.date}<br>
                            Jumlah Benih: ${data.sowing.seed_quantity} ${data.sowing.seed_unit}
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += `</div>`; // End Kolom Kiri

        // Kolom Kanan
        html += `<div class="col-md-6 mb-3">`;

        // 3. Riwayat Penyapihan
        if (data.weaning) {
            html += `
                <div class="card shadow-sm border-left-success mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><i class="fas fa-expand-arrows-alt mr-1"></i> Riwayat Sapih/Entres (PE/ET)</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">${data.weaning.code}</div>
                        <div class="small text-muted">
                            Tanggal: ${data.weaning.date}<br>
                            Lokasi Awal: ${data.weaning.location || '-'}<br>
                            Qty Sapih/Potong: ${Number(data.weaning.quantity).toLocaleString('id-ID')} btg
                        </div>
                    </div>
                </div>
            `;
        }

        // 4. Komposisi Media
        if (data.media && data.media.items.length > 0) {
            let mediaItemsHtml = data.media.items.map(m => `<li>${m.name} &mdash; ${m.quantity} ${m.unit}</li>`).join('');
            html += `
                <div class="card shadow-sm border-left-info mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">${data.media.code}</div>
                        <div class="small text-muted mt-2">Komposisi:</div>
                        <ul class="small text-muted pl-3 mb-0">
                            ${mediaItemsHtml}
                        </ul>
                    </div>
                </div>
            `;
        } else if (currentSourceType === 'PE') {
            html += `
                <div class="card shadow-sm border-left-secondary mb-3">
                    <div class="card-body p-3">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1"><i class="fas fa-mortar-pestle mr-1"></i> Media Tanam (MT)</div>
                        <div class="small text-muted">Data komposisi media tidak ditemukan untuk batch ini.</div>
                    </div>
                </div>
            `;
        }

        html += `</div>`; // End Kolom Kanan
        html += `</div>`; // End Row

        modalBody.innerHTML = html;
    }

    // Dynamic location label based on mutation type
    const mutationType = document.getElementById('mutation_type');
    const locationLabel = document.getElementById('location_label');
    const locationHint = document.getElementById('location_hint');
    const locationInput = document.getElementById('target_location');

    function updateLocationLabel() {
        const val = mutationType.value;
        const isNaikKelas = val.startsWith('NAIK KELAS');

        if (val === 'MATI') {
            locationLabel.textContent = 'Lokasi Kejadian (Opsional)';
            locationInput.placeholder = 'Contoh: GHA Blok A, Bedeng No. 3';
            locationHint.textContent = 'Catat di area mana kematian bibit ini terjadi.';
        } else if (isNaikKelas) {
            locationLabel.textContent = 'Tujuan / Keterangan Kelulusan';
            locationInput.placeholder = 'Contoh: Siap Distribusi, OGA Utara';
            const programLabel = val.replace('NAIK KELAS ', '');
            locationHint.textContent = 'Bibit ini akan otomatis masuk ke Stok Bibit Jadi dengan program ' + programLabel + '.';
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
