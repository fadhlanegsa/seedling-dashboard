<div class="page-header">
    <h1><i class="fas fa-edit"></i> <?= $title ?></h1>
    <p>Form data persemaian</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?= isset($nursery) ? url('admin/nurseries/update') : url('admin/nurseries/store') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <?php if (isset($nursery)): ?>
                        <input type="hidden" name="id" value="<?= $nursery['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nama Persemaian <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="<?= $nursery['name'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bpdas_id">BPDAS Induk <span class="text-danger">*</span></label>
                        <select name="bpdas_id" id="bpdas_id" class="form-control select2" required>
                            <option value="">-- Pilih BPDAS --</option>
                            <?php foreach ($bpdas_list as $bpdas): ?>
                                <option value="<?= $bpdas['id'] ?>" 
                                    <?= (isset($nursery) && $nursery['bpdas_id'] == $bpdas['id']) ? 'selected' : '' ?>>
                                    <?= $bpdas['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <textarea name="address" id="address" class="form-control" rows="3"><?= $nursery['address'] ?? '' ?></textarea>
                    </div>
                    
                    <?php if (isset($nursery)): ?>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                   <?= $nursery['is_active'] ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group mt-4 d-flex justify-content-between">
                        <a href="<?= url('admin/nurseries') ?>" class="btn btn-secondary">Kembali</a>
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
        theme: 'bootstrap4'
    });
});
</script>
