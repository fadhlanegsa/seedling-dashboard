<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 font-weight-bold text-uppercase text-primary">EDIT MUTASI BIBIT (BO)</h2>
        <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('seedling-edit/update-mutation/' . $data['id']) ?>" method="POST" id="editMutationForm">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow mb-4 border-top-primary">
                    <div class="card-body p-4">
                        
                        <div class="alert alert-info small shadow-sm">
                            <i class="fas fa-info-circle mr-2"></i> 
                            <b>INFO:</b> Anda sedang mengedit record mutasi <b><?= $data['mutation_code'] ?></b> (<?= $data['mutation_type'] ?>). 
                            Jenis mutasi tidak dapat diubah untuk menjaga integritas stok.
                        </div>

                        <div class="row form-group mb-4">
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-primary">Kode Mutasi</label>
                                <input type="text" class="form-control bg-light font-weight-bold shadow-none" value="<?= $data['mutation_code'] ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-primary">Tanggal Mutasi</label>
                                <input type="date" name="mutation_date" class="form-control font-weight-bold" value="<?= $data['mutation_date'] ?>" required>
                            </div>
                        </div>

                        <hr>

                        <div class="bg-light p-4 rounded border mb-4 shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <label class="small font-weight-bold text-danger text-uppercase mb-0">Batch Bibit Asal</label>
                                    <div class="h6 font-weight-bold text-dark mt-1"><?= $data['source_type'] ?> - ID: <?= $data['source_id'] ?></div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <label class="small text-muted mb-0">Tipe Mutasi</label>
                                    <div class="badge badge-warning p-2 px-3 font-weight-bold"><?= $data['mutation_type'] ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Jumlah Mutasi (btg)</label>
                                    <input type="number" step="1" name="quantity" class="form-control form-control-lg border-primary font-weight-bold text-primary" value="<?= (int)$data['quantity'] ?>" required>
                                    <small class="text-muted">Perubahan jumlah akan otomatis menyesuaikan stok bibit jadi.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Lokasi Asal (Origin)</label>
                                    <input type="text" name="origin_location" class="form-control form-control-lg bg-light" value="<?= $data['origin_location'] ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 shadow-none">
                            <label class="small font-weight-bold text-dark">Lokasi Tujuan / Keterangan</label>
                            <input type="text" name="target_location" class="form-control" value="<?= $data['target_location'] ?>" placeholder="Contoh: OGA Blok C, Distribusi, dll">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-muted">Mandor</label>
                                    <input type="text" name="mandor" class="form-control" value="<?= $data['mandor'] ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small text-muted">Pelaksana / Manager</label>
                                    <input type="text" name="manager" class="form-control" value="<?= $data['manager'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted">Notes (Catatan Tambahan)</label>
                            <textarea name="notes" class="form-control" rows="2"><?= $data['notes'] ?></textarea>
                        </div>

                        <div class="bg-warning-soft p-3 rounded mb-4 border border-warning">
                            <label class="small font-weight-bold text-dark"><i class="fas fa-exclamation-triangle mr-2"></i>Alasan Perubahan Data (Audit Trail)</label>
                            <textarea name="edit_reason" class="form-control border-warning" rows="2" placeholder="Sebutkan alasan mengapa data ini diubah..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm">Simpan Perubahan</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
</style>
