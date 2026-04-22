<?php
/**
 * Edit Form: Bahan Baku Transaksi
 * Pre-filled from existing data with Audit Trail (edit_reason required)
 */
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Edit Alert Banner -->
            <div class="alert alert-warning border-left-warning shadow-sm mb-4 d-flex align-items-center" style="border-left:4px solid #f6c23e;">
                <i class="fas fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
                <div>
                    <strong>Mode Edit Aktif</strong> — Anda sedang mengedit transaksi <code><?= htmlspecialchars($data['transaction_id'] ?? $data['id']) ?></code>.
                    Perubahan ini akan dicatat dalam <strong>Audit Trail</strong>.
                </div>
            </div>

            <div class="card shadow mb-4 border-top-warning">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> EDIT TRANSAKSI BAHAN BAKU</h6>
                    <a href="<?= url('seedling-admin') ?>" class="btn btn-sm btn-light border shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body px-5 py-4">
                    <form action="<?= url('seedling-edit/update-bahan-baku/' . $data['id']) ?>" method="POST" id="editBahanBakuForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6 border-right">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">ID Transaksi</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($data['transaction_id'] ?? '') ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Tanggal</label>
                                    <div class="col-sm-7">
                                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($data['transaction_date'] ?? $data['date'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Jenis Bahan</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['item_name'] ?? $data['category'] ?? '-') ?>" readonly>
                                        <input type="hidden" name="item_id" value="<?= $data['item_id'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Jumlah</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="quantity" class="form-control font-weight-bold" value="<?= $data['quantity'] ?>" required min="0.01">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light"><?= htmlspecialchars($data['item_unit'] ?? 'kg') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Keterangan</label>
                                    <div class="col-sm-7">
                                        <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6 pl-md-5">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Pengirim (Vendor)</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="sender" class="form-control" value="<?= htmlspecialchars($data['sender'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Penerima (Staff)</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="receiver" class="form-control" value="<?= htmlspecialchars($data['receiver'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Mandor</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="foreman" class="form-control" value="<?= htmlspecialchars($data['foreman'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-gray-700">Pelaksana / Manager</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="manager" class="form-control" value="<?= htmlspecialchars($data['manager'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- === AUDIT TRAIL REASON === -->
                                <div class="form-group row mb-3 mt-4">
                                    <label class="col-sm-5 col-form-label font-weight-bold text-danger"><i class="fas fa-clipboard-list mr-1"></i> Alasan Edit <span class="text-danger">*</span></label>
                                    <div class="col-sm-7">
                                        <textarea name="edit_reason" class="form-control border-danger" rows="2" required placeholder="Wajib diisi!"></textarea>
                                        <small class="text-danger font-weight-bold">Audit Trail.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4 pt-3 border-top justify-content-center">
                            <div class="col-md-8 text-center">
                                <a href="<?= url('seedling-admin') ?>" class="btn btn-secondary btn-lg mr-3 px-5 shadow-sm">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm font-weight-bold text-dark">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-top-warning { border-top: 4px solid #f6c23e !important; }
    .text-gray-700 { color: #4a4a4a !important; }
</style>
