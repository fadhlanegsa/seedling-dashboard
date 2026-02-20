<?php
/**
 * Operator - Request Detail View
 */
?>

<div class="page-header">
    <h1><i class="fas fa-file-alt"></i> Detail Permintaan</h1>
    <a href="<?= url('operator/requests') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Informasi Permintaan</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">No. Permintaan:</th>
                        <td><strong><?= htmlspecialchars($request['request_number'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $status = $request['status'] ?? 'pending';
                            $statusClass = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'completed' => 'info',
                                'delivered' => 'purple'
                            ];
                            $class = $statusClass[$status] ?? 'secondary';
                            ?>
                            <span class="badge badge-<?= $class ?>">
                                <?= REQUEST_STATUS[$status] ?? $status ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Permintaan:</th>
                        <td><?= isset($request['created_at']) ? formatDate($request['created_at'], DATETIME_FORMAT) : '-' ?></td>
                    </tr>
                    <?php if (!empty($request['items'])): ?>
                        <tr>
                            <th>Daftar Bibit:</th>
                            <td class="p-0">
                                <table class="table table-sm table-bordered m-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Jenis Bibit</th>
                                            <th class="text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($request['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['seedling_name'] ?? 'Item #' . $item['seedling_type_id']) ?></td>
                                            <td class="text-right"><?= formatNumber($item['quantity']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-light">
                                            <th>Total</th>
                                            <th class="text-right"><?= formatNumber($request['total_quantity'] ?? 0) ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th>Jenis Bibit:</th>
                            <td><strong><?= htmlspecialchars($request['seedling_name'] ?? 'Permintaan Multi-Item') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Jumlah Diminta:</th>
                            <td><strong><?= formatNumber($request['quantity'] ?? 0) ?></strong> bibit</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Tujuan Penggunaan:</th>
                        <td><?= htmlspecialchars($request['purpose'] ?? '-') ?></td>
                    </tr>
                    <?php if (!empty($request['land_area'])): ?>
                    <tr>
                        <th>Luas Lahan:</th>
                        <td><?= formatLandArea($request['land_area']) ?> Ha</td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['planting_address'])): ?>
                    <tr>
                        <th>Alamat Tanam:</th>
                        <td><?= nl2br(htmlspecialchars($request['planting_address'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['proposal_file_path'])): ?>
                    <tr>
                        <th>Surat Pengajuan:</th>
                        <td>
                            <a href="<?= url('uploads/' . $request['proposal_file_path']) ?>" 
                               target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf"></i> Download Proposal (PDF)
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['approval_letter_path'])): ?>
                    <tr>
                        <th>Surat Persetujuan:</th>
                        <td>
                            <a href="<?= url('uploads/' . $request['approval_letter_path']) ?>" 
                               target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-file-pdf"></i> Download Surat Persetujuan (PDF)
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($request['notes'])): ?>
                    <tr>
                        <th>Catatan:</th>
                        <td><?= nl2br(htmlspecialchars($request['notes'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <?php if (($request['status'] ?? '') === 'approved' || ($request['status'] ?? '') === 'delivered'): ?>
        <!-- Delivery Photo Upload -->
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h3><i class="fas fa-camera"></i> Foto Bukti Serah Terima</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($request['delivery_photo_path'])): ?>
                    <!-- Show existing photo -->
                    <div class="mb-3">
                        <h6>Foto yang sudah diupload:</h6>
                        <a href="<?= url('uploads/' . $request['delivery_photo_path']) ?>" target="_blank">
                            <img src="<?= url('uploads/' . $request['delivery_photo_path']) ?>" 
                                 alt="Bukti Serah Terima" 
                                 class="img-thumbnail" 
                                 style="max-width: 400px; cursor: pointer;">
                        </a>
                        <p class="text-muted mt-2">
                            <small>Klik gambar untuk melihat ukuran penuh</small>
                        </p>
                    </div>
                <?php else: ?>
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Silakan upload foto bukti serah terima bibit kepada pemohon untuk menyelesaikan permintaan ini.
                    </p>
                <?php endif; ?>
                
                <form id="photoUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="request_id" value="<?= $request['id'] ?? '' ?>">
                    
                    <div class="form-group">
                        <label>Pilih Foto:</label>
                        <input type="file" name="photo" id="photoInput" class="form-control" 
                               accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-image"></i> Format: JPEG, PNG, GIF, WebP | 
                            Maksimal 10 MB (akan dikompres otomatis ke WebP &lt;500KB)
                        </small>
                    </div>
                    
                    <!-- Preview -->
                    <div id="photoPreview" style="display: none;" class="mb-3">
                        <h6>Preview:</h6>
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                    
                    <button type="submit" class="btn btn-success" id="uploadBtn">
                        <i class="fas fa-upload"></i> Upload Foto & Selesaikan
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($history)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Riwayat</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($history as $item): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong><?= REQUEST_STATUS[$item['status'] ?? 'pending'] ?? ($item['status'] ?? '-') ?></strong>
                                <?php if (!empty($item['notes'])): ?>
                                    <p><?= nl2br(htmlspecialchars($item['notes'])) ?></p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <?= isset($item['created_at']) ? formatDate($item['created_at'], DATETIME_FORMAT) : '-' ?>
                                    <?php if (!empty($item['user_name'])): ?>
                                        oleh <?= htmlspecialchars($item['user_name']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Data Pemohon</h3>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong><br><?= htmlspecialchars($request['requester_name'] ?? '-') ?></p>
                <p><strong>Email:</strong><br><?= htmlspecialchars($request['requester_email'] ?? '-') ?></p>
                <p><strong>Telepon:</strong><br><?= htmlspecialchars($request['requester_phone'] ?? '-') ?></p>
                <p><strong>NIK:</strong><br><?= htmlspecialchars($request['requester_nik'] ?? '-') ?></p>
                <?php if (!empty($request['requester_address'])): ?>
                <p><strong>Alamat:</strong><br><?= nl2br(htmlspecialchars($request['requester_address'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// ===== PHOTO UPLOAD LOGIC =====
const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('photoPreview');
const previewImage = document.getElementById('previewImage');
const photoUploadForm = document.getElementById('photoUploadForm');

// Show preview when file is selected
if (photoInput) {
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('File harus berupa gambar (JPEG, PNG, GIF, atau WebP)');
                this.value = '';
                photoPreview.style.display = 'none';
                return;
            }
            
            // Validate file size (max 10MB)
            const maxSize = 10485760; // 10MB
            if (file.size > maxSize) {
                const sizeMB = (file.size / 1048576).toFixed(2);
                alert(`Ukuran file terlalu besar (${sizeMB} MB). Maksimal 10 MB`);
                this.value = '';
                photoPreview.style.display = 'none';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                photoPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            photoPreview.style.display = 'none';
        }
    });
}

// Handle photo upload form submission
if (photoUploadForm) {
    photoUploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const uploadBtn = document.getElementById('uploadBtn');
        const originalText = uploadBtn.innerHTML;
        
        // Disable button and show loading
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload & Mengompres...';
        
        const formData = new FormData(this);
        
        fetch('<?= url('operator/uploadDeliveryPhoto') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupload foto: ' + error.message);
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = originalText;
        });
    });
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary-color);
    border: 2px solid var(--white);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    bottom: -20px;
    width: 2px;
    background: var(--border-color);
}

.timeline-item:last-child::before {
    display: none;
}

.badge-purple {
    background-color: #9b59b6;
    color: white;
}
</style>
