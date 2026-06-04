<style>
    /* Tema Warna PPTH (Hijau Earth-tone) */
    :root {
        --ppth-green: #2e7d32;
        --ppth-green-light: #e8f5e9;
        --ppth-green-dark: #1b5e20;
    }
    
    /* 1. Styling untuk Tap-able Cards (Kompatibel dengan Bootstrap 4) */
    .btn-check {
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }
    
    .radio-card {
        border-radius: 16px;
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
    
    .radio-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
    }

    .btn-check:checked + .radio-card.disabled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        box-shadow: none;
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

<div class="page-header d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h4 class="font-weight-bold mb-0 text-dark"><i class="fas fa-edit text-success mr-2"></i> <?= $title ?></h4>
        <p class="text-muted small mb-0 mt-1">Input data stok bibit untuk persemaian</p>
    </div>
    <a href="<?= url('operator/stock') ?>" class="btn btn-light border shadow-sm btn-sm font-weight-bold">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row justify-content-center form-wrapper">
    <div class="col-md-8 col-lg-6">
        <form action="<?= url('operator/stock/save') ?>" method="POST" id="stockForm">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" value="<?= $stock['id'] ?? '' ?>">
            
            <!-- Jenis Bibit -->
            <div class="form-group mb-4">
                <label for="seedling_type_id" class="font-weight-bold text-muted small px-1">JENIS BIBIT <span class="text-danger">*</span></label>
                <select name="seedling_type_id" id="seedling_type_id" class="form-control select2 shadow-sm" required <?= isset($stock) ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Jenis Bibit --</option>
                    <?php foreach ($seedling_types as $type): ?>
                        <option value="<?= $type['id'] ?>" 
                            <?= (isset($stock) && $stock['seedling_type_id'] == $type['id']) ? 'selected' : '' ?>>
                            <?= $type['name'] ?> (<?= $type['scientific_name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($stock)): ?>
                    <input type="hidden" name="seedling_type_id" value="<?= $stock['seedling_type_id'] ?>">
                    <small class="form-text text-muted mt-2"><i class="fas fa-info-circle"></i> Jenis bibit tidak dapat diubah pada mode edit.</small>
                <?php endif; ?>
            </div>
            
            <!-- STANDAR 1: Tap-able Cards untuk Program -->
            <div class="form-group mb-4">
                <label class="font-weight-bold text-muted small px-1">PROGRAM <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-6 pr-2">
                        <input type="radio" class="btn-check" name="program_type" id="prog_reguler" value="Reguler" autocomplete="off" 
                            <?= (!isset($stock) || (isset($stock['program_type']) && $stock['program_type'] == 'Reguler')) ? 'checked' : '' ?>
                            <?= isset($stock) ? 'disabled' : '' ?> required>
                        <label class="radio-card w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center <?= isset($stock) ? 'disabled' : '' ?>" for="prog_reguler">
                            <i class="fas fa-seedling mb-2" style="font-size: 2rem;"></i>
                            <span class="font-weight-bold">Reguler</span>
                        </label>
                    </div>
                    <div class="col-6 pl-2">
                        <input type="radio" class="btn-check" name="program_type" id="prog_folu" value="FOLU" autocomplete="off"
                            <?= (isset($stock) && isset($stock['program_type']) && $stock['program_type'] == 'FOLU') ? 'checked' : '' ?>
                            <?= isset($stock) ? 'disabled' : '' ?> required>
                        <label class="radio-card w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center <?= isset($stock) ? 'disabled' : '' ?>" for="prog_folu">
                            <i class="fas fa-tree mb-2" style="font-size: 2rem;"></i>
                            <span class="font-weight-bold">FOLU</span>
                        </label>
                    </div>
                </div>
                <?php if (isset($stock)): ?>
                    <input type="hidden" name="program_type" value="<?= $stock['program_type'] ?>">
                <?php endif; ?>
            </div>
            
            <!-- STANDAR 2: Optimasi Input Angka -->
            <div class="form-group mb-4">
                <label class="font-weight-bold text-muted small px-1">JUMLAH STOK <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg shadow-sm overflow-hidden" style="border-radius: 12px; border: 1px solid #ced4da;">
                    <div class="input-group-prepend">
                        <button class="btn btn-light border-0 px-4 h-100 d-flex align-items-center justify-content-center" type="button" onclick="ubahQty(-1)" style="width: 70px; font-size: 1.5rem; font-weight: bold; color: var(--ppth-green);">-</button>
                    </div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" name="quantity" id="qty_input" class="form-control border-0 text-center font-weight-bold shadow-none" 
                           value="<?= $stock['quantity'] ?? '0' ?>" onfocus="this.select()" required style="background: white; height: 70px; font-size: 2rem; color: var(--ppth-green);">
                    <div class="input-group-append">
                        <button class="btn btn-light border-0 px-4 h-100 d-flex align-items-center justify-content-center" type="button" onclick="ubahQty(1)" style="width: 70px; font-size: 1.5rem; font-weight: bold; color: var(--ppth-green);">+</button>
                    </div>
                </div>
            </div>
            
            <!-- STANDAR 4: Minimalist Form (Accordion) -->
            <div class="form-group mb-4">
                <div class="accordion shadow-sm border-0 overflow-hidden" id="accordionOptional" style="border-radius: 12px;">
                    <div class="card border-0">
                        <div class="card-header bg-white p-0" id="headingNotes">
                            <button class="btn btn-block text-left font-weight-bold text-muted p-3 collapsed d-flex align-items-center justify-content-between btn-accordion-custom" type="button" data-toggle="collapse" data-target="#collapseNotes" aria-expanded="<?= !empty($stock['notes']) ? 'true' : 'false' ?>" aria-controls="collapseNotes" style="box-shadow: none;">
                                <span><i class="fas fa-sticky-note mr-2" style="color: var(--ppth-green);"></i> Catatan Tambahan (Opsional)</span>
                                <i class="fas fa-chevron-down small"></i>
                            </button>
                        </div>
                        <div id="collapseNotes" class="collapse <?= !empty($stock['notes']) ? 'show' : '' ?>" aria-labelledby="headingNotes" data-parent="#accordionOptional">
                            <div class="card-body bg-light p-3 border-top">
                                <textarea name="notes" id="notes" class="form-control border-0 shadow-sm" rows="3" placeholder="Tuliskan keterangan tambahan di sini..."><?= $stock['notes'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STANDAR 3: Sticky Bottom Bar -->
            <div class="sticky-bottom-bar">
                <div class="container p-0">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <button type="submit" class="btn btn-lg w-100 py-3 font-weight-bold shadow d-flex align-items-center justify-content-center text-white" style="border-radius: 50px; background-color: var(--ppth-green); border-color: var(--ppth-green);">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> SIMPAN DATA STOK
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script nonce="<?= cspNonce() ?>">
// JS Logic untuk merubah ikon chevron pada Accordion
$('#accordionOptional').on('show.bs.collapse', function () {
    $(this).find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
}).on('hide.bs.collapse', function () {
    $(this).find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
});

// JS Logic untuk tombol - / +
function ubahQty(delta) {
    const input = document.getElementById('qty_input');
    let val = parseInt(input.value) || 0;
    val += delta;
    if (val < 0) val = 0; 
    input.value = val;
}

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Pilih Jenis Bibit",
        width: '100%'
    });
});
</script>
