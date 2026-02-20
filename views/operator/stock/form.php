<div class="page-header">
    <h1><i class="fas fa-edit"></i> <?= $title ?></h1>
    <p>Input data stok bibit untuk persemaian</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= url('operator/stock/save') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="id" value="<?= $stock['id'] ?? '' ?>">
                    
                    <div class="form-group">
                        <label for="seedling_type_id">Jenis Bibit <span class="text-danger">*</span></label>
                        <select name="seedling_type_id" id="seedling_type_id" class="form-control select2" required <?= isset($stock) ? 'disabled' : '' ?>>
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
                            <small class="form-text text-muted">Jenis bibit tidak dapat diubah pada mode edit.</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Jumlah Stok <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" 
                               value="<?= $stock['quantity'] ?? '' ?>" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"><?= $stock['notes'] ?? '' ?></textarea>
                    </div>
                    
                    <div class="form-group mt-4 d-flex justify-content-between">
                        <a href="<?= url('operator/stock') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: "Pilih Jenis Bibit"
    });
});
</script>
