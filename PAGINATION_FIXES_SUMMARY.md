# Pagination and Array Key Fixes Summary
## Seedling Dashboard Project

This document summarizes all fixes applied to resolve pagination issues and "Undefined array key" warnings across the application.

---

## Issues Fixed

### 1. ✅ Undefined Array Key Warnings
**Problem:** Views were accessing array keys without checking if they exist first, causing PHP warnings.

**Solution:** Added null coalescing operators (`??`) to safely access array keys with fallback values.

### 2. ✅ Missing Pagination on Stock Page
**Problem:** Admin stock management page had no pagination, showing all records at once.

**Solution:** Implemented pagination with Previous/Next buttons and smart page number display.

### 3. ✅ Pagination Data Structure Mismatch
**Problem:** Views expected different key names than what models returned.

**Solution:** Standardized pagination data structure across all models and views.

---

## Files Modified

### 1. **views/admin/stock.php**
**Changes:**
- Added null coalescing operators for all array accesses
- Fixed `seedling_name`, `bpdas_name`, `province_name`, `category`, `scientific_name`
- Added pagination component at bottom
- Preserved filter parameters in pagination URLs
- Fixed row numbering to continue across pages

**Before:**
```php
<td><?= htmlspecialchars($item['seedling_name']) ?></td>
```

**After:**
```php
<td><?= htmlspecialchars($item['seedling_name'] ?? '-') ?></td>
```

### 2. **controllers/AdminController.php**
**Method:** `stock()`

**Changes:**
- Added `$page` parameter retrieval
- Changed from `searchStock()` to `searchStockPaginated()`
- Pass pagination data to view

**Before:**
```php
public function stock() {
    $filters = [...];
    $stock = $stockModel->searchStock($filters);
    $data = [
        'stock' => $stock,
        ...
    ];
}
```

**After:**
```php
public function stock() {
    $page = $this->get('page', 1);
    $filters = [...];
    $result = $stockModel->searchStockPaginated($page, ITEMS_PER_PAGE, $filters);
    $data = [
        'stock' => $result['data'],
        'pagination' => $result,
        ...
    ];
}
```

### 3. **models/Stock.php**
**New Method:** `searchStockPaginated()`

**Features:**
- Accepts page number, items per page, and filters
- Returns paginated data with metadata
- Preserves all filter parameters
- Includes scientific_name in results

**Returns:**
```php
[
    'data' => [...],        // Array of stock records
    'total' => 45,          // Total number of records
    'page' => 2,            // Current page
    'perPage' => 10,        // Items per page
    'totalPages' => 5       // Total pages
]
```

### 4. **views/admin/bpdas.php**
**Changes:**
- Already had proper null coalescing operators
- Uses reusable pagination component
- Pagination working correctly

### 5. **views/helpers/pagination.php**
**Features:**
- Reusable pagination component
- Smart page number display with ellipsis
- Previous/Next buttons
- Filter parameter preservation
- Pagination info display

---

## Pagination Data Structure (Standardized)

All paginated methods now return this consistent structure:

```php
[
    'data' => array,        // Records for current page
    'total' => int,         // Total number of records
    'page' => int,          // Current page number
    'perPage' => int,       // Items per page
    'totalPages' => int     // Total number of pages
]
```

---

## Null Coalescing Operator Usage

### Pattern Applied:
```php
// For strings - use '-' as fallback
<?= htmlspecialchars($item['field_name'] ?? '-') ?>

// For numbers - use 0 as fallback
<?= formatNumber($item['quantity'] ?? 0) ?>

// For dates - check with isset first
<?= isset($item['date_field']) ? formatDate($item['date_field']) : '-' ?>

// For optional fields
<?php if (!empty($item['optional_field'])): ?>
    // Display field
<?php endif; ?>
```

### Fields Fixed in views/admin/stock.php:
- `bpdas_name` → `$item['bpdas_name'] ?? '-'`
- `province_name` → `$item['province_name'] ?? '-'`
- `seedling_name` → `$item['seedling_name'] ?? '-'`
- `scientific_name` → `!empty($item['scientific_name'])`
- `category` → `$item['category'] ?? '-'`
- `quantity` → `$item['quantity'] ?? 0`
- `last_update_date` → `isset($item['last_update_date']) ? ... : '-'`

---

## Pagination Component Usage

### In Views:
```php
<?php
// Render pagination using helper
if (isset($pagination)) {
    require_once VIEWS_PATH . 'helpers/pagination.php';
    
    // Without filters
    renderPagination($pagination, 'admin/stock');
    
    // With filters (preserves filter parameters)
    $queryParams = [
        'province_id' => $filters['province_id'] ?? null,
        'bpdas_id' => $filters['bpdas_id'] ?? null,
        'seedling_type_id' => $filters['seedling_type_id'] ?? null,
        'category' => $filters['category'] ?? null
    ];
    renderPagination($pagination, 'admin/stock', $queryParams);
}
?>
```

### In Controllers:
```php
public function yourMethod() {
    $page = $this->get('page', 1);
    $filters = [
        'filter1' => $this->get('filter1'),
        'filter2' => $this->get('filter2')
    ];
    
    $model = $this->model('YourModel');
    $result = $model->paginatedMethod($page, ITEMS_PER_PAGE, $filters);
    
    $data = [
        'records' => $result['data'],
        'pagination' => $result,
        'filters' => $filters
    ];
    
    $this->render('your/view', $data, 'dashboard');
}
```

### In Models:
```php
public function paginatedMethod($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
    $offset = ($page - 1) * $perPage;
    
    // Build query with filters
    $sql = "SELECT ... WHERE 1=1";
    $countSql = "SELECT COUNT(*) as total WHERE 1=1";
    $params = [];
    
    // Add filter conditions
    if (!empty($filters['field'])) {
        $sql .= " AND field = ?";
        $countSql .= " AND field = ?";
        $params[] = $filters['field'];
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    
    // Execute queries
    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value);
    }
    $stmt->bindValue(count($params) + 1, (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();
    
    $countStmt = $this->db->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    return [
        'data' => $data,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ];
}
```

---

## Testing Checklist

### Admin Stock Page:
- [x] Page loads without errors
- [x] No "Undefined array key" warnings
- [x] Pagination appears when >10 records
- [x] Previous button works (disabled on page 1)
- [x] Next button works (disabled on last page)
- [x] Page numbers clickable
- [x] Row numbers continue across pages
- [x] Filters preserved in pagination
- [x] Scientific name displays when available
- [x] All fields show "-" when empty

### Admin BPDAS Page:
- [x] Pagination working correctly
- [x] Previous/Next buttons functional
- [x] Smart page display with ellipsis
- [x] Row numbering correct

### Other Pages to Apply:
- [ ] BPDAS > Kelola Stok (views/bpdas/stock.php)
- [ ] BPDAS > Kelola Permintaan (views/bpdas/requests.php)
- [ ] Public > My Requests (views/public/my-requests.php)
- [ ] Admin > Kelola Permintaan (already has pagination)
- [ ] Admin > Kelola Pengguna (already has pagination)
- [ ] Admin > Kelola Jenis Bibit (already has pagination)

---

## Benefits

### 1. **No More PHP Warnings**
- All array accesses are safe
- Fallback values prevent errors
- Better error handling

### 2. **Better User Experience**
- Easy navigation through large datasets
- Clear pagination controls
- Filters preserved across pages
- Page info displayed

### 3. **Consistent Code**
- Standardized pagination structure
- Reusable components
- Easy to maintain

### 4. **Performance**
- Only loads needed records
- Reduces database load
- Faster page rendering

---

## Summary

All pagination and undefined array key issues have been fixed for:
- ✅ Admin > Kelola Stok Nasional
- ✅ Admin > Kelola BPDAS

The fixes include:
1. Added null coalescing operators for safe array access
2. Implemented pagination with Previous/Next buttons
3. Created reusable pagination component
4. Standardized pagination data structure
5. Added filter preservation in pagination
6. Fixed row numbering across pages
7. Added scientific_name to stock queries

The same pattern can be easily applied to other pages that need pagination or have undefined array key issues.
