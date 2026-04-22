<?php /** Edit Form: Bag Filling */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div><strong>Mode Edit Aktif</strong> — Mengedit Pengisian Kantong <code><?= htmlspecialchars($data['filling_code']) ?></code>. Media lama akan dihapus dan diganti media baru.</div>
            </div>
            <div class="card shadow mb-4" style="border-top:4px solid #17a2b8;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> EDIT PENGISIAN KANTONG — <?= htmlspecialchars($data['filling_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-edit/update-bag-filling/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <!-- Media Usage Table -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold text-info mb-0">Media Tanam Digunakan</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-light small text-uppercase font-weight-bold text-center">
                                        <tr>
                                            <th>Kode Produksi Media</th>
                                            <th width="180">Jumlah (m3)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($currentMedia as $cm): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="media_production_id[]" value="<?= $cm['media_production_id'] ?>">
                                                <span class="badge badge-info px-2"><?= htmlspecialchars($cm['production_code'] ?? 'Media #' . $cm['media_production_id']) ?></span>
                                            </td>
                                            <td><input type="number" step="0.01" name="quantity[]" class="form-control form-control-sm" value="<?= $cm['quantity'] ?>" required></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Kode Pengisian</label>
                                    <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['filling_code']) ?>" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Tanggal</label>
                                    <input type="date" name="filling_date" class="form-control" value="<?= $data['filling_date'] ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Jenis Kantong/Polybag</label>
                                    <select name="bag_item_id" class="form-control" required>
                                        <option value="">-- Pilih Kantong --</option>
                                        <?php foreach ($bags as $bag): ?>
                                            <option value="<?= $bag['id'] ?>" <?= ($bag['id'] == $data['bag_item_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($bag['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-info">Jumlah Digunakan (Kg/Pcs)</label>
                                    <input type="number" step="0.01" name="bag_quantity" class="form-control" value="<?= $data['bag_quantity'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Mandor</label>
                                    <input type="text" name="mandor" class="form-control" value="<?= htmlspecialchars($data['mandor'] ?? '') ?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Manager</label>
                                    <input type="text" name="manager" class="form-control" value="<?= htmlspecialchars($data['manager'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-info">Total Produksi (Polybag)</label>
                                    <div class="input-group">
                                        <input type="number" step="1" name="total_production" class="form-control form-control-lg border-info text-center font-weight-bold" style="background:#e8f7ff;" value="<?= $data['total_production'] ?>" required>
                                        <div class="input-group-append"><span class="input-group-text bg-light">poly</span></div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">Keterangan</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                                </div>
                                <!-- AUDIT TRAIL -->
                                <div class="form-group mb-3 p-3 rounded border border-warning" style="background:#fff9e6;">
                                    <label class="small font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-1"></i> Alasan Edit <span class="text-danger">*</span></label>
                                    <textarea name="edit_reason" class="form-control border-danger" rows="3" required placeholder="Wajib diisi!"></textarea>
                                    <small class="text-danger">Wajib untuk Audit Trail.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 pt-3 border-top justify-content-end">
                            <div class="col-auto">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary mr-2 py-2 px-4">Batal</a>
                                <button type="submit" class="btn btn-info font-weight-bold py-2 px-5 text-white"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
