<?php
/**
 * Admin - Manage Users View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Kelola Pengguna</h1>
    <a href="<?= url('admin/user-form') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pengguna
    </a>
</div>

<!-- Role Filter -->
<div class="card mb-3">
    <div class="card-body">
        <div class="filter-buttons">
            <a href="<?= url('admin/users') ?>" 
               class="btn btn-sm <?= !$currentRole ? 'btn-primary' : 'btn-outline-primary' ?>">
                Semua
            </a>
            <?php foreach (USER_ROLES as $roleKey => $roleLabel): ?>
                <a href="<?= url('admin/users?role=' . $roleKey) ?>" 
                   class="btn btn-sm <?= $currentRole == $roleKey ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($roleLabel) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>BPDAS</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php
                    // Get pagination data for row numbering
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                            <td><strong><?= htmlspecialchars($user['username'] ?? '-') ?></strong></td>
                            <td><?= htmlspecialchars($user['full_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                            <td>
                                <?php
                                $roleClass = [
                                    'admin' => 'danger',
                                    'bpdas' => 'primary',
                                    'public' => 'success'
                                ];
                                $role = $user['role'] ?? 'public';
                                $class = $roleClass[$role] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $class ?>">
                                    <?= USER_ROLES[$role] ?? $role ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($user['bpdas_name'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($user['is_active'])): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td><?= isset($user['created_at']) ? formatDate($user['created_at'], DATE_FORMAT) : '-' ?></td>
                            <td>
                                <a href="<?= url('admin/user-form/' . $user['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($user['id'] != currentUser()['id']): ?>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data pengguna</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<?php
// Render pagination using helper
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    
    // Preserve role filter in pagination
    $queryParams = [
        'role' => $currentRole ?? null
    ];
    
    renderPagination($pagination, 'admin/users', $queryParams);
}
?>

<script>
function deleteUser(id) {
    if (confirm('Yakin ingin menghapus pengguna ini?')) {
        fetch('<?= url('admin/delete-user/') ?>' + id, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error);
            });
    }
}
</script>

<style>
.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
</style>
