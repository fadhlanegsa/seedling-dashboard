<?php /** Edit Form: Seedling Mutation (Naik Kelas / BO-PUB) */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="alert alert-warning shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div><strong>Mode Edit Aktif</strong> — Mengedit Mutasi <code><?= htmlspecialchars($data['mutation_code']) ?></code>. Tipe mutasi tidak dapat diubah.</div>
            </div>
            <div class="card shadow mb-4" style="border-top:4px solid #e74a3b;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-danger text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> EDIT MUTASI BIBIT — <?= htmlspecialchars($data['mutation_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body p-5">
                    <form action="<?= url('seedling-edit/update-mutation/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <!-- Code, Type & Date Row -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted">Kode Mutasi</label>
                                <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['mutation_code']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted">Tipe Mutasi (Terkunci)</label>
                                <input type="text" class="form-control bg-light font-weight-bold <?= $data['mutation_type'] === 'MATI' ? 'text-danger' : ($data['mutation_type'] === 'NAIK KELAS' ? 'text-success' : 'text-primary') ?>" value="<?= htmlspecialchars($data['mutation_type']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted">Tanggal</label>
                                <input type="date" name="mutation_date" class="form-control" value="<?= $data['mutation_date'] ?>" required>
                            </div>
                        </div>

                        <!-- Source (readonly) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-warning"><i class="fas fa-link mr-1"></i> Sumber Bibit (Terkunci)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text bg-warning text-dark"><?= htmlspecialchars($data['source_type']) ?></span></div>
                                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['source_code'] ?? 'ID: ' . $data['source_id']) ?>" readonly>
                                </div>
                                <small class="text-muted">Sumber tidak dapat diubah saat edit.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-danger">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1" name="quantity" class="form-control form-control-lg border-danger text-center font-weight-bold" style="background:#fff5f5;" value="<?= $data['quantity'] ?>" required>
                                    <div class="input-group-append"><span class="input-group-text">btg</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Lokasi Asal</label>
                                    <input type="text" name="origin_location" class="form-control" value="<?= htmlspecialchars($data['origin_location'] ?? '') ?>" placeholder="Blok/Bedeng asal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Lokasi Tujuan</label>
                                    <input type="text" name="target_location" class="form-control" value="<?= htmlspecialchars($data['target_location'] ?? '') ?>" placeholder="Blok/Bedeng tujuan">
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Mandor</label>
                                    <input type="text" name="mandor" class="form-control" value="<?= htmlspecialchars($data['mandor'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Manager</label>
                                    <input type="text" name="manager" class="form-control" value="<?= htmlspecialchars($data['manager'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Keterangan</label>
                            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                        </div>

                        <!-- AUDIT TRAIL -->
                        <div class="form-group p-3 rounded border border-warning mt-4" style="background:#fff9e6;">
                            <label class="font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-2"></i>Alasan Edit <span class="text-danger">*</span></label>
                            <textarea name="edit_reason" class="form-control border-danger mt-2" rows="3" required placeholder="Wajib diisi! Contoh: Koreksi jumlah mutasi, perubahan tipe, dll..."></textarea>
                            <small class="text-danger">Wajib diisi untuk keperluan Audit Trail.</small>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= url('seedling-admin') ?>" class="btn btn-light border px-4 py-2 font-weight-bold shadow-sm mr-2">Batal</a>
                            <button type="submit" class="btn btn-danger px-4 py-2 font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
