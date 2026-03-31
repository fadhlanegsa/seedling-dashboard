<?php
/**
 * Kabar Kehutanan - BPDAS View
 * BPDAS can post and manage their own news articles
 */
?>

<div class="page-header">
    <h1><i class="fas fa-newspaper"></i> Kabar Kehutanan</h1>
    <p>Bagikan informasi kegiatan BPDAS Anda kepada masyarakat</p>
</div>

<!-- Add News Form -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle"></i> Tambah Berita</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('bpdas/store-news') ?>" enctype="multipart/form-data" id="newsForm">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

            <div class="form-group">
                <label for="title">Judul Berita <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" class="form-control"
                       placeholder="Masukkan judul berita..." required maxlength="255">
            </div>

            <div class="form-group">
                <label for="content">Konten Berita <span class="text-danger">*</span></label>
                <textarea id="content" name="content" class="form-control"
                          rows="5" placeholder="Tulis konten berita di sini..." required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Gambar (Opsional)</label>
                <div class="image-upload-area" id="imageUploadArea">
                    <input type="file" id="image" name="image" accept="image/*"
                           class="image-file-input" onchange="previewImage(this)">
                    <div class="image-upload-placeholder" id="uploadPlaceholder">
                        <i class="fas fa-camera"></i>
                        <span>Klik atau seret gambar ke sini</span>
                        <small>JPG, PNG, GIF, WebP — Maks 5 MB</small>
                    </div>
                    <img id="imagePreview" class="image-preview" src="" alt="Preview" style="display:none;">
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane"></i> Publikasikan Berita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- News Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Berita Anda (<?= count($newsList) ?>)</h3>
    </div>
    <div class="card-body p-0">
        <?php if (empty($newsList)): ?>
            <div class="empty-table-msg">
                <i class="fas fa-newspaper"></i>
                <p>Belum ada berita dari BPDAS Anda. Tambahkan berita di atas.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover mb-0" id="newsTable">
                <thead>
                    <tr>
                        <th width="60">Gambar</th>
                        <th>Judul</th>
                        <th width="110">Penulis</th>
                        <th width="110">Tanggal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($newsList as $news): ?>
                        <tr>
                            <td>
                                <?php if ($news['image_filename']): ?>
                                    <img src="<?= asset('uploads/news/' . htmlspecialchars($news['image_filename'])) ?>"
                                         alt="thumb" class="news-thumb"
                                         onerror="this.parentElement.innerHTML='<i class=\'fas fa-image text-muted\'></i>'">
                                <?php else: ?>
                                    <div class="news-thumb-placeholder"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($news['title']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($news['content'], 0, 80, '...')) ?></small>
                            </td>
                            <td><?= htmlspecialchars($news['author_name']) ?></td>
                            <td><?= formatDate($news['published_at'], 'd/m/Y') ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete-news"
                                        data-id="<?= $news['id'] ?>"
                                        data-title="<?= htmlspecialchars($news['title']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script nonce="<?= cspNonce() ?>">
function previewImage(input) {
    const preview     = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

const uploadArea = document.getElementById('imageUploadArea');
const fileInput  = document.getElementById('image');
if (uploadArea) {
    uploadArea.addEventListener('click', () => fileInput.click());
    uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
    uploadArea.addEventListener('drop', e => {
        e.preventDefault(); uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files[0]) { fileInput.files = e.dataTransfer.files; previewImage(fileInput); }
    });
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-delete-news');
    if (!btn) return;
    const id    = btn.dataset.id;
    const title = btn.dataset.title;
    if (!confirm(`Hapus berita "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;

    fetch('<?= url('bpdas/delete-news') ?>/' + id, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { btn.closest('tr').remove(); }
        else { alert(data.message || 'Gagal menghapus berita'); }
    })
    .catch(() => alert('Terjadi kesalahan jaringan'));
});
</script>

<style>
.image-upload-area {
    border: 2px dashed #ced4da; border-radius: 10px;
    padding: 2rem; text-align: center; cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative; min-height: 120px;
    display: flex; align-items: center; justify-content: center;
}
.image-upload-area:hover, .image-upload-area.drag-over { border-color: #2e7d32; background: #f1f8e9; }
.image-file-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }
.image-upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; pointer-events: none; }
.image-upload-placeholder i { font-size: 2rem; color: #adb5bd; }
.image-upload-placeholder span { font-size: 0.9rem; color: #666; }
.image-upload-placeholder small { color: #adb5bd; }
.image-preview { max-height: 200px; max-width: 100%; border-radius: 8px; object-fit: cover; }
.news-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
.news-thumb-placeholder { width: 48px; height: 48px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #ccc; }
.empty-table-msg { text-align: center; padding: 3rem; color: #aaa; }
.empty-table-msg i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
.form-footer { padding-top: 0.5rem; }
</style>
