<?php /** Admin Audit Trail Dashboard */ ?>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 font-weight-bold text-gray-800"><i class="fas fa-history text-danger mr-2"></i> Audit Trail — Penatausahaan Bibit</h2>
            <small class="text-muted">Monitoring riwayat edit seluruh proses PUB di Indonesia</small>
        </div>
        <nav>
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= url('') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('seedling-admin') ?>">PUB</a></li>
                <li class="breadcrumb-item active">Audit Trail</li>
            </ol>
        </nav>
    </div>

    <?php if ($flash = $this->getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow-sm">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Edit (Semua Waktu)</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_edits']) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-edit fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Edit Bulan Ini</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['edits_this_month']) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Request Edit Pending</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800"><?= count($pendingRequests) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Proses Paling Banyak Diedit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($stats['most_edited']) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-bar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests section (if any) -->
    <?php if (!empty($pendingRequests)): ?>
    <div class="card shadow-sm border-left-warning mb-4" style="border-left:4px solid #f6c23e;">
        <div class="card-header bg-warning text-dark py-2 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-bell mr-2"></i> Permintaan Edit Menunggu Persetujuan (<?= count($pendingRequests) ?>)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light small text-uppercase font-weight-bold">
                        <tr>
                            <th>Tanggal Request</th>
                            <th>Operator/Nursery</th>
                            <th>BPDAS</th>
                            <th>Proses</th>
                            <th>ID Record</th>
                            <th>Alasan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($pendingRequests as $req): ?>
                        <tr>
                            <td class="text-muted"><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <div class="font-weight-bold"><?= htmlspecialchars($req['requester_name'] ?? '-') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($req['nursery_name'] ?? '-') ?></small>
                            </td>
                            <td><?= htmlspecialchars($req['bpdas_name'] ?? '-') ?></td>
                            <td><span class="badge badge-secondary px-2"><?= htmlspecialchars($req['transaction_type']) ?></span></td>
                            <td><code>#<?= $req['record_id'] ?></code></td>
                            <td>
                                <span class="text-dark" style="max-width:200px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($req['reason'] ?? '') ?>">
                                    <?= htmlspecialchars(substr($req['reason'] ?? '-', 0, 60)) ?>...
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:6px;">
                                    <form action="<?= url('seedling-audit/approve-request/' . $req['id']) ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="admin_note" value="Disetujui">
                                        <button type="submit" class="btn btn-success btn-sm px-2 py-0" title="Setujui">
                                            <i class="fas fa-check"></i> Setuju
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm px-2 py-0" data-toggle="modal" data-target="#rejectModal<?= $req['id'] ?>" title="Tolak">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </div>
                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal<?= $req['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h6 class="modal-title"><i class="fas fa-times-circle mr-2"></i> Tolak Request Edit</h6>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form action="<?= url('seedling-audit/reject-request/' . $req['id']) ?>" method="POST">
                                                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCSRFToken() ?>">
                                                <div class="modal-body">
                                                    <p class="text-muted small">Alasan penolakan ini akan dilihat oleh Operator.</p>
                                                    <textarea name="admin_note" class="form-control border-danger" rows="3" required placeholder="Tulis alasan penolakan..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-times mr-1"></i> Tolak Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded py-3">
            <form action="<?= url('seedling-audit') ?>" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <?php if ($user['role'] === 'admin'): ?>
                    <div class="col-md-2">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1">BPDAS</label>
                        <select name="bpdas_id" class="form-control form-control-sm">
                            <option value="">-- Semua BPDAS --</option>
                            <?php foreach ($bpdasList as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= ($filters['bpdas_id'] ?? '') == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1">Proses</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">-- Semua Proses --</option>
                            <?php foreach ($processTypes as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($filters['transaction_type'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= $filters['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1">Sampai</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= $filters['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1">Cari Editor</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama / Proses..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-2 d-flex mt-md-0 mt-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3 mr-2 font-weight-bold"><i class="fas fa-search mr-1"></i> Filter</button>
                        <a href="<?= url('seedling-audit') ?>" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-list-ul mr-2 text-danger"></i> Riwayat Edit (<?= number_format($totalLogs) ?> total)</h6>
            <small class="text-muted">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark text-white small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Waktu Edit</th>
                            <th>Proses</th>
                            <th>Record ID</th>
                            <th>Editor</th>
                            <th>Peran</th>
                            <th>Nursery / BPDAS</th>
                            <th>Alasan Edit</th>
                            <th class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if (empty($auditLogs)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                Tidak ada riwayat edit yang ditemukan.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($auditLogs as $i => $log): 
                            $auditData = json_decode($log['audit_data'], true);
                            $reason    = $auditData['reason'] ?? '-';
                        ?>
                        <tr>
                            <td class="text-muted"><?= ($page - 1) * 20 + $i + 1 ?></td>
                            <td>
                                <div class="font-weight-bold"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                                <small class="text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-pill" style="background:#334155; color:#f1f5f9; padding: 4px 10px;">
                                    <?= htmlspecialchars($processTypes[$log['transaction_type']] ?? $log['transaction_type']) ?>
                                </span>
                            </td>
                            <td><code class="text-dark">#<?= htmlspecialchars($log['record_id']) ?></code></td>
                            <td>
                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($log['editor_name'] ?? 'Unknown') ?></div>
                            </td>
                            <td>
                                <?php
                                $roleColors = ['admin' => 'danger', 'bpdas' => 'info', 'operator_persemaian' => 'warning'];
                                $roleColor  = $roleColors[$log['editor_role'] ?? ''] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $roleColor ?> text-<?= $roleColor === 'warning' ? 'dark' : 'white' ?> px-2">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $log['editor_role'] ?? '-'))) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-dark font-weight-bold small"><?= htmlspecialchars($log['nursery_name'] ?? '-') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($log['bpdas_name'] ?? '-') ?></small>
                            </td>
                            <td>
                                <span class="d-block" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($reason) ?>">
                                    <?= htmlspecialchars(substr($reason, 0, 50)) ?><?= strlen($reason) > 50 ? '...' : '' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('seedling-audit/view-log/' . $log['id']) ?>" class="btn btn-xs btn-outline-primary px-2 py-0">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('seedling-audit') . '?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('seedling-audit') . '?' . http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('seedling-audit') . '?' . http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.opacity-50 { opacity: 0.5; }
.btn-xs { font-size: 0.75rem; padding: 2px 8px; }
</style>
