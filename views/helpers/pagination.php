<?php
/**
 * Pagination Component
 * Reusable pagination UI for all pages
 * 
 * Usage:
 * include 'views/helpers/pagination.php';
 * renderPagination($pagination, 'admin/bpdas');
 */

/**
 * Render pagination controls
 * 
 * @param array $pagination Pagination data (page, totalPages, total, perPage)
 * @param string $baseUrl Base URL for pagination links (e.g., 'admin/bpdas')
 * @param array $queryParams Additional query parameters to preserve
 */
function renderPagination($pagination, $baseUrl, $queryParams = []) {
    $currentPage = $pagination['page'] ?? 1;
    $totalPages = $pagination['totalPages'] ?? 1;
    $total = $pagination['total'] ?? 0;
    
    // Don't show pagination if only 1 page
    if ($totalPages <= 1) {
        return;
    }
    
    // Build query string from additional parameters
    $queryString = '';
    if (!empty($queryParams)) {
        $params = [];
        foreach ($queryParams as $key => $value) {
            if ($value !== null && $value !== '') {
                $params[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        if (!empty($params)) {
            $queryString = '&' . implode('&', $params);
        }
    }
    
    ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <?php if ($currentPage > 1): ?>
                    <a class="page-link" href="<?= url($baseUrl . '?page=' . ($currentPage - 1) . $queryString) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span> Previous
                    </a>
                <?php else: ?>
                    <span class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span> Previous
                    </span>
                <?php endif; ?>
            </li>
            
            <?php
            // Smart pagination: show first page, last page, current page and 2 pages around it
            $range = 2; // Number of pages to show around current page
            $showPages = [];
            
            // Always show first page
            $showPages[] = 1;
            
            // Show pages around current page
            for ($i = max(2, $currentPage - $range); $i <= min($totalPages - 1, $currentPage + $range); $i++) {
                $showPages[] = $i;
            }
            
            // Always show last page
            if ($totalPages > 1) {
                $showPages[] = $totalPages;
            }
            
            // Remove duplicates and sort
            $showPages = array_unique($showPages);
            sort($showPages);
            
            // Display pages with ellipsis
            $prevPage = 0;
            foreach ($showPages as $pageNum):
                // Show ellipsis if there's a gap
                if ($pageNum - $prevPage > 1):
            ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php
                endif;
                $prevPage = $pageNum;
            ?>
                <li class="page-item <?= ($pageNum == $currentPage) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url($baseUrl . '?page=' . $pageNum . $queryString) ?>">
                        <?= $pageNum ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <!-- Next Button -->
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <?php if ($currentPage < $totalPages): ?>
                    <a class="page-link" href="<?= url($baseUrl . '?page=' . ($currentPage + 1) . $queryString) ?>" aria-label="Next">
                        Next <span aria-hidden="true">&raquo;</span>
                    </a>
                <?php else: ?>
                    <span class="page-link" aria-label="Next">
                        Next <span aria-hidden="true">&raquo;</span>
                    </span>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
    
    <!-- Pagination Info -->
    <div class="text-center text-muted mt-2 mb-4">
        <small>
            Menampilkan halaman <?= $currentPage ?> dari <?= $totalPages ?> 
            (Total: <?= number_format($total) ?> data)
        </small>
    </div>
    <?php
}
