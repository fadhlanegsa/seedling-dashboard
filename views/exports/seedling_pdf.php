<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detil Penatausahaan Bibit</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }
        .header-container {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 10px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #2C3E50;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }
        .header-subtitle {
            font-size: 11px;
            color: #555;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .meta-table td {
            border: none;
            padding: 3px 0;
        }
        .meta-label {
            font-weight: bold;
            color: #555;
            width: 150px;
        }
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
        }
        table.summary-table th {
            background-color: #2C3E50;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #2C3E50;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.summary-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
        }
        table.summary-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5px;
            background-color: #fff;
        }
        table.detail-table th {
            background-color: #34495e;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #475569;
            text-transform: uppercase;
        }
        table.detail-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            word-wrap: break-word;
        }
        table.detail-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 8px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 50px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- PAGE 1: RINGKASAN REKAPITULASI -->
    <div class="header-container">
        <h1 class="header-title">Rekapitulasi Laporan Penatausahaan Bibit</h1>
        <p class="header-subtitle">Kementerian Kehutanan Republik Indonesia</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Organisasi / Unit Kerja</td>
            <td>: <?= htmlspecialchars($orgName) ?></td>
            <td class="meta-label">Tanggal Cetak</td>
            <td>: <?= htmlspecialchars($download_time) ?></td>
        </tr>
        <tr>
            <td class="meta-label">Periode Laporan</td>
            <td>: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></td>
            <td class="meta-label">Dicetak Oleh</td>
            <td>: <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</td>
        </tr>
        <?php if (!empty($seedlingTypeName)): ?>
        <tr>
            <td class="meta-label">Filter Jenis Tanaman</td>
            <td colspan="3">: <?= htmlspecialchars($seedlingTypeName) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <h2 style="font-size: 12px; color: #2C3E50; margin-bottom: 8px; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px;">Ringkasan Aktivitas Operasional</h2>

    <table class="summary-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="35%">Kategori Kegiatan</th>
                <th width="25%" class="text-center">Jumlah Transaksi (Record)</th>
                <th width="35%" class="text-right">Total Kuantitas / Volume</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Bahan Baku Masuk (BI)</td>
                <td class="text-center"><?= number_format($summary['bahan_baku']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['bahan_baku']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Unit/Pcs/Kg</span></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Pencampuran Media Tanam (MT)</td>
                <td class="text-center"><?= number_format($summary['media_mixing']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['media_mixing']['tot'], 2, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">m³</span></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Pengisian Kantong / Polybag (PB)</td>
                <td class="text-center"><?= number_format($summary['bag_filling']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['bag_filling']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Kantong</span></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Penaburan Benih (PC)</td>
                <td class="text-center"><?= number_format($summary['seed_sowing']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold">
                    <?= number_format($summary['seed_sowing']['tot_seed'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Benih</span>
                    <br>
                    <span style="font-size: 8.5px; color: #4A5568; font-weight: normal;">(<?= number_format($summary['seed_sowing']['tot_polybags'], 0, ',', '.') ?> Polybag)</span>
                </td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Pemanenan Semai (PA)</td>
                <td class="text-center"><?= number_format($summary['seedling_harvest']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['seedling_harvest']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Anakan</span></td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Penyapihan Bibit (PE)</td>
                <td class="text-center"><?= number_format($summary['seedling_weaning']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['seedling_weaning']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Bibit</span></td>
            </tr>
            <tr>
                <td class="text-center">7</td>
                <td>Entres (ET)</td>
                <td class="text-center"><?= number_format($summary['seedling_entres']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['seedling_entres']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Pcs</span></td>
            </tr>
            <tr>
                <td class="text-center">8</td>
                <td>Mutasi Bibit (BO)</td>
                <td class="text-center"><?= number_format($summary['seedling_mutation']['cnt'], 0, ',', '.') ?></td>
                <td class="text-right bold"><?= number_format($summary['seedling_mutation']['tot'], 0, ',', '.') ?> <span style="font-weight: normal; font-size: 8px; color: #666;">Bibit</span></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p style="margin-bottom: 30px;">Petugas Penatausahaan,</p>
            <div class="signature-space"></div>
            <p class="bold" style="text-decoration: underline; margin-bottom: 2px;"><?= htmlspecialchars($user['full_name']) ?></p>
            <p style="margin: 0; font-size: 9px; color: #666;"><?= htmlspecialchars(ucfirst($user['role'])) ?></p>
        </div>
        <div style="clear: both;"></div>
    </div>


    <!-- PAGES 2+: DETAILS FOR EACH MENU -->
    <?php foreach ($dataDetails as $key => $detail): ?>
        <div class="page-break"></div>
        
        <div class="header-container">
            <h1 class="header-title">Detail Laporan Penatausahaan Bibit</h1>
            <p class="header-subtitle"><?= htmlspecialchars($detail['title']) ?> &nbsp;|&nbsp; Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?></p>
        </div>

        <table class="detail-table">
            <thead>
                <tr>
                    <?php foreach ($detail['headers'] as $header): ?>
                        <th><?= htmlspecialchars($header) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detail['records'])): ?>
                    <?php foreach ($detail['records'] as $index => $item): ?>
                        <tr>
                            <td class="text-center" width="3%"><?= $index + 1 ?></td>
                            <?php foreach ($detail['mappings'] as $colKey): ?>
                                <?php 
                                    $val = $item[$colKey] ?? null;

                                    if (in_array($colKey, $detail['date_fields']) && !empty($val)) {
                                        $val = date('d-m-Y', strtotime($val));
                                    }
                                    
                                    $isNumber = in_array($colKey, $detail['number_fields']);
                                    $alignClass = $isNumber ? 'text-right' : '';
                                    
                                    if ($val === null || $val === '') {
                                        $formattedVal = '-';
                                        $alignClass = 'text-center';
                                    } else {
                                        if ($isNumber && is_numeric($val)) {
                                            $formattedVal = number_format($val, (strpos((string)$val, '.') !== false ? 2 : 0), ',', '.');
                                        } else {
                                            $formattedVal = htmlspecialchars($val);
                                        }
                                    }
                                ?>
                                <td class="<?= $alignClass ?>"><?= $formattedVal ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($detail['headers']) ?>" class="text-center text-muted" style="padding: 15px; font-style: italic;">
                            Tidak ada data transaksi pada rentang waktu ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>


    <!-- FOOTER WITH COPYRIGHT & AUTO PAGINATION INFO (Dompdf will auto-draw on pages) -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">Sistem Informasi Dashboard Stok Bibit Persemaian Indonesia</td>
                <td style="border: none; padding: 0; text-align: right;">Kementerian Kehutanan Republik Indonesia</td>
            </tr>
        </table>
    </div>

</body>
</html>
