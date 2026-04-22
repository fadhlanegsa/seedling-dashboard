<?php /** Edit Form: Seedling Harvesting (PA) */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="alert alert-warning shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div><strong>Mode Edit Aktif</strong> — Mengedit Pemanenan <code><?= htmlspecialchars($data['harvest_code']) ?></code>.</div>
            </div>
            <div class="card shadow mb-4" style="border-top:4px solid #4e73df;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-leaf mr-2"></i> EDIT PANEN ANAKAN — <?= htmlspecialchars($data['harvest_code']) ?></h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body p-5">
                    <form action="<?= url('seedling-edit/update-harvesting/' . $data['id']) ?>" method="POST">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <!-- Code & Date Row -->
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-md-6 d-flex align-items-center">
                                <label class="text-primary font-weight-bold mr-3 mb-0" style="min-width:80px;">Tanggal</label>
                                <input type="date" name="harvest_date" class="form-control" value="<?= $data['harvest_date'] ?>" required>
                            </div>
                            <div class="col-md-6 d-flex align-items-center mt-3 mt-md-0">
                                <label class="text-primary font-weight-bold mr-3 mb-0" style="min-width:60px;">Kode</label>
                                <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['harvest_code']) ?>" readonly>
                            </div>
                        </div>

                        <!-- Source Sowing (readonly) -->
                        <h6 class="font-weight-bold text-warning mb-3">SUMBER SEMAI (Terkunci)</h6>
                        <div class="row mb-4 pl-4 ml-2 border-left border-warning">
                            <div class="col-12 mb-2">
                                <input type="text" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($data['sowing_code'] ?? 'PC-' . $data['sowing_id']) ?>" readonly>
                                <small class="text-muted">Kode Semai tidak dapat diubah saat edit.</small>
                            </div>
                        </div>

                        <!-- Harvested Quantity -->
                        <div class="form-group row align-items-center mb-5">
                            <label class="col-sm-6 col-form-label font-weight-bold text-primary text-md-right h6 mb-0 text-uppercase">Jumlah Anakan Dihasilkan</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" step="1" name="harvested_quantity" class="form-control font-weight-bold text-lg border-primary" value="<?= $data['harvested_quantity'] ?>" required>
                                    <div class="input-group-append"><span class="input-group-text">btg</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <!-- Metadata -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted small">Mandor</label>
                            <div class="col-sm-9"><input type="text" name="mandor" class="form-control" value="<?= htmlspecialchars($data['mandor'] ?? '') ?>"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted small">Pelaksana/Manager</label>
                            <div class="col-sm-9"><input type="text" name="manager" class="form-control" value="<?= htmlspecialchars($data['manager'] ?? '') ?>"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold text-warning small"><i class="fas fa-map-marker-alt mr-1"></i> Lokasi / Blok</label>
                            <div class="col-sm-9"><input type="text" name="location" class="form-control" value="<?= htmlspecialchars($data['location'] ?? '') ?>"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted small">Keterangan</label>
                            <div class="col-sm-9"><input type="text" name="notes" class="form-control" value="<?= htmlspecialchars($data['notes'] ?? '') ?>"></div>
                        </div>

                        <!-- AUDIT TRAIL -->
                        <div class="form-group p-3 rounded border border-warning mt-4" style="background:#fff9e6;">
                            <label class="font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-2"></i>Alasan Edit <span class="text-danger">*</span></label>
                            <textarea name="edit_reason" class="form-control border-danger mt-2" rows="3" required placeholder="Wajib diisi! Contoh: Koreksi jumlah panen, kesalahan tanggal, dll..."></textarea>
                            <small class="text-danger">Wajib diisi untuk keperluan Audit Trail.</small>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= url('seedling-admin') ?>" class="btn btn-light border px-4 py-2 font-weight-bold shadow-sm mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.border-top-primary { border-top: 4px solid #4e73df !important; }</style>
