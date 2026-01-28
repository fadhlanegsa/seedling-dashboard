<?php
// Jika ada flash message sukses
if (isset($_SESSION['success'])):
?>
  <div class="alert alert-success" role="alert">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<!-- Judul Halaman -->
<h1 class="h3 mb-3">Kelola BPDAS</h1>

<!-- Tombol Tambah -->
<a href="<?= url('admin/bpdas-form') ?>" class="btn btn-primary mb-3">
  <i class="fas fa-plus"></i> Tambah BPDAS Baru
</a>

<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>No</th>
      <th>Nama BPDAS</th>
      <th>Provinsi</th>
      <th>Alamat</th>
      <th>Kontak</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($bpdas)): ?>
      <?php
          // Atur pagination dengan default agar aman jika $pagination tidak ada atau tidak lengkap
          $currentPage = $pagination['page'] ?? 1;
          $totalPages = $pagination['totalPages'] ?? 1;
          $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
      ?>
      <?php foreach ($bpdas as $index => $item): ?>
        <tr>
          <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
          <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
          <td><?= htmlspecialchars($item['province_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($item['address']) ?></td>
          <td>
            <?php if (!empty($item['phone'])): ?>
              <i class="fas fa-phone"></i> <?= htmlspecialchars($item['phone']) ?><br>
            <?php endif; ?>
            <?php if (!empty($item['email'])): ?>
              <i class="fas fa-envelope"></i> <?= htmlspecialchars($item['email']) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($item['is_active'])): ?>
              <span class="badge badge-success">Aktif</span>
            <?php else: ?>
              <span class="badge badge-secondary">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= url('admin/bpdas-form/' . $item['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
              <i class="fas fa-edit"></i>
            </a>
            <button onclick="createAccount(<?= $item['id'] ?>)" class="btn btn-sm btn-info" title="Buat Akun">
              <i class="fas fa-user-plus"></i>
            </button>
            <button onclick="deleteBPDAS(<?= $item['id'] ?>)" class="btn btn-sm btn-danger" title="Hapus">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="7" class="text-center">Tidak ada data BPDAS</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php
// Render pagination using helper
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    renderPagination($pagination, 'admin/bpdas');
}
?>

<script>
  function createAccount(id) {
    if (confirm('Yakin ingin membuat akun untuk BPDAS ini?')) {
      window.location.href = '<?= url('admin/create-account/') ?>' + id;
    }
  }

  function deleteBPDAS(id) {
    if (confirm('Yakin ingin menghapus BPDAS ini? Data terkait akan hilang!')) {
      window.location.href = '<?= url('admin/bpdas-delete/') ?>' + id;
    }
  }
</script>