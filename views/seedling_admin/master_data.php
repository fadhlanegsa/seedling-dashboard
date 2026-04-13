<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-database text-primary mr-2"></i> Database Penatausahaan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">Penatausahaan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Database</li>
            </ol>
        </nav>
    </div>

    <!-- Alert for Success/Error -->
    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jenis Barang (Spesifik)</h6>
            <a href="<?= url('seedling-admin/master-data-form') ?>" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Item Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="masterTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Kategori</th>
                            <th>Nama Item (Spesifik)</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted border-bottom-0">Belum ada data master. Silakan klik "Tambah Item Baru".</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center font-weight-bold"><?= $item['category_code'] ?>.<?= $item['code'] ?></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= $item['category'] ?></span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-gray-800"><?= $item['name'] ?></div>
                                        <?php if ($item['scientific_name']): ?>
                                            <small class="text-italic font-italic"><?= $item['scientific_name'] ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($item['result_seedling_name'])): ?>
                                            <div class="mt-1">
                                                <span class="badge badge-primary-soft text-primary" style="background-color: rgba(78, 115, 223, 0.1); font-size: 0.7rem;">
                                                    <i class="fas fa-link mr-1"></i> Terhubung: <?= $item['result_seedling_name'] ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($item['description']): ?>
                                            <div class="text-xs text-muted mt-1"><?= $item['description'] ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item['unit'] ?></td>
                                    <td class="text-center">
                                        <a href="<?= url('seedling-admin/master-data-form/' . $item['id']) ?>" class="btn btn-sm btn-info mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $item['id'] ?>" data-name="<?= $item['name'] ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" action="" method="POST">
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-bottom-0">
                    <h5 class="modal-title font-weight-bold">Konfirmasi Hapus</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body py-4">
                    Apakah Anda yakin ingin menghapus item <span id="deleteItemName" class="font-weight-bold"></span> dari database? 
                    <br><small class="text-danger">*Data yang sudah digunakan dalam transaksi mungkin tidak bisa dihapus.</small>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">Hapus Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete Button Logic
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const deleteModal = $('#deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteItemName = document.getElementById('deleteItemName');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            deleteItemName.textContent = name;
            deleteForm.action = `<?= url('seedling-admin/delete-master-data') ?>/${id}`;
            deleteModal.modal('show');
        });
    });

    // DataTable initialization if exists
    if ($.fn.DataTable) {
        $('#masterTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[2, "asc"], [1, "asc"]]
        });
    }
});
</script>
