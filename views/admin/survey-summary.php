<?php
/**
 * Admin/BPDAS - Rekap Survei Kepuasan Pelanggan
 */
$surveyRoute = (($user ?? currentUser())['role'] ?? '') === 'bpdas' ? 'bpdas/survey-summary' : 'admin/survey-summary';
?>

<div class="page-header">
    <h1><i class="fas fa-poll"></i> Rekap Survei Kepuasan Pelanggan</h1>
</div>

<!-- Overall Stats -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="mb-0"><?= number_format($overallStats['average'] ?? 0, 2) ?> <i class="fas fa-star text-warning"></i></h2>
                <p class="text-muted mb-0">Rata-rata Rating</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="mb-0"><?= formatNumber($overallStats['total'] ?? 0) ?></h2>
                <p class="text-muted mb-0">Total Ulasan Masuk</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="mb-1 font-weight-bold">Distribusi Rating</p>
                <?php for ($star = 5; $star >= 1; $star--): ?>
                    <?php
                        $count = $distribution[$star] ?? 0;
                        $total = max(1, $overallStats['total'] ?? 0);
                        $percent = round(($count / $total) * 100);
                    ?>
                    <div class="d-flex align-items-center mb-1" style="font-size: 0.85rem;">
                        <span style="width: 45px;"><?= $star ?> <i class="fas fa-star text-warning"></i></span>
                        <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: <?= $percent ?>%"></div>
                        </div>
                        <span style="width: 30px;" class="text-right"><?= $count ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?= url($surveyRoute) ?>" class="filter-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-control">
                            <option value="">Semua Rating</option>
                            <?php for ($star = 5; $star >= 1; $star--): ?>
                                <option value="<?= $star ?>" <?= ($filters['rating'] == $star) ? 'selected' : '' ?>>
                                    <?= $star ?> Bintang
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">BPDAS</label>
                        <select name="bpdas_id" class="form-control">
                            <option value="">Semua BPDAS</option>
                            <?php foreach ($bpdasList as $bpdas): ?>
                                <option value="<?= $bpdas['id'] ?>" <?= ($filters['bpdas_id'] == $bpdas['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bpdas['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?= url($surveyRoute) ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Survey Table -->
<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Permintaan</th>
                    <th>Pemohon</th>
                    <th>BPDAS</th>
                    <th>Rating</th>
                    <th>Ulasan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($surveys)): ?>
                    <?php
                    $currentPage = $pagination['page'] ?? 1;
                    $perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
                    ?>
                    <?php foreach ($surveys as $index => $survey): ?>
                        <tr>
                            <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                            <td><strong><?= htmlspecialchars($survey['request_number'] ?? '-') ?></strong></td>
                            <td><?= htmlspecialchars($survey['user_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($survey['bpdas_name'] ?? '-') ?></td>
                            <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa<?= $i <= (int)$survey['rating'] ? 's' : 'r' ?> fa-star text-warning"></i>
                                <?php endfor; ?>
                            </td>
                            <td><?= htmlspecialchars($survey['comment'] ?? '-') ?></td>
                            <td><?= isset($survey['created_at']) ? formatDate($survey['created_at'], DATETIME_FORMAT) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data survei</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';

    $queryParams = [
        'rating'   => $filters['rating'] ?? null,
        'bpdas_id' => $filters['bpdas_id'] ?? null
    ];

    renderPagination($pagination, $surveyRoute, $queryParams);
}
?>
