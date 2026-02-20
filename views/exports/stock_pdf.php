<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Bibit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .meta {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h2>Laporan Stok Bibit Nasional</h2>
    <div class="meta">
        Data diunduh pada: <?= $download_time ?><br>
        Oleh: <?= htmlspecialchars($user['full_name']) ?>
    </div>

    <!-- Summary of filters if any -->
    <?php if (!empty($filters) && array_filter($filters)): ?>
    <div style="margin-bottom: 10px; font-size: 11px;">
        <strong>Filter Aktif:</strong><br>
        <?php foreach ($filters as $key => $val): 
            if (empty($val)) continue;
        ?>
            - <?= ucfirst(str_replace('_', ' ', str_replace('_id', '', $key))) ?>: <?= htmlspecialchars($val) ?><br>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">BPDAS</th>
                <th width="15%">Provinsi</th>
                <th width="20%">Jenis Bibit</th>
                <th width="15%">Kategori</th>
                <th width="15%" class="text-right">Jumlah Stok</th>
                <th width="15%" class="text-center">Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($stock)): ?>
                <?php foreach ($stock as $index => $item): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['bpdas_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['province_name'] ?? '-') ?></td>
                        <td>
                            <strong><?= htmlspecialchars($item['seedling_name'] ?? '-') ?></strong><br>
                            <span style="color: #666; font-style: italic;"><?= htmlspecialchars($item['scientific_name'] ?? '') ?></span>
                        </td>
                        <td><span class="badge"><?= htmlspecialchars($item['category'] ?? '-') ?></span></td>
                        <td class="text-right"><strong><?= number_format($item['quantity'] ?? 0, 0, ',', '.') ?></strong></td>
                        <td class="text-center"><?= isset($item['last_update_date']) ? date('d/m/Y', strtotime($item['last_update_date'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data stok yang tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #888;">
        Dicetak dari Sistem Informasi Persemaian
    </div>
</body>
</html>
