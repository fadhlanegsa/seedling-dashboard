<?php
/**
 * Laporan Data Permintaan Bibit (PDF Template)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Permintaan Bibit</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #2C3E50;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #7f8c8d;
        }
        .meta-info {
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }
        .meta-info-left {
            display: table-cell;
            width: 70%;
        }
        .meta-info-right {
            display: table-cell;
            width: 30%;
            text-align: right;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #bdc3c7;
            padding: 6px 4px;
            text-align: left;
        }
        table.data-table th {
            background-color: #2C3E50;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #7f8c8d;
            text-align: right;
        }
        .signature-area {
            margin-top: 40px;
            width: 300px;
            float: right;
            text-align: center;
        }
        .signature-title {
            margin-bottom: 60px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN DATA PERMINTAAN BIBIT</h1>
        <p>Sistem Informasi dan Manajemen Dasboard Stok Bibit Persemaian Indonesia</p>
    </div>

    <div class="meta-info">
        <div class="meta-info-left">
            <table>
                <tr>
                    <td width="100"><strong>Status</strong></td>
                    <td width="10">:</td>
                    <td><?= $status ? status_text($status) : 'Semua Status' ?></td>
                </tr>
                <tr>
                    <td><strong>BPDAS</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($user['bpdas_name'] ?? 'Seluruh BPDAS') ?></td>
                </tr>
                <tr>
                    <td><strong>Dicetak Oleh</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($user['full_name']) ?> (<?= ucfirst($user['role']) ?>)</td>
                </tr>
            </table>
        </div>
        <div class="meta-info-right">
            <strong>Tanggal Cetak:</strong><br>
            <?= $download_time ?>
        </div>
    </div>

        <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">No. Permintaan</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Pemohon</th>
                <th width="8%">No. HP</th>
                <th width="8%">NIK</th>
                <th width="9%">Tujuan</th>
                <th width="15%">Detail Bibit (Jenis & Jumlah)</th>
                <th width="6%">Total</th>
                <th width="5%">Luas</th>
                <th width="10%">Alamat Tanam</th>
                <th width="10%">Koordinat</th>
                <th width="7%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $index => $req): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($req['request_number'] ?? '-') ?></td>
                        <td><?= isset($req['created_at']) ? date('d-m-Y', strtotime($req['created_at'])) : '-' ?></td>
                        <td>
                            <?= htmlspecialchars($req['requester_name'] ?? '-') ?><br>
                            <span style="color:#7f8c8d; font-size:9px;"><?= htmlspecialchars($req['requester_email'] ?? '-') ?></span>
                        </td>
                        <td><?= htmlspecialchars($req['requester_phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($req['requester_nik'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($req['purpose'] ?? '-') ?></td>
                        <td>
                            <?php 
                                if (!empty($req['items'])) {
                                    $itemStrings = [];
                                    foreach ($req['items'] as $item) {
                                        $itemStrings[] = '• ' . htmlspecialchars($item['seedling_name']) . ' (' . number_format($item['quantity'], 0, ',', '.') . ')';
                                    }
                                    echo implode("<br>", $itemStrings);
                                } else {
                                    echo htmlspecialchars($req['seedling_name'] ?? '-') . ' (' . number_format($req['quantity'] ?? 0, 0, ',', '.') . ')';
                                }
                            ?>
                        </td>
                        <td class="text-right"><?= number_format($req['item_quantity'] ?: $req['quantity'] ?: 0, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($req['land_area'] ?? '-') ?> Ha</td>
                        <td><?= htmlspecialchars($req['planting_address'] ?? '-') ?></td>
                        <td style="font-size: 8px;">
                            <?php if(isset($req['latitude']) && isset($req['longitude'])): ?>
                                <?= $req['latitude'] ?><br><?= $req['longitude'] ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= status_text($req['status'] ?? 'pending') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data permintaan ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="clearfix">
        <div class="signature-area">
            <div class="signature-title">Pencetak Laporan</div>
            <strong><u><?= htmlspecialchars($user['full_name']) ?></u></strong><br>
            <?= htmlspecialchars($user['bpdas_name'] ?? '') ?>
        </div>
    </div>

    <div class="footer">
        Generated by Dashboard Bibit System &copy; <?= date('Y') ?>
    </div>

</body>
</html>
