<?php
/**
 * View: Kamerha Sync Log
 * Halaman monitoring log sinkronisasi dengan Kamerha
 */
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 1rem;">
    <div>
        <h1><i class="fas fa-satellite-dish" style="color: #00b894;"></i> Integrasi Kamerha</h1>
        <p class="text-muted mb-0">Monitor sinkronisasi data QR Index & Geotag dengan sistem Kamerha</p>
    </div>
    <div class="d-flex align-items-center" style="gap: 0.5rem; flex-wrap: wrap;">
        <button id="btn-sync-all" class="btn btn-success" onclick="syncAll()">
            <i class="fas fa-sync-alt"></i> Sinkronisasi Semua Geotag
        </button>
        <a href="<?= url('kamerha/explore-schema') ?>" class="btn btn-outline-info btn-sm" target="_blank">
            <i class="fas fa-database"></i> Explore Schema API
        </a>
        <a href="<?= url('kamerha/sync-log') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-redo"></i> Refresh
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="kamerha-stats-grid mb-4">
    <div class="kamerha-stat-card" style="border-left: 4px solid #6c5ce7;">
        <div class="ksc-icon" style="background: #6c5ce7;"><i class="fas fa-exchange-alt"></i></div>
        <div class="ksc-content">
            <h3><?= number_format($stats['total'] ?? 0) ?></h3>
            <p>Total Operasi Sync</p>
        </div>
    </div>
    <div class="kamerha-stat-card" style="border-left: 4px solid #00b894;">
        <div class="ksc-icon" style="background: #00b894;"><i class="fas fa-check-circle"></i></div>
        <div class="ksc-content">
            <h3><?= number_format($stats['success'] ?? 0) ?></h3>
            <p>Berhasil</p>
        </div>
    </div>
    <div class="kamerha-stat-card" style="border-left: 4px solid #d63031;">
        <div class="ksc-icon" style="background: #d63031;"><i class="fas fa-times-circle"></i></div>
        <div class="ksc-content">
            <h3><?= number_format($stats['failed'] ?? 0) ?></h3>
            <p>Gagal</p>
        </div>
    </div>
    <div class="kamerha-stat-card" style="border-left: 4px solid #0984e3;">
        <div class="ksc-icon" style="background: #0984e3;"><i class="fas fa-upload"></i></div>
        <div class="ksc-content">
            <h3><?= number_format($stats['pushes'] ?? 0) ?></h3>
            <p>Push QR Index</p>
        </div>
    </div>
    <div class="kamerha-stat-card" style="border-left: 4px solid #fdcb6e;">
        <div class="ksc-icon" style="background: #fdcb6e;"><i class="fas fa-download"></i></div>
        <div class="ksc-content">
            <h3><?= number_format($stats['pulls'] ?? 0) ?></h3>
            <p>Pull Geotag</p>
        </div>
    </div>
</div>

<!-- Alert Koneksi Sukses -->
<div class="alert alert-success d-flex align-items-start mb-4" style="border-radius: 8px; border-left: 4px solid #00b894; background: #e6fffa; color: #006b54; border-color: #b2f5ea;">
    <i class="fas fa-check-circle fa-lg mt-1 mr-3" style="color: #00b894; flex-shrink: 0;"></i>
    <div>
        <strong>✅ Koneksi API Kamerha Aktif:</strong><br>
        Sistem backend bibitgratis.com telah berhasil terhubung dengan API PostgREST Kamerha menggunakan <strong>JWT HS256 Self-Signed Token</strong>. Data indeks dipetakan secara dinamis ke tabel masing-masing BPDAS wilayah.
        <br><small class="text-muted">Status: Operasional | Mode Autentikasi: JWT HS256 & API-Key Gateway</small>
    </div>
</div>

<!-- Manual Test Panel -->
<div class="card mb-4" style="border: 1px solid #6c5ce7;">
    <div class="card-header" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: white;">
        <h5 class="mb-0"><i class="fas fa-flask"></i> Panel Test Manual</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold">Test Push QR Index</label>
                <div class="input-group">
                    <input type="number" id="test-request-id" class="form-control" placeholder="Request ID (contoh: 42)">
                    <input type="text" id="test-index-code" class="form-control" placeholder="Index Code (PE-54-12-3-7-260415-1)">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="testPushQr()"><i class="fas fa-paper-plane"></i> Push</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold">Test Sync Geotag</label>
                <div class="input-group">
                    <input type="number" id="test-sync-request-id" class="form-control" placeholder="Request ID">
                    <div class="input-group-append">
                        <button class="btn btn-success" onclick="testSyncGeotag()"><i class="fas fa-map-marker-alt"></i> Pull Geotag</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="test-result" class="mt-2" style="display:none;">
            <pre id="test-result-body" class="bg-dark text-white p-3 rounded" style="max-height: 300px; overflow-y: auto; font-size: 12px;"></pre>
        </div>
    </div>
</div>

<!-- Log Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-history"></i> Log Sinkronisasi Terbaru</h3>
        <small class="text-muted">Total: <?= number_format($total ?? 0) ?> entri</small>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($logs)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0" style="font-size: 0.875rem;">
                <thead class="thead-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Request</th>
                        <th>Index Code</th>
                        <th>Status</th>
                        <th>HTTP</th>
                        <th>Oleh</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><small><?= date('d/m H:i', strtotime($log['created_at'])) ?></small></td>
                        <td>
                            <?php if ($log['sync_type'] === 'push_qr'): ?>
                                <span class="badge" style="background: #0984e3; color: white;"><i class="fas fa-upload"></i> Push QR</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fdcb6e; color: #2d3436;"><i class="fas fa-download"></i> Pull Geo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['request_number']): ?>
                                <a href="<?= url('bpdas/requestDetail/' . $log['request_id']) ?>" class="text-primary">
                                    <small><?= htmlspecialchars($log['request_number']) ?></small>
                                </a>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code style="font-size: 10px; color: #6c5ce7;"><?= htmlspecialchars(substr($log['index_code'] ?? '-', 0, 25)) ?></code>
                        </td>
                        <td>
                            <?php if ($log['status'] === 'success'): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> OK</span>
                            <?php elseif ($log['status'] === 'failed'): ?>
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Gagal</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Sebagian</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $hc = (int)($log['http_code'] ?? 0); ?>
                            <span class="badge <?= $hc >= 200 && $hc < 300 ? 'badge-success' : ($hc >= 400 ? 'badge-danger' : 'badge-secondary') ?>">
                                <?= $hc ?: '-' ?>
                            </span>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($log['synced_by_name'] ?? 'Cronjob') ?></small></td>
                        <td>
                            <?php if ($log['error_message']): ?>
                                <button class="btn btn-xs btn-outline-danger" 
                                        onclick="showDetail(this)" 
                                        data-detail="<?= htmlspecialchars($log['error_message']) ?>">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            <?php elseif ($log['response_body']): ?>
                                <button class="btn btn-xs btn-outline-secondary" 
                                        onclick="showDetail(this)" 
                                        data-detail="<?= htmlspecialchars(substr($log['response_body'], 0, 500)) ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-satellite-dish fa-4x mb-3" style="color: #dfe6e9;"></i>
            <h5 class="text-muted">Belum ada log sinkronisasi</h5>
            <p class="text-muted">Log akan muncul di sini setelah pertama kali sync dijalankan.</p>
        </div>
        <?php endif; ?>
    </div>
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination pagination-sm mb-0 justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Detail Log</h5>
                <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <pre id="modal-detail-content" class="bg-dark text-white p-3 rounded" style="font-size: 12px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
</div>

<style>
.kamerha-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}
.kamerha-stat-card {
    background: #fff;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.ksc-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.ksc-content h3 { margin: 0; font-size: 1.5rem; color: #2d3436; }
.ksc-content p  { margin: 0; font-size: 0.78rem; color: #636e72; }

.btn-xs {
    padding: 2px 6px;
    font-size: 11px;
    border-radius: 4px;
}
#btn-sync-all .fa-sync-alt { transition: transform 0.5s; }
#btn-sync-all.loading .fa-sync-alt { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
const KAMERHA_PUSH_URL  = '<?= url('kamerha/push-qr') ?>';
const KAMERHA_SYNC_URL  = '<?= url('kamerha/sync-geotag') ?>';
const KAMERHA_SYNC_ALL  = '<?= url('kamerha/sync-all') ?>';

function showResult(data, isError = false) {
    const el = document.getElementById('test-result');
    const body = document.getElementById('test-result-body');
    el.style.display = 'block';
    body.style.borderLeft = isError ? '3px solid #d63031' : '3px solid #00b894';
    body.textContent = JSON.stringify(data, null, 2);
}

function showDetail(btn) {
    const detail = btn.getAttribute('data-detail');
    document.getElementById('modal-detail-content').textContent = detail;
    $('#detailModal').modal('show');
}

async function testPushQr() {
    const requestId = document.getElementById('test-request-id').value;
    const indexCode = document.getElementById('test-index-code').value;
    if (!requestId || !indexCode) {
        alert('Isi Request ID dan Index Code terlebih dahulu');
        return;
    }
    try {
        const res = await fetch(KAMERHA_PUSH_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ request_id: parseInt(requestId), index_code: indexCode })
        });
        const data = await res.json();
        showResult(data, !data.success);
        if (data.success) setTimeout(() => location.reload(), 2000);
    } catch (e) {
        showResult({ error: e.message }, true);
    }
}

async function testSyncGeotag() {
    const requestId = document.getElementById('test-sync-request-id').value;
    if (!requestId) {
        alert('Isi Request ID terlebih dahulu');
        return;
    }
    try {
        const res = await fetch(KAMERHA_SYNC_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ request_id: parseInt(requestId) })
        });
        const data = await res.json();
        showResult(data, !data.success);
        if (data.success) setTimeout(() => location.reload(), 2000);
    } catch (e) {
        showResult({ error: e.message }, true);
    }
}

async function syncAll() {
    const btn = document.getElementById('btn-sync-all');
    btn.classList.add('loading');
    btn.disabled = true;
    try {
        const res = await fetch(KAMERHA_SYNC_ALL);
        const data = await res.json();
        showResult(data, !data.success);
        alert(data.message || 'Selesai!');
        if (data.success) setTimeout(() => location.reload(), 1500);
    } catch (e) {
        alert('Error: ' + e.message);
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}
</script>
