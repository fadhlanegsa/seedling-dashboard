<?php /** Edit Form: Seed Sowing */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="alert alert-warning shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div><strong>Mode Edit Aktif</strong> — Mengedit Penyemaian <code><?= htmlspecialchars($data['sowing_code']) ?></code>.</div>
            </div>
            <div class="card shadow mb-4" style="border-top:4px solid #ffc107;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-seedling mr-2"></i> EDIT PENYEMAIAN BENIH — <?= htmlspecialchars($data['sowing_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-edit/update-seed-sowing/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <div class="row">
                            <div class="col-md-6 border-right">
                                <h6 class="font-weight-bold text-warning mb-3">Info Utama</h6>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Kode</label>
                                    <div class="col-sm-8"><input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['sowing_code']) ?>" readonly></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Tanggal</label>
                                    <div class="col-sm-8"><input type="date" name="sowing_date" class="form-control" value="<?= $data['sowing_date'] ?>" required></div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Benih</label>
                                    <div class="col-sm-8">
                                        <select name="seed_item_id" class="form-control" required>
                                            <option value="">-- Pilih Benih --</option>
                                            <?php foreach ($seeds as $seed): ?>
                                                <option value="<?= $seed['id'] ?>" <?= ($seed['id'] == $data['seed_item_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($seed['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 col-form-label small font-weight-bold">Jml Disemaikan</label>
                                    <div class="col-sm-8"><input type="number" step="0.01" name="seed_quantity" class="form-control font-weight-bold" value="<?= $data['seed_quantity'] ?>" required></div>
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
                                <!-- Polybags used -->
                                <h6 class="font-weight-bold text-info mb-3">Polybag Digunakan</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light small text-uppercase"><tr><th>Kode Pengisian</th><th width="150">Jumlah</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($currentPolybags as $cp): ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="bag_filling_id[]" value="<?= $cp['bag_filling_id'] ?>">
                                                    <span class="badge badge-info px-2"><?= htmlspecialchars($cp['filling_code'] ?? 'PB#' . $cp['bag_filling_id']) ?></span>
                                                </td>
                                                <td><input type="number" step="1" name="polybag_quantity[]" class="form-control form-control-sm" value="<?= $cp['quantity'] ?>" required></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Materials used -->
                                <h6 class="font-weight-bold text-success mb-3">Bahan Pendukung</h6>
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
                                <div class="form-group p-3 rounded border border-warning" style="background:#fff9e6;">
                                    <label class="small font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-1"></i> Alasan Edit <span class="text-danger">*</span></label>
                                    <textarea name="edit_reason" class="form-control border-danger" rows="3" required placeholder="Wajib diisi! (misal: typo jumlah benih, koreksi tanggal, dll)"></textarea>
                                    <small class="text-danger">Wajib untuk Audit Trail.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top justify-content-end">
                            <div class="col-auto">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary mr-2 py-2 px-4">Batal</a>
                                <button type="submit" class="btn btn-warning font-weight-bold py-2 px-5 text-dark"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
