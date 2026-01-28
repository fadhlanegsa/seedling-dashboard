# Pagination Implementation Guide
## Seedling Dashboard Project

This document explains the pagination feature implementation for the Manage BPDAS page and how to apply it to other pages.

---

## Overview

The pagination system has been implemented with the following features:
- ✅ Previous/Next navigation buttons
- ✅ Smart page number display with ellipsis (...)
- ✅ Current page highlighting
- ✅ Total records count display
- ✅ Disabled state for first/last pages
- ✅ Reusable pagination component
- ✅ Responsive design

---

## Architecture

### 1. Backend (Model Layer)

**File:** `models/BPDAS.php`

The `paginate()` method handles data pagination:

```php
public function paginate($page = 1, $perPage = ITEMS_PER_PAGE) {
    $offset = ($page - 1) * $perPage;
    
    // Query with LIMIT and OFFSET
    $sql = "SELECT ... LIMIT ? OFFSET ?";
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
    
    return [
        'data' => $data,           // Array of records for current page
        'total' => $total,         // Total number of records
        'page' => $page,           // Current page number
        'perPage' => $perPage,     // Items per page
        'totalPages' => ceil($total / $perPage)  // Total pages
    ];
}
```

### 2. Controller Layer

**File:** `controllers/AdminController.php`

The controller retrieves the page parameter and passes pagination data to the view:

```php
public function bpdas() {
    $page = $this->get('page', 1);  // Get page from URL, default to 1
    
    $bpdasModel = $this->model('BPDAS');
    $result = $bpdasModel->paginate($page);
    
    $data = [
        'title' => 'Kelola BPDAS',
        'bpdas' => $result['data'],      // Records for current page
        'pagination' => $result           // Pagination metadata
    ];
    
    $this->render('admin/bpdas', $data, 'dashboard');
}
```

### 3. View Layer

**File:** `views/admin/bpdas.php`

The view uses the reusable pagination helper:

```php
<?php
// Render pagination using helper
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    renderPagination($pagination, 'admin/bpdas');
}
?>
```

### 4. Pagination Helper

**File:** `views/helpers/pagination.php`

Reusable component that generates pagination HTML:

```php
function renderPagination($pagination, $baseUrl, $queryParams = []) {
    // Generates:
    // - Previous button
    // - Page numbers with smart ellipsis
    // - Next button
    // - Pagination info (e.g., "Page 2 of 5 (Total: 45 records)")
}
```

---

## Features Explained

### 1. Smart Page Number Display

Instead of showing all page numbers (which can be overwhelming with many pages), the pagination shows:

- **First page** (always)
- **Current page ± 2 pages** (configurable range)
- **Last page** (always)
- **Ellipsis (...)** for gaps

**Examples:**

```
Current page: 1
Display: [1] 2 3 4 5 ... 20

Current page: 10
Display: 1 ... 8 9 [10] 11 12 ... 20

Current page: 20
Display: 1 ... 16 17 18 19 [20]
```

### 2. Previous/Next Buttons

- **Previous button**: Disabled on first page
- **Next button**: Disabled on last page
- Uses `&laquo;` (<<) and `&raquo;` (>>) symbols

### 3. Active Page Highlighting

The current page is highlighted with Bootstrap's `active` class.

### 4. Pagination Info

Displays helpful information below the pagination controls:
```
Menampilkan halaman 2 dari 5 (Total: 45 data)
```

---

## How to Apply to Other Pages

### Step 1: Ensure Model Has `paginate()` Method

Your model should have a `paginate()` method that returns:

```php
return [
    'data' => $records,
    'total' => $totalCount,
    'page' => $currentPage,
    'perPage' => $itemsPerPage,
    'totalPages' => $totalPages
];
```

### Step 2: Update Controller

```php
public function yourMethod() {
    $page = $this->get('page', 1);
    
    $yourModel = $this->model('YourModel');
    $result = $yourModel->paginate($page);
    
    $data = [
        'title' => 'Your Page Title',
        'records' => $result['data'],
        'pagination' => $result
    ];
    
    $this->render('your/view', $data, 'dashboard');
}
```

### Step 3: Add Pagination to View

At the bottom of your view file, add:

```php
<?php
// Render pagination
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    renderPagination($pagination, 'your/route');
}
?>
```

### Step 4: Update Row Numbers (Optional)

If you display row numbers, calculate them correctly:

```php
<?php
$currentPage = $pagination['page'] ?? 1;
$perPage = $pagination['perPage'] ?? ITEMS_PER_PAGE;
?>

<?php foreach ($records as $index => $item): ?>
    <tr>
        <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
        <!-- other columns -->
    </tr>
<?php endforeach; ?>
```

---

## Advanced Usage: Pagination with Filters

If your page has filters (e.g., search, category filter), preserve them in pagination links:

### Controller:

```php
public function yourMethod() {
    $page = $this->get('page', 1);
    $filters = [
        'status' => $this->get('status'),
        'category' => $this->get('category')
    ];
    
    $yourModel = $this->model('YourModel');
    $result = $yourModel->paginate($page, ITEMS_PER_PAGE, $filters);
    
    $data = [
        'records' => $result['data'],
        'pagination' => $result,
        'filters' => $filters  // Pass filters to view
    ];
    
    $this->render('your/view', $data, 'dashboard');
}
```

### View:

```php
<?php
// Render pagination with filters
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    renderPagination($pagination, 'your/route', $filters);
}
?>
```

The helper will automatically append filter parameters to pagination URLs:
```
/your/route?page=2&status=active&category=trees
```

---

## Customization

### Change Number of Pages Shown

Edit `views/helpers/pagination.php`:

```php
$range = 2;  // Change this to show more/fewer pages around current page
```

### Change Pagination Style

The pagination uses Bootstrap 4 classes. You can customize by:

1. **Modifying CSS** in `public/css/style.css`:
```css
.pagination .page-link {
    color: #2e7d32;  /* Custom color */
}

.pagination .page-item.active .page-link {
    background-color: #2e7d32;
    border-color: #2e7d32;
}
```

2. **Changing Button Text**:
Edit `views/helpers/pagination.php` to change "Previous" and "Next" text.

---

## Testing

### Test Cases:

1. **Single Page** (≤10 records):
   - Pagination should not appear

2. **Multiple Pages** (>10 records):
   - Previous button disabled on page 1
   - Next button disabled on last page
   - Page numbers display correctly
   - Ellipsis appears for gaps

3. **Navigation**:
   - Click Previous/Next buttons
   - Click specific page numbers
   - Verify correct data loads

4. **Edge Cases**:
   - Navigate to page 1
   - Navigate to last page
   - Try invalid page numbers (should default to page 1)

### Manual Testing:

1. Go to: `http://localhost/seedling-dashboard/public/admin/bpdas`
2. If you have >10 BPDAS records, pagination should appear
3. Test all navigation buttons
4. Verify row numbers are correct on each page

---

## Troubleshooting

### Pagination Not Showing

**Problem:** Pagination controls don't appear

**Solutions:**
1. Check if `$pagination` variable is set in view
2. Verify `totalPages > 1` in pagination data
3. Ensure model's `paginate()` method returns correct structure

### Wrong Page Numbers

**Problem:** Row numbers restart at 1 on each page

**Solution:** Use correct calculation:
```php
$index + 1 + (($currentPage - 1) * $perPage)
```

### Filters Not Preserved

**Problem:** Filters reset when changing pages

**Solution:** Pass filters to `renderPagination()`:
```php
renderPagination($pagination, 'your/route', $filters);
```

### Styling Issues

**Problem:** Pagination looks broken

**Solutions:**
1. Ensure Bootstrap CSS is loaded
2. Check for CSS conflicts
3. Verify pagination helper is included correctly

---

## Pages with Pagination

Currently implemented:
- ✅ Admin > Kelola BPDAS (`/admin/bpdas`)
- ✅ Admin > Kelola Jenis Bibit (`/admin/seedling-types`)
- ✅ Admin > Kelola Permintaan (`/admin/requests`)
- ✅ Admin > Kelola Pengguna (`/admin/users`)

Can be easily added to:
- BPDAS > Kelola Stok
- BPDAS > Kelola Permintaan
- Public > My Requests
- Any other list pages

---

## Summary

The pagination system provides:
- ✅ **User-friendly navigation** with Previous/Next buttons
- ✅ **Smart page display** that handles large page counts
- ✅ **Reusable component** for consistency across pages
- ✅ **Filter preservation** for search/filter functionality
- ✅ **Responsive design** that works on all devices
- ✅ **Accessible** with proper ARIA labels

The implementation follows MVC pattern and can be easily extended to other pages in the application.
