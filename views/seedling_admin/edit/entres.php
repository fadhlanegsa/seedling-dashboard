<?php /** Edit Form: Seedling Entres (Sapih ET) */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="alert alert-warning shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div><strong>Mode Edit Aktif</strong> — Mengedit Entres <code><?= htmlspecialchars($data['entres_code']) ?></code>.</div>
            </div>
            <div class="card shadow mb-4" style="border-top:4px solid #6f42c1;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background:#6f42c1; color:white;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> EDIT SAPIH ENTRES (ET) — <?= htmlspecialchars($data['entres_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-edit/update-entres/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <div class="row">
                            <div class="col-md-6 border-right">
                                <h6 class="font-weight-bold mb-3" style="color:#6f42c1;">Info Utama</h6>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Kode Entres</label>
                                    <div class="col-sm-8"><input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['entres_code']) ?>" readonly></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Tanggal</label>
                                    <div class="col-sm-8"><input type="date" name="entres_date" class="form-control" value="<?= $data['entres_date'] ?>" required></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Sumber Panen (PA)</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['harvest_code'] ?? 'PA#' . $data['harvest_id']) ?>" readonly>
                                        <small class="text-muted">Sumber tidak dapat diubah.</small>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Sapih PE (Sumber)</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['weaning_code'] ?? 'PE#' . ($data['weaning_id'] ?? '-')) ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Jml Dipakai (btg)</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="number" step="1" name="used_quantity" class="form-control font-weight-bold" value="<?= $data['used_quantity'] ?>" required>
                                            <div class="input-group-append"><span class="input-group-text">btg</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Hasil Entres (btg)</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="number" step="1" name="yield_quantity" class="form-control font-weight-bold" value="<?= $data['yield_quantity'] ?>" required>
                                            <div class="input-group-append"><span class="input-group-text">btg</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Lokasi</label>
                                    <div class="col-sm-8"><input type="text" name="location" class="form-control" value="<?= htmlspecialchars($data['location'] ?? '') ?>"></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Mandor</label>
                                    <div class="col-sm-8"><input type="text" name="mandor" class="form-control" value="<?= htmlspecialchars($data['mandor'] ?? '') ?>"></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Manager</label>
                                    <div class="col-sm-8"><input type="text" name="manager" class="form-control" value="<?= htmlspecialchars($data['manager'] ?? '') ?>"></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Keterangan</label>
                                    <div class="col-sm-8"><textarea name="notes" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea></div>
                                </div>
                            </div>

                            <div class="col-md-6 pl-md-4">
                                <!-- Materials -->
                                <h6 class="font-weight-bold text-success mb-3">Bahan Pendukung Entres</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light small text-uppercase"><tr><th>Bahan</th><th width="150">Jumlah</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($currentMaterials as $cm): ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="item_id[]" value="<?= $cm['item_id'] ?>">
                                                    <span class="font-weight-bold small"><?= htmlspecialchars($cm['item_name'] ?? 'Item #' . $cm['item_id']) ?></span>
                                                </td>
                                                <td><input type="number" step="0.01" name="material_quantity[]" class="form-control form-control-sm" value="<?= $cm['quantity'] ?>" required></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- AUDIT TRAIL -->
                                <div class="form-group p-3 rounded border border-warning mt-3" style="background:#fff9e6;">
                                    <label class="font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-1"></i> Alasan Edit <span class="text-danger">*</span></label>
                                    <textarea name="edit_reason" class="form-control border-danger mt-2" rows="4" required placeholder="Wajib diisi! Contoh: Koreksi jumlah bibit entres, perubahan tanggal, dll..."></textarea>
                                    <small class="text-danger font-weight-bold">Wajib diisi untuk keperluan Audit Trail.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top justify-content-end">
                            <div class="col-auto">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary mr-2 py-2 px-4">Batal</a>
                                <button type="submit" class="btn font-weight-bold py-2 px-5 text-white" style="background:#6f42c1;"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
