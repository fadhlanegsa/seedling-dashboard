<?php
/**
 * Kabar Kehutanan - Admin View
 * Admin can see all news (pusat + bpdas) and add new articles
 */
?>

<div class="page-header">
    <h1><i class="fas fa-newspaper"></i> Kabar Kehutanan</h1>
    <p>Kelola berita kehutanan dari Pusat, BPDAS, dan BPTH</p>
</div>

<!-- Add News Form -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle"></i> Tambah Berita</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/store-news') ?>" enctype="multipart/form-data" id="newsForm">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="title">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control"
                           placeholder="Masukkan judul berita..." required maxlength="255">
                </div>
                <div class="form-group col-md-4">
                    <label>Sumber Berita <span class="text-danger">*</span></label>
                    <div class="source-radio-group">
                        <label class="source-radio-label">
                            <input type="radio" name="source_type" value="pusat" checked>
                            <span class="radio-custom pusat">
                                <i class="fas fa-landmark"></i> Pusat
                            </span>
                        </label>
                        <label class="source-radio-label">
                            <input type="radio" name="source_type" value="bpdas">
                            <span class="radio-custom bpdas">
                                <i class="fas fa-water"></i> BPDAS
                            </span>
                        </label>
                        <label class="source-radio-label">
                            <input type="radio" name="source_type" value="bpth">
                            <span class="radio-custom bpth">
                                <i class="fas fa-tree"></i> BPTH
                            </span>
                        </label>
                    </div>
                </div>
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-list"></i> Semua Berita (<?= count($newsList) ?>)</h3>
    </div>
    <div class="card-body p-0">
        <?php if (empty($newsList)): ?>
            <div class="empty-table-msg">
                <i class="fas fa-newspaper"></i>
                <p>Belum ada berita. Tambahkan berita pertama di atas.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover mb-0" id="newsTable">
                <thead>
                    <tr>
                        <th width="60">Gambar</th>
                        <th>Judul</th>
                        <th width="100">Sumber</th>
                        <th width="140">BPDAS</th>
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
                                         onerror="this.src='';this.parentElement.innerHTML='<i class=\'fas fa-image text-muted\'></i>'">
                                <?php else: ?>
                                    <div class="news-thumb-placeholder"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($news['title']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($news['content'], 0, 80, '...')) ?></small>
                            </td>
                            <td>
                                <?php if ($news['source_type'] === 'pusat'): ?>
                                    <span class="badge badge-pusat">Pusat</span>
                                <?php else: ?>
                                    <span class="badge badge-bpdas">BPDAS</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $news['bpdas_name'] ? htmlspecialchars($news['bpdas_name']) : '<span class="text-muted">-</span>' ?></td>
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
// Image preview
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
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

// Drag & drop
const uploadArea = document.getElementById('imageUploadArea');
const fileInput  = document.getElementById('image');
if (uploadArea) {
    uploadArea.addEventListener('click', () => fileInput.click());
    uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
    uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files[0]) {
            fileInput.files = e.dataTransfer.files;
            previewImage(fileInput);
        }
    });
}

// Delete news
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-delete-news');
    if (!btn) return;
    const id    = btn.dataset.id;
    const title = btn.dataset.title;
    if (!confirm(`Hapus berita "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;

    fetch('<?= url('admin/delete-news') ?>/' + id, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.closest('tr').remove();
        } else {
            alert(data.message || 'Gagal menghapus berita');
        }
    })
    .catch(() => alert('Terjadi kesalahan jaringan'));
});
</script>

<style>
/* Source radio */
.source-radio-group { display: flex; gap: 0.75rem; }
.source-radio-label { cursor: pointer; }
.source-radio-label input[type=radio] { display: none; }
.radio-custom {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.45rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
    border: 2px solid #dee2e6; color: #666; transition: all 0.15s ease; cursor: pointer;
}
.source-radio-label input:checked + .radio-custom.pusat { border-color: #1565c0; color: #1565c0; background: #e3f2fd; }
.source-radio-label input:checked + .radio-custom.bpdas { border-color: #1b5e20; color: #1b5e20; background: #e8f5e9; }
.source-radio-label input:checked + .radio-custom.bpth  { border-color: #33691e; color: #33691e; background: #f1f8e9; }

/* Image upload */
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

/* Thumb */
.news-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
.news-thumb-placeholder { width: 48px; height: 48px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #ccc; }

/* Badges */
.badge-pusat { background: #1565c0; color: #fff; padding: 0.3em 0.65em; border-radius: 6px; font-size: 0.75rem; }
.badge-bpdas  { background: #1b5e20; color: #fff; padding: 0.3em 0.65em; border-radius: 6px; font-size: 0.75rem; }
.badge-bpth   { background: #33691e; color: #fff; padding: 0.3em 0.65em; border-radius: 6px; font-size: 0.75rem; }

.empty-table-msg { text-align: center; padding: 3rem; color: #aaa; }
.empty-table-msg i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }

.form-footer { padding-top: 0.5rem; }
</style>
