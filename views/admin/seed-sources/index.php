<?php
/**
 * Admin: Seed Sources List
 */
?>
<div class="page-header">
    <h1><i class="fas fa-tree"></i> Direktori Sumber Benih Nasional</h1>
    <a href="<?= url('admin/seed-sources/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Sumber Benih
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Sumber Benih</h3>
    </div>
    <div class="card-body">
        <table id="seedSources Table" class="data-table">
            <thead>
                <tr>
                    <th>Nama Sumber Benih</th>
                    <th>Jenis Pohon</th>
                    <th>Provinsi</th>
                    <th>Lokasi</th>
                    <th>Pemilik</th>
                    <th>Kelas SB</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seedSources as $source): ?>
                <tr>
                    <td><?= htmlspecialchars($source['seed_source_name']) ?></td>
                    <td><?= htmlspecialchars($source['seedling_type_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($source['province_name']) ?></td>
                    <td><?= htmlspecialchars($source['location'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($source['owner_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($source['seed_class'] ?? '-') ?></td>
                    <td>
                        <a href="<?= url('admin/seed-sources/detail/' . $source['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= url('admin/seed-sources/edit/' . $source['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= url('admin/seed-sources/delete/' . $source['id']) ?>" 
                           class="btn btn-sm btn-danger" 
                           title="Hapus"
                           onclick="return confirm('Yakin ingin menghapus sumber benih ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#seedSourcesTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
