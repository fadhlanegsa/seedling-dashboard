<?php
/**
 * Admin: Create/Edit Seed Source Form  
 */
$isEdit = isset($seedSource);
$formAction = $isEdit ? url('admin/seed-sources/update/' . $seedSource['id']) : url('admin/seed-sources/store');
?>

<div class="page-header">
    <h1>
        <i class="fas fa-tree"></i> 
        <?= $isEdit ? 'Edit Sumber Benih' : 'Tambah Sumber Benih' ?>
    </h1>
    <a href="<?= url('admin/seed-sources') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $formAction ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <h4 class="mb-3">Informasi Dasar</h4>
            
            <div class="form-group">
                <label for="seed_source_name">Nama Sumber Benih <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="seed_source_name" name="seed_source_name" 
                       value="<?= $seedSource['seed_source_name'] ?? '' ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="local_name">Nama Lokal</label>
                        <input type="text" class="form-control" id="local_name" name="local_name" 
                               value="<?= $seedSource['local_name'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="botanical_name">Nama Botani</label>
                        <input type="text" class="form-control" id="botanical_name" name="botanical_name" 
                               value="<?= $seedSource['botanical_name'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="province_id">Provinsi <span class="text-danger">*</span></label>
                        <select class="form-control" id="province_id" name="province_id" required>
                            <option value="">Pilih Provinsi</option>
                            <?php foreach ($provinces as $prov): ?>
                            <option value="<?= $prov['id'] ?>" <?= ($seedSource['province_id'] ?? '') == $prov['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="seedling_type_id">Jenis Bibit</label>
                        <select class="form-control" id="seedling_type_id" name="seedling_type_id">
                            <option value="">Pilih Jenis Bibit (Opsional)</option>
                            <?php foreach ($seedlingTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= ($seedSource['seedling_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Lokasi & Area</h4>
            
            <div class="form-group">
                <label for="location">Lokasi SB</label>
                <textarea class="form-control" id="location" name="location" rows="2"><?= $seedSource['location'] ?? '' ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="area_hectares">Luas (Ha)</label>
                        <input type="number" step="0.001" class="form-control" id="area_hectares" name="area_hectares" 
                               value="<?= $seedSource['area_hectares'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                               value="<?= $seedSource['latitude'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                               value="<?= $seedSource['longitude'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="seed_class">Kelas SB</label>
                <input type="text" class="form-control" id="seed_class" name="seed_class" 
                       placeholder="Contoh: TBT, KBT" value="<?= $seedSource['seed_class'] ?? '' ?>">
            </div>
            
            <h4 class="mt-4 mb-3">Informasi Pemilik</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="owner_name">Nama Pemilik</label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" 
                               value="<?= $seedSource['owner_name'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="owner_phone">Nomor Telepon Pemilik</label>
                        <input type="text" class="form-control" id="owner_phone" name="owner_phone" 
                               placeholder="08xxxxxxxxxx" value="<?= $seedSource['owner_phone'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="ownership_type">Jenis Kepemilikan</label>
                <select class="form-control" id="ownership_type" name="ownership_type">
                    <option value="">Pilih Jenis Kepemilikan</option>
                    <option value="Perorangan" <?= ($seedSource['ownership_type'] ?? '') == 'Perorangan' ? 'selected' : '' ?>>Perorangan</option>
                    <option value="Perusahaan" <?= ($seedSource['ownership_type'] ?? '') == 'Perusahaan' ? 'selected' : '' ?>>Perusahaan</option>
                    <option value="Instansi" <?= ($seedSource['ownership_type'] ?? '') == 'Instansi' ? 'selected' : '' ?>>Instansi</option>
                </select>
            </div>
            
            <h4 class="mt-4 mb-3">Sertifikasi</h4>
            
            <div class="form-group">
                <label for="certificate_number">Nomor Sertifikat</label>
                <input type="text" class="form-control" id="certificate_number" name="certificate_number" 
                       value="<?= $seedSource['certificate_number'] ?? '' ?>">
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="certificate_date">Tanggal Sertifikat</label>
                        <input type="date" class="form-control" id="certificate_date" name="certificate_date" 
                               value="<?= $seedSource['certificate_date'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="certificate_validity">Masa Berlaku</label>
                        <input type="date" class="form-control" id="certificate_validity" name="certificate_validity" 
                               value="<?= $seedSource['certificate_validity'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Produksi</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tree_count">Jumlah Pohon</label>
                        <input type="number" class="form-control" id="tree_count" name="tree_count" 
                               value="<?= $seedSource['tree_count'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="production_estimate_per_year">Estimasi Produksi (Kg/tahun)</label>
                        <input type="number" step="0.001" class="form-control" id="production_estimate_per_year" name="production_estimate_per_year" 
                               value="<?= $seedSource['production_estimate_per_year'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="seed_quantity_estimate">Estimasi Jumlah Benih (butir)</label>
                        <input type="number" class="form-control" id="seed_quantity_estimate" name="seed_quantity_estimate" 
                               value="<?= $seedSource['seed_quantity_estimate'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="flowering_season">Musim Pembungaan</label>
                        <input type="text" class="form-control" id="flowering_season" name="flowering_season" 
                               value="<?= $seedSource['flowering_season'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fruiting_season">Musim Buah Masak</label>
                        <input type="text" class="form-control" id="fruiting_season" name="fruiting_season" 
                               value="<?= $seedSource['fruiting_season'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Pemanfaatan</h4>
            
            <div class="form-group">
                <label for="utilization">Pemanfaatan</label>
                <textarea class="form-control" id="utilization" name="utilization" rows="3"><?= $seedSource['utilization'] ?? '' ?></textarea>
            </div>
            
            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Update' : 'Simpan' ?>
                </button>
                <a href="<?= url('admin/seed-sources') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
