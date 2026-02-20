# Panduan Update Infografis Distribusi di Hosting

Ikuti langkah-langkah ini untuk memunculkan grafik "Distribusi Bulanan" di dashboard admin hostingan kamu.

## 1. Update Model (`models/Request.php`)
Buka file `seedling-dashboard/models/Request.php` di hosting. Tambahkan fungsi ini di bagian paling bawah class `Request`, **sebelum** kurung kurawal tutup `}` terakhir.

```php
    /**
     * Get monthly distribution statistics per province
     * For Stacked Bar Chart in Dashboard
     */
    public function getMonthlyDistributionStats($year = null) {
        $year = $year ?? date('Y');
        
        $sql = "SELECT 
                    MONTH(r.updated_at) as month,
                    p.name as province_name,
                    SUM(COALESCE(r.quantity, (SELECT SUM(quantity) FROM request_items ri WHERE ri.request_id = r.id), 0)) as total_distributed
                FROM requests r
                JOIN bpdas b ON r.bpdas_id = b.id
                JOIN provinces p ON b.province_id = p.id
                WHERE r.status = 'delivered' 
                AND YEAR(r.updated_at) = ?
                GROUP BY MONTH(r.updated_at), p.id, p.name
                ORDER BY MONTH(r.updated_at) ASC, total_distributed DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
```

## 2. Update Controller (`controllers/AdminController.php`)
Buka file `seedling-dashboard/controllers/AdminController.php`. Cari method `dashboard()`.
Tambahkan baris `'distributionStats' => ...` ke dalam array `$data`.

Code seharusnya terlihat seperti ini (perhatikan baris komentar `// TAMBAHKAN INI`):

```php
        $data = [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'stockByProvince' => $stockByProvince,
            'topSeedlings' => $topSeedlings,
            // TAMBAHKAN BARIS INI:
            'distributionStats' => $requestModel->getMonthlyDistributionStats(date('Y')) 
        ];
        
        $this->render('admin/dashboard', $data, 'dashboard');
```

## 3. Update View (`views/admin/dashboard.php`)
Buka file `seedling-dashboard/views/admin/dashboard.php`.

### A. Tambahkan HTML Card
Cari bagian `<!-- Charts Section -->`. Tambahkan blok HTML ini sejajar dengan chart lainnya (misalnya setelah "Top 10 Jenis Bibit"):

```html
    <!-- Chart 3: Monthly Distribution -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-chart-area"></i> Distribusi Bulanan</h3>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
```

### B. Tambahkan Javascript Chart
Di bagian bawah file, cari tag `<script>`. Tambahkan kode berikut ini **sebelum** fungsi `new Chart` yang lain, atau di paling bawah script tapi sebelum `</script>`.

```javascript
// Prepare Distribution Data
const distributionStats = <?= json_encode($distributionStats ?? []) ?>;

if (distributionStats && distributionStats.length > 0) {
    // Process data for Stacked Bar Chart
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Get unique provinces
    const provinces = [...new Set(distributionStats.map(item => item.province_name))];
    
    // Generate distinct colors for provinces
    const colors = [
        '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', 
        '#edc948', '#b07aa1', '#ff9da7', '#9c755f', '#bab0ac',
        '#882d17', '#8175aa', '#6baa2c', '#d6a319', '#be514b'
    ];
    
    // Create datasets
    const datasets = provinces.map((province, index) => {
        const data = months.map((_, monthIndex) => {
            const monthNum = monthIndex + 1;
            const record = distributionStats.find(item => 
                item.province_name === province && parseInt(item.month) === monthNum
            );
            return record ? parseInt(record.total_distributed) : 0;
        });
        
        return {
            label: province,
            data: data,
            backgroundColor: colors[index % colors.length],
            stack: 'Stack 0'
        };
    });
    
    const ctxDist = document.getElementById('distributionChart');
    if (ctxDist) {
        new Chart(ctxDist, {
            type: 'bar',
            data: {
                labels: months,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            footer: (tooltipItems) => {
                                let total = 0;
                                tooltipItems.forEach((item) => {
                                    total += item.parsed.y;
                                });
                                return 'Total Bulan Ini: ' + total.toLocaleString();
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Bibit per Bulan (Tahun <?= date("Y") ?>)'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });
    }
} else {
     const chartContainer = document.getElementById('distributionChart');
     if(chartContainer) {
        chartContainer.parentElement.innerHTML = 
            '<p class="text-center text-muted py-5">Belum ada data distribusi (Requests Delivered) tahun ini</p>';
     }
}
```
