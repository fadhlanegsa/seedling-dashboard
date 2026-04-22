<?php /** Audit Log Detail View — JSON Diff */ ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="font-weight-bold text-gray-800"><i class="fas fa-search-plus text-danger mr-2"></i> Detail Audit Log #<?= $log['id'] ?></h4>
                <a href="<?= url('seedling-audit') ?>" class="btn btn-sm btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
            </div>

            <!-- Meta Info Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Waktu Edit</div>
                            <div class="font-weight-bold text-dark"><?= date('d F Y, H:i:s', strtotime($log['created_at'])) ?></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Proses</div>
                            <span class="badge badge-pill px-3 py-1" style="background:#334155; color:#f1f5f9; font-size:0.8rem;">
                                <?= htmlspecialchars($log['transaction_type']) ?>
                            </span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Record ID</div>
                            <code class="text-dark">#<?= $log['record_id'] ?></code>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Editor</div>
                            <div class="font-weight-bold text-dark"><?= htmlspecialchars($log['editor_name'] ?? '-') ?></div>
                            <small class="text-muted"><?= htmlspecialchars(ucfirst(str_replace('_',' ', $log['editor_role'] ?? ''))) ?> · <?= htmlspecialchars($log['nursery_name'] ?? '') ?></small>
                        </div>
                    </div>

                    <!-- Reason highlight -->
                    <div class="alert alert-warning border-0 d-flex align-items-start mt-2" style="background:#fff9e6; border-left:4px solid #f6c23e !important;">
                        <i class="fas fa-clipboard-list mr-3 mt-1 text-warning"></i>
                        <div>
                            <div class="font-weight-bold text-dark mb-1">Alasan Edit</div>
                            <div><?= nl2br(htmlspecialchars($log['audit_parsed']['reason'] ?? 'Tidak ada alasan tercatat.')) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diff Table: Old vs New -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-code-branch mr-2"></i> Perbandingan Data (Sebelum → Sesudah)</h6>
                </div>
                <div class="card-body p-0">
                    <?php
                    $oldData = $log['audit_parsed']['old_data'] ?? [];
                    $newData = $log['audit_parsed']['new_data'] ?? [];
                    $allKeys = array_unique(array_merge(array_keys((array)$oldData), array_keys((array)$newData)));
                    // Skip system/tracking fields for cleaner view
                    $skipKeys = ['updated_at', 'updated_by', 'created_at', 'created_by', 'bpdas_id', 'nursery_id'];
                    $displayKeys = array_filter($allKeys, fn($k) => !in_array($k, $skipKeys));
                    ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="bg-light small text-uppercase font-weight-bold">
                                <tr>
                                    <th style="width:25%;">Field</th>
                                    <th style="width:37.5%; background:#fff5f5;">Sebelum (Old)</th>
                                    <th style="width:37.5%; background:#f0fff4;">Sesudah (New)</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach ($displayKeys as $key): 
                                    $oldVal = $oldData[$key] ?? null;
                                    $newVal = $newData[$key] ?? null;
                                    $changed = $oldVal != $newVal;
                                ?>
                                <tr <?= $changed ? 'class="table-active"' : '' ?>>
                                    <td class="font-weight-bold text-muted" style="vertical-align:middle;">
                                        <?= htmlspecialchars(str_replace('_', ' ', $key)) ?>
                                        <?php if ($changed): ?><span class="badge badge-warning ml-1 text-dark">Berubah</span><?php endif; ?>
                                    </td>
                                    <td style="background:<?= $changed ? '#fff5f5' : 'transparent' ?>; vertical-align:middle;">
                                        <?php if (is_array($oldVal)): ?>
                                            <code><?= htmlspecialchars(json_encode($oldVal, JSON_UNESCAPED_UNICODE)) ?></code>
                                        <?php else: ?>
                                            <span class="<?= $changed ? 'text-danger font-weight-bold' : '' ?>"><?= htmlspecialchars((string)($oldVal ?? '—')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="background:<?= $changed ? '#f0fff4' : 'transparent' ?>; vertical-align:middle;">
                                        <?php if (is_array($newVal)): ?>
                                            <code><?= htmlspecialchars(json_encode($newVal, JSON_UNESCAPED_UNICODE)) ?></code>
                                        <?php else: ?>
                                            <span class="<?= $changed ? 'text-success font-weight-bold' : '' ?>"><?= htmlspecialchars((string)($newVal ?? '—')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Raw JSON (collapsible) -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-2">
                    <button class="btn btn-link p-0 font-weight-bold text-muted text-decoration-none" type="button" data-toggle="collapse" data-target="#rawJson">
                        <i class="fas fa-code mr-1"></i> Lihat Raw JSON Audit Data
                    </button>
                </div>
                <div class="collapse" id="rawJson">
                    <div class="card-body bg-dark">
                        <pre class="text-success mb-0" style="font-size:0.75rem; max-height:400px; overflow-y:auto;"><?= htmlspecialchars(json_encode($log['audit_parsed'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.table-active td, .table-active th { background-color: rgba(255, 193, 7, 0.05) !important; }
</style>
