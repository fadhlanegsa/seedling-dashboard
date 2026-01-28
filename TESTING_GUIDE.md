# Critical Path Testing Guide
## Dashboard Charts & Statistics Feature

This guide will help you test the newly implemented chart features on both the admin dashboard and public homepage.

---

## Prerequisites

1. Ensure your XAMPP server is running (Apache and MySQL)
2. Ensure the database `db_bibit` is populated with sample data
3. Have at least one admin user account for testing
4. Open your browser (Chrome, Firefox, or Edge recommended)

---

## Test 1: Public Homepage Charts

### URL to Test
```
http://localhost/seedling-dashboard/public
```
or
```
http://localhost/seedling-dashboard/public/home
```

### Expected Results

#### Statistics Cards
- [ ] **Total BPDAS** card displays a number (should be > 0 if data exists)
- [ ] **Jenis Bibit** card displays a number (should be > 0 if data exists)
- [ ] **Total Stok Nasional** card displays a number (should be > 0 if data exists)

#### Chart 1: Stock by Province
- [ ] Chart renders as a vertical bar chart
- [ ] X-axis shows province names
- [ ] Y-axis shows stock quantities with proper formatting (e.g., "1,000")
- [ ] Bars are green (rgba(46, 125, 50, 0.7))
- [ ] Hovering over bars shows tooltip with "Stok: X bibit"
- [ ] If no data: Shows message "Belum ada data stok per provinsi"

#### Chart 2: Top 10 Jenis Bibit
- [ ] Chart renders as a horizontal bar chart
- [ ] Y-axis shows seedling type names
- [ ] X-axis shows stock quantities
- [ ] Bars are blue (rgba(33, 150, 243, 0.7))
- [ ] Hovering over bars shows tooltip with "Stok: X bibit"
- [ ] If no data: Shows message "Belum ada data jenis bibit"

#### Other Elements
- [ ] Search form is visible with province and seedling type dropdowns
- [ ] "Cari BPDAS" button works
- [ ] "How It Works" section displays 4 steps
- [ ] CTA section shows appropriate button (Register or Request)

### Browser Console Check
1. Press F12 to open Developer Tools
2. Go to Console tab
3. Look for these log messages:
   ```
   Stock by Province: [array of data]
   Top Seedlings: [array of data]
   ```
4. [ ] No JavaScript errors appear in red

---

## Test 2: Admin Dashboard Charts

### Steps to Access
1. Navigate to: `http://localhost/seedling-dashboard/public/auth/login`
2. Login with admin credentials
3. You should be redirected to: `http://localhost/seedling-dashboard/public/admin/dashboard`

### Expected Results

#### Statistics Cards
- [ ] **Total BPDAS** card displays correct count
- [ ] **Jenis Bibit** card displays correct count
- [ ] **Total Stok Nasional** card displays total stock
- [ ] **Permintaan Pending** card displays pending request count

#### Chart 1: Stock by Province
- [ ] Chart renders as a vertical bar chart
- [ ] Shows stock distribution across provinces
- [ ] Green bars with proper formatting
- [ ] Tooltip shows "Stok: X bibit" on hover
- [ ] If no data: Shows "Belum ada data stok per provinsi"

#### Chart 2: Top 10 Jenis Bibit
- [ ] Chart renders as a horizontal bar chart
- [ ] Shows top 10 seedling types by stock
- [ ] Blue bars with proper formatting
- [ ] Tooltip shows "Stok: X bibit" on hover
- [ ] If no data: Shows "Belum ada data jenis bibit"

#### Chart 3: Tren Update Stok (12 Bulan Terakhir)
- [ ] Chart renders as a line chart
- [ ] X-axis shows month names (e.g., "Jan 2025", "Feb 2025")
- [ ] Y-axis shows update count
- [ ] Orange line with filled area underneath
- [ ] Tooltip shows "Update: X kali" on hover
- [ ] If no data: Shows "Belum ada data tren update"

#### Quick Actions
- [ ] All 5 quick action buttons are visible
- [ ] Buttons link to correct pages:
  - Kelola BPDAS → `/admin/bpdas`
  - Kelola Jenis Bibit → `/admin/seedling-types`
  - Lihat Stok Nasional → `/admin/stock`
  - Kelola Permintaan → `/admin/requests`
  - Kelola Pengguna → `/admin/users`

### Browser Console Check
1. Press F12 to open Developer Tools
2. Go to Console tab
3. Look for these log messages:
   ```
   Stock by Province: [array of data]
   Top Seedlings: [array of data]
   Update Trend: [array of data]
   ```
4. [ ] No JavaScript errors appear in red
5. [ ] Chart.js library loads successfully (check Network tab)

---

## Test 3: Database Data Verification

### Check if Sample Data Exists

Open phpMyAdmin or MySQL command line and run these queries:

#### Check BPDAS Count
```sql
SELECT COUNT(*) as total_bpdas FROM bpdas WHERE is_active = 1;
```
**Expected:** Should return a number > 0

#### Check Seedling Types Count
```sql
SELECT COUNT(*) as total_types FROM seedling_types WHERE is_active = 1;
```
**Expected:** Should return a number > 0

#### Check Stock Data
```sql
SELECT COUNT(*) as total_stock_records FROM stock;
SELECT SUM(quantity) as total_quantity FROM stock;
```
**Expected:** Should return records and total quantity > 0

#### Check Stock by Province
```sql
SELECT p.name as province_name, SUM(s.quantity) as total_stock
FROM provinces p
INNER JOIN bpdas b ON p.id = b.province_id
LEFT JOIN stock s ON b.id = s.bpdas_id
WHERE b.is_active = 1
GROUP BY p.id, p.name
HAVING total_stock > 0
ORDER BY total_stock DESC;
```
**Expected:** Should return provinces with stock data

#### Check Top Seedlings
```sql
SELECT st.name as seedling_name, SUM(s.quantity) as total_stock
FROM stock s
INNER JOIN seedling_types st ON s.seedling_type_id = st.id
GROUP BY st.id, st.name
ORDER BY total_stock DESC
LIMIT 10;
```
**Expected:** Should return top 10 seedling types

#### Check Update Trend Data
```sql
SELECT 
    DATE_FORMAT(last_update_date, '%Y-%m') as month,
    DATE_FORMAT(last_update_date, '%b %Y') as month_name,
    COUNT(DISTINCT bpdas_id, seedling_type_id) as update_count
FROM stock
WHERE last_update_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(last_update_date, '%Y-%m'), DATE_FORMAT(last_update_date, '%b %Y')
ORDER BY month ASC;
```
**Expected:** Should return monthly update counts

---

## Test 4: Empty State Testing

### If No Data Exists

If your database has no stock data, the charts should display friendly messages:

1. **Public Homepage:**
   - Stock by Province chart area shows: "Belum ada data stok per provinsi"
   - Top Seedlings chart area shows: "Belum ada data jenis bibit"

2. **Admin Dashboard:**
   - Stock by Province chart area shows: "Belum ada data stok per provinsi"
   - Top Seedlings chart area shows: "Belum ada data jenis bibit"
   - Update Trend chart area shows: "Belum ada data tren update"

---

## Test 5: Responsive Design (Optional)

### Desktop View (1920x1080)
- [ ] All charts display properly
- [ ] Statistics cards are in a row
- [ ] Charts are side-by-side (2 columns)

### Tablet View (768x1024)
- [ ] Charts stack vertically
- [ ] Statistics cards remain readable
- [ ] Navigation menu adapts

### Mobile View (375x667)
- [ ] All elements stack vertically
- [ ] Charts remain interactive
- [ ] Text is readable without zooming

---

## Troubleshooting

### Charts Not Displaying

**Problem:** Charts show empty space or error messages

**Solutions:**
1. Check browser console for JavaScript errors
2. Verify Chart.js CDN is loading (check Network tab in DevTools)
3. Ensure database has stock data
4. Check that `last_update_date` field has recent dates

### "Undefined array key" Warnings

**Problem:** PHP warnings appear at top of page

**Solutions:**
1. Verify all array access uses null coalescing operator (`??`)
2. Check that controller is passing all required data to view
3. Ensure models are returning data in expected format

### Charts Show "No Data" Message

**Problem:** Charts display "Belum ada data..." messages

**Solutions:**
1. Run database verification queries (Test 3)
2. Add sample data using `database/sample_data.sql`
3. Ensure BPDAS records have `is_active = 1`
4. Check that stock records have valid `bpdas_id` and `seedling_type_id`

### Statistics Cards Show Zero

**Problem:** All statistics cards display "0"

**Solutions:**
1. Verify database connection in `config/database.php`
2. Check that tables exist and have data
3. Run SQL queries manually to verify data exists
4. Check PHP error logs in `logs/` directory

---

## Success Criteria

✅ **Test Passes If:**
- All statistics cards display correct numbers (or 0 if no data)
- Charts render properly with data visualization
- Empty states show friendly messages when no data exists
- No JavaScript errors in browser console
- No PHP errors or warnings on page
- Tooltips work on chart hover
- Page loads within 3 seconds

❌ **Test Fails If:**
- JavaScript errors appear in console
- Charts don't render at all
- PHP errors/warnings visible on page
- Statistics show incorrect numbers
- Page takes > 5 seconds to load
- Charts are not interactive (no tooltips)

---

## Reporting Issues

If you encounter any issues during testing, please note:

1. **What page** you were testing (URL)
2. **What you expected** to happen
3. **What actually happened**
4. **Browser console errors** (if any)
5. **Screenshots** of the issue
6. **Database query results** (if relevant)

---

## Next Steps After Testing

Once critical-path testing is complete:

1. ✅ If all tests pass → Mark feature as complete
2. ⚠️ If minor issues found → Document and fix
3. ❌ If major issues found → Debug and retest

Thank you for testing!
