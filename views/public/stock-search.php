<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Stok Bibit - Dashboard Stok Bibit Indonesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            margin-bottom: 2rem;
            font-size: 1rem;
            transition: transform 0.2s;
        }

        .back-btn:hover {
            transform: translateX(-5px);
        }

        .search-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
        }

        .search-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
        }

        .form-group select,
        .form-group input {
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .results-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: none;
        }

        .results-card.show {
            display: block;
        }

        .results-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .results-header h3 {
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .stock-list {
            display: grid;
            gap: 1rem;
        }

        .stock-item {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.2s;
        }

        .stock-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .stock-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .stock-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2d3748;
        }

        .stock-quantity {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stock-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #718096;
            font-size: 0.9rem;
        }

        .info-item i {
            color: #667eea;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            display: none;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 4px solid #e2e8f0;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .search-card {
                padding: 1.5rem;
            }

            .stock-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= url('public/landing') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Beranda
        </a>

        <div class="header">
            <h1><i class="fas fa-search"></i> Cari Stok Bibit</h1>
            <p>Temukan ketersediaan bibit tanaman di seluruh Indonesia</p>
        </div>

        <div class="search-card">
            <form class="search-form" id="searchForm">
                <div class="form-group">
                    <label for="province">
                        <i class="fas fa-map-marker-alt"></i> Provinsi
                    </label>
                    <select name="province_id" id="province">
                        <option value="">-- Semua Provinsi --</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?= $province['id'] ?>"><?= htmlspecialchars($province['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="seedling_type">
                        <i class="fas fa-seedling"></i> Jenis Bibit
                    </label>
                    <select name="seedling_type_id" id="seedling_type">
                        <option value="">-- Semua Jenis Bibit --</option>
                        <?php foreach ($seedlingTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i>
                    Cari Stok Bibit
                </button>
            </form>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Mencari data...</p>
        </div>

        <div class="results-card" id="results">
            <div class="results-header">
                <h3><i class="fas fa-list"></i> Hasil Pencarian</h3>
                <p id="resultCount"></p>
            </div>
            <div class="stock-list" id="stockList"></div>
        </div>
    </div>

    <script>
        const searchForm = document.getElementById('searchForm');
        const loading = document.getElementById('loading');
        const results = document.getElementById('results');
        const stockList = document.getElementById('stockList');
        const resultCount = document.getElementById('resultCount');

        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Show loading
            loading.classList.add('show');
            results.classList.remove('show');

            // Get form data
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData);

            try {
                const response = await fetch('<?= url('public/search-stock-ajax') ?>?' + params);
                const data = await response.json();

                // Hide loading
                loading.classList.remove('show');

                if (data.success) {
                    displayResults(data.data);
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            } catch (error) {
                loading.classList.remove('show');
                alert('Terjadi kesalahan saat mencari data');
                console.error(error);
            }
        });

        function displayResults(stocks) {
            results.classList.add('show');

            if (stocks.length === 0) {
                stockList.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>Tidak ada hasil ditemukan</h3>
                        <p>Coba ubah kriteria pencarian Anda</p>
                    </div>
                `;
                resultCount.textContent = 'Tidak ada hasil';
                return;
            }

            resultCount.textContent = `Ditemukan ${stocks.length} hasil`;

            stockList.innerHTML = stocks.map(stock => `
                <div class="stock-item">
                    <div class="stock-item-header">
                        <div>
                            <div class="stock-name">${stock.seedling_name}</div>
                            <div style="font-size: 0.9rem; color: #718096; font-style: italic;">
                                ${stock.scientific_name || ''}
                            </div>
                        </div>
                        <div class="stock-quantity">
                            ${parseInt(stock.quantity).toLocaleString('id-ID')} bibit
                        </div>
                    </div>
                    <div class="stock-info">
                        <div class="info-item">
                            <i class="fas fa-building"></i>
                            <span>${stock.bpdas_name}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${stock.province_name}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>${stock.category}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    </script>
</body>
</html>
