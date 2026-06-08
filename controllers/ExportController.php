<?php
/**
 * Export Controller
 * Handles Exporting Data to Excel and PDF
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Dompdf\Dompdf;
use Dompdf\Options;

require_once CORE_PATH . 'Controller.php';
require_once VIEWS_PATH . 'helpers/view_helpers.php'; // Required for status_text()

class ExportController extends Controller {
    
    public function __construct() {
        parent::__construct();
        // Ensure user is logged in
        if (!isLoggedIn()) {
            $this->redirect('auth/login');
        }

        // Check for GD extension
        if (!extension_loaded('gd')) {
            die("Error: Ekstensi GD PHP belum aktif. Fitur export membutuhkan ekstensi ini. Harap hubungi administrator server.");
        }
    }
    
    /**
     * Export Stock to Excel
     */
    public function stockExcel() {
        $user = currentUser();
        $isAdmin = ($user['role'] === 'admin');
        
        // Prepare Filters
        $filters = [];
        
        if ($isAdmin) {
            $filters['province_id'] = $this->get('province_id') ? (int)$this->get('province_id') : null;
            $filters['bpdas_id'] = $this->get('bpdas_id') ? (int)$this->get('bpdas_id') : null;
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
            $filters['category'] = $this->get('category');
        } elseif ($user['role'] === 'operator_persemaian') {
            $userModel = $this->model('User');
            $userData = $userModel->getUserWithNursery($user['id']);
            $filters['nursery_id'] = $userData['nursery_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
        } else {
            // BPDAS User - Locked to their BPDAS
            $filters['bpdas_id'] = $user['bpdas_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
        }
        
        // Date Filters (for both roles)
        $filters['month'] = $this->get('month');
        $filters['year'] = $this->get('year');
        
        // Get Data
        $stockModel = $this->model('Stock');
        // Use a high limit to get all records, or implement a specific getAll method
        // Using searchStockPaginated with large limit for now
        $result = $stockModel->searchStockPaginated(1, 10000, $filters); 
        $stockData = $result['data'];
        
        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Properties
        $spreadsheet->getProperties()
            ->setCreator($user['full_name'])
            ->setLastModifiedBy($user['full_name'])
            ->setTitle("Laporan Stok Bibit")
            ->setSubject("Data Stok Bibit")
            ->setDescription("Export Data Stok Bibit System");
            
        // Header Row
        $headers = ['No', 'BPDAS', 'Provinsi', 'Jenis Bibit', 'Nama Ilmiah', 'Kategori', 'Sumber Program', 'Jumlah Stok', 'Terakhir Update'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        // Fill Data
        $row = 2;
        foreach ($stockData as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['bpdas_name'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['province_name'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['seedling_name'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['scientific_name'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['category'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['program_type'] ?? 'Reguler');
            $sheet->setCellValue('H' . $row, $item['quantity'] ?? 0);
            $sheet->setCellValue('I' . $row, isset($item['last_update_date']) ? date('d-m-Y', strtotime($item['last_update_date'])) : '-');
            $row++;
        }
        
        // Border Style
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:I' . ($row - 1))->applyFromArray($borderStyle);
        
        // Filename
        $filename = 'Laporan_Stok_Bibit_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Redirect Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Export Stock to PDF (Simple)
     */
    public function stockPDF() {
        $user = currentUser();
        $isAdmin = ($user['role'] === 'admin');
        
        // Prepare Filters (Same logc)
        $filters = [];
        if ($isAdmin) {
            $filters['province_id'] = $this->get('province_id') ? (int)$this->get('province_id') : null;
            $filters['bpdas_id'] = $this->get('bpdas_id') ? (int)$this->get('bpdas_id') : null;
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
            $filters['category'] = $this->get('category');
        } elseif ($user['role'] === 'operator_persemaian') {
            $userModel = $this->model('User');
            $userData = $userModel->getUserWithNursery($user['id']);
            $filters['nursery_id'] = $userData['nursery_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
        } else {
            $filters['bpdas_id'] = $user['bpdas_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;
        }
        
        // Date Filters
        $filters['month'] = $this->get('month');
        $filters['year'] = $this->get('year');
        
        // Get Data
        $stockModel = $this->model('Stock');
        $result = $stockModel->searchStockPaginated(1, 10000, $filters);
        $stockData = $result['data'];
        
        // View Data
        $data = [
            'stock' => $stockData,
            'filters' => $filters,
            'download_time' => date('d F Y H:i'),
            'user' => $user
        ];
        
        // Load HTML content
        ob_start();
        $this->render('exports/stock_pdf', $data);
        $html = ob_get_clean();
        
        // Init DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // For images if any
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Stream PDF
        $filename = 'Laporan_Stok_Bibit_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
    
    /**
     * Export Requests to Excel
     */
    public function requestsExcel() {
        $user = currentUser();
        // Cek hanya bisa diakses BPDAS/Admin/Operator
        if ($user['role'] !== 'bpdas' && $user['role'] !== 'admin' && $user['role'] !== 'operator_persemaian') {
             $this->redirect('dashboard');
             return;
        }

        $status = $this->get('status');
        $startDate = $this->get('start_date');
        $endDate = $this->get('end_date');
        $includePhoto = $this->get('include_photo') === 'yes';
        
        $requestModel = $this->model('Request');
        // Ambil data (tidak peduli pagination, ambil semua karena export)
        if ($user['role'] === 'operator_persemaian') {
            $requests = $requestModel->getByNursery($user['nursery_id'], $status, $startDate, $endDate);
        } else {
            $bpdasId = $user['role'] === 'admin' ? null : $user['bpdas_id'];
            $requests = $requestModel->getByBPDAS($bpdasId, $status, $startDate, $endDate);
        }
        
        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Properties
        $spreadsheet->getProperties()
            ->setCreator($user['full_name'])
            ->setLastModifiedBy($user['full_name'])
            ->setTitle("Daftar Permintaan Bibit")
            ->setSubject("Data Permintaan Bibit")
            ->setDescription("Export Data Permintaan Bibit System");
            
        // Add Metadata Header Before Table
        $sheet->setCellValue('A1', 'LAPORAN DATA PERMINTAAN BIBIT');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Dicetak Oleh : ' . $user['full_name'] . ' (' . ucfirst($user['role']) . ')');
        $sheet->mergeCells('A2:E2');
        if ($user['role'] === 'operator_persemaian') {
            $sheet->setCellValue('A3', 'Persemaian : ' . ($user['nursery_name'] ?? 'Persemaian'));
        } else {
            $sheet->setCellValue('A3', 'BPDAS : ' . ($user['bpdas_name'] ?? 'Seluruh BPDAS'));
        }
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A4', 'Status Filter : ' . ($status ? status_text($status) : 'Semua Status'));
        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A5', 'Tanggal Cetak : ' . date('d F Y H:i'));
        $sheet->mergeCells('A5:E5');

        // Header Row
        $headers = [
            'No', 'No. Permintaan', 'Tanggal', 'Pemohon', 'Email', 
            'NIK', 'No. HP', 'Tujuan Penggunaan', 'Sumber Program', 'Detail Bibit (Jenis & Jumlah)', 'Total Jumlah', 
            'Luas Lahan', 'Alamat Tanam', 'Koordinat', 'Status'
        ];
        
        if ($includePhoto) {
            $headers[] = 'Foto Lampiran';
        }
        
        $col = 'A';
        $startRow = 7;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        // Apply to the dynamically determined last column
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A'.$startRow.':'.$lastCol.$startRow)->applyFromArray($headerStyle);
        
        // Fill Data
        $row = $startRow + 1;
        foreach ($requests as $index => $req) {
            // Ambil detail items jika ada
            $items = $requestModel->getRequestItems($req['id']);
            $detailBibit = '';
            
            if (!empty($items)) {
                $itemStrings = [];
                foreach ($items as $item) {
                     $pt = $item['program_type'] ?? 'Reguler';
                     $itemStrings[] = $item['seedling_name'] . " ($pt) - " . number_format($item['quantity'], 0, ',', '.');
                }
                $detailBibit = implode(", \n", $itemStrings);
            } else {
                 $detailBibit = ($req['seedling_name'] ?? '-') . ' (' . number_format($req['quantity'] ?? 0, 0, ',', '.') . ')';
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $req['request_number'] ?? '-');
            $sheet->setCellValue('C' . $row, isset($req['created_at']) ? date('d-m-Y', strtotime($req['created_at'])) : '-');
            $sheet->setCellValue('D' . $row, $req['requester_name'] ?? '-');
            $sheet->setCellValue('E' . $row, $req['requester_email'] ?? '-');
            // Supaya excel tak ubah NIK jadi angka sci notation 
            $sheet->setCellValueExplicit('F' . $row, $req['requester_nik'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row, $req['requester_phone'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $row, $req['purpose'] ?? '-');
            
            $sheet->setCellValue('I' . $row, $req['program_type'] ?? 'Reguler');
            $sheet->setCellValue('J' . $row, $detailBibit);
            // Tambahkan newline style agar text terwrap
            $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
            
            $sheet->setCellValue('K' . $row, $req['item_quantity'] ?: $req['quantity'] ?: 0);
            
            // Kolom Tambahan Detail
            // Luas Lahan (biasanya tersimpan sebagai land_area)
            $sheet->setCellValue('L' . $row, ($req['land_area'] ?? '-') . ' Ha');
            $sheet->setCellValue('M' . $row, $req['planting_address'] ?? '-');
            $koordinat = (isset($req['latitude']) && isset($req['longitude'])) ? $req['latitude'].', '.$req['longitude'] : '-';
            $sheet->setCellValue('N' . $row, $koordinat);
            
            $sheet->setCellValue('O' . $row, status_text($req['status'] ?? 'pending'));
            
            if ($includePhoto) {
                $photoPath = !empty($req['delivery_photo_path']) ? UPLOAD_PATH . $req['delivery_photo_path'] : '';
                if (!empty($photoPath) && file_exists($photoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Bukti');
                    $drawing->setDescription('Foto Bukti Serah Terima');
                    $drawing->setPath($photoPath);
                    $drawing->setCoordinates('P' . $row);
                    $drawing->setHeight(80); // Adjust height
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    
                    // Adjust row height to fit image
                    $sheet->getRowDimension($row)->setRowHeight(70);
                    // Adjust column width manually since AutoSize doesn't work well with images
                    $sheet->getColumnDimension('P')->setWidth(20);
                } else {
                    $sheet->setCellValue('P' . $row, 'Tidak ada foto');
                }
            }
            
            $row++;
        }
        
        // Border Style
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A'.$startRow.':'.$lastCol . ($row - 1))->applyFromArray($borderStyle);
        
        // Alignments
        $sheet->getStyle('A'.($startRow+1).':A' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K'.($startRow+1).':K' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Filename
        $filename = 'Permintaan_Bibit_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Redirect Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Requests to PDF
     */
    public function requestsPDF() {
        $user = currentUser();
        if ($user['role'] !== 'bpdas' && $user['role'] !== 'admin' && $user['role'] !== 'operator_persemaian') {
             $this->redirect('dashboard');
             return;
        }

        $status = $this->get('status');
        $startDate = $this->get('start_date');
        $endDate = $this->get('end_date');
        $includePhoto = $this->get('include_photo') === 'yes';
        
        $requestModel = $this->model('Request');
        
        if ($user['role'] === 'operator_persemaian') {
            $requests = $requestModel->getByNursery($user['nursery_id'], $status, $startDate, $endDate);
        } else {
            $bpdasId = $user['role'] === 'admin' ? null : $user['bpdas_id'];
            $requests = $requestModel->getByBPDAS($bpdasId, $status, $startDate, $endDate);
        }
        
        // Get details per request
        foreach($requests as &$req) {
            $req['items'] = $requestModel->getRequestItems($req['id']);
        }
        unset($req);

        // Fetch BPDAS Name if available
        if ($user['role'] === 'operator_persemaian') {
            $user['bpdas_name'] = $user['nursery_name'] ?? 'Persemaian Anda';
        } else if (!isset($user['bpdas_name']) && isset($bpdasId)) {
            $bpdasModel = $this->model('BPDAS');
            $bpdas = $bpdasModel->find($bpdasId);
            $user['bpdas_name'] = $bpdas ? $bpdas['name'] : 'Seluruh BPDAS';
        }

        // View Data
        $data = [
            'requests' => $requests,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'include_photo' => $includePhoto,
            'download_time' => date('d F Y H:i'),
            'user' => $user
        ];
        
        // Load HTML content
        ob_start();
        $this->render('exports/requests_pdf', $data);
        $html = ob_get_clean();
        
        // Init DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Stream PDF
        $filename = 'Permintaan_Bibit_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }

    /**
     * Export All Seedling Production Data to Excel
     */
    public function seedlingExcel() {
        $user = currentUser();
        
        $startDate = $this->get('start_date');
        $endDate = $this->get('end_date');
        $seedlingTypeId = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;

        if (empty($startDate) || empty($endDate)) {
            die("Error: Rentang waktu wajib diisi.");
        }

        // Get organization info for header
        $userModel = $this->model('User');
        $orgName = 'Seluruh Indonesia (Pusat)';
        $filters = [
            'nursery_id' => null,
            'bpdas_id' => null
        ];

        if ($user['role'] === 'operator_persemaian') {
            $userData = $userModel->getUserWithNursery($user['id']);
            $orgName = $userData['nursery_name'] ?? 'Persemaian';
            $filters['nursery_id'] = $userData['nursery_id'] ?? null;
        } elseif ($user['role'] === 'bpdas') {
            $userData = $userModel->getUserWithBPDAS($user['id']);
            $orgName = $userData['bpdas_name'] ?? 'BPDAS';
            $filters['bpdas_id'] = $user['bpdas_id'];
        }

        // Fetch seedling type name if filtered
        $seedlingTypeName = '';
        if ($seedlingTypeId) {
            $stModel = $this->model('SeedlingType');
            $stData = $stModel->find($seedlingTypeId);
            if ($stData) {
                $seedlingTypeName = $stData['name'];
            }
        }

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();

        // We will define the sheets structure:
        $sheetsInfo = [
            [
                'title' => 'Bahan Baku IN',
                'sql' => "SELECT t.transaction_id, t.transaction_date, m.name as item_name, m.category as item_category, 
                                 t.quantity, m.unit as item_unit, ss.seed_source_name, t.sender_name, t.receiver_name, t.notes
                          FROM bahan_baku_transactions t
                          JOIN bahan_baku_master m ON t.item_id = m.id
                          LEFT JOIN seed_sources ss ON t.seed_source_id = ss.id
                          WHERE t.transaction_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Transaksi', 'Tanggal', 'Item', 'Kategori', 'Jumlah', 'Satuan', 'Sumber Benih', 'Pengirim', 'Penerima', 'Catatan'],
                'mappings' => ['transaction_id', 'transaction_date', 'item_name', 'item_category', 'quantity', 'item_unit', 'seed_source_name', 'sender_name', 'receiver_name', 'notes'],
                'date_fields' => ['transaction_date'],
                'number_fields' => ['quantity'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 't.nursery_id',
                'role_bpdas_col' => 't.bpdas_id'
            ],
            [
                'title' => 'Mixing Media',
                'sql' => "SELECT p.production_code, p.production_date, p.total_production, p.mandor, p.manager, p.notes
                          FROM media_mixing_productions p
                          WHERE p.production_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Produksi', 'Tanggal', 'Total Produksi (m³)', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['production_code', 'production_date', 'total_production', 'mandor', 'manager', 'notes'],
                'date_fields' => ['production_date'],
                'number_fields' => ['total_production'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 'p.nursery_id',
                'role_bpdas_col' => 'p.bpdas_id'
            ],
            [
                'title' => 'Pengisian Kantong',
                'sql' => "SELECT f.filling_code, f.filling_date, m.name as bag_name, f.total_production, f.mandor, f.manager, f.notes
                          FROM bag_fillings f
                          JOIN bahan_baku_master m ON f.bag_item_id = m.id
                          WHERE f.filling_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Pengisian', 'Tanggal', 'Jenis Kantong', 'Total Produksi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['filling_code', 'filling_date', 'bag_name', 'total_production', 'mandor', 'manager', 'notes'],
                'date_fields' => ['filling_date'],
                'number_fields' => ['total_production'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 'f.nursery_id',
                'role_bpdas_col' => 'f.bpdas_id'
            ],
            [
                'title' => 'Penaburan Benih',
                'sql' => "SELECT s.sowing_code, s.sowing_date, m.name as seed_name, s.seed_quantity, m.unit as seed_unit,
                                 ss.seed_source_name, 
                                 (SELECT SUM(quantity) FROM seed_sowing_polybags WHERE sowing_id = s.id) as total_polybags,
                                 s.mandor, s.manager, s.notes
                          FROM seed_sowings s
                          JOIN bahan_baku_master m ON s.seed_item_id = m.id
                          LEFT JOIN seed_sources ss ON s.seed_source_id = ss.id
                          WHERE s.sowing_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Tabur', 'Tanggal', 'Jenis Benih', 'Jumlah Benih', 'Satuan', 'Sumber Benih', 'Total Polybag', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['sowing_code', 'sowing_date', 'seed_name', 'seed_quantity', 'seed_unit', 'seed_source_name', 'total_polybags', 'mandor', 'manager', 'notes'],
                'date_fields' => ['sowing_date'],
                'number_fields' => ['seed_quantity', 'total_polybags'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'm.seedling_type_id',
                'role_nursery_col' => 's.nursery_id',
                'role_bpdas_col' => 's.bpdas_id'
            ],
            [
                'title' => 'Pemanenan Semai',
                'sql' => "SELECT h.harvest_code, h.harvest_date, m.name as seed_name, h.harvested_quantity,
                                 h.location, h.mandor, h.manager, h.notes
                          FROM seedling_harvests h
                          JOIN seed_sowings s ON h.sowing_id = s.id
                          JOIN bahan_baku_master m ON s.seed_item_id = m.id
                          WHERE h.harvest_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Panen', 'Tanggal', 'Jenis Benih', 'Jumlah Anakan', 'Lokasi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['harvest_code', 'harvest_date', 'seed_name', 'harvested_quantity', 'location', 'mandor', 'manager', 'notes'],
                'date_fields' => ['harvest_date'],
                'number_fields' => ['harvested_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'm.seedling_type_id',
                'role_nursery_col' => 'h.nursery_id',
                'role_bpdas_col' => 'h.bpdas_id'
            ],
            [
                'title' => 'Penyapihan',
                'sql' => "SELECT w.weaning_code, w.weaning_date, h.harvest_code, st.name as result_name, w.weaned_quantity,
                                 w.location, w.mandor, w.manager, w.notes
                          FROM seedling_weanings w
                          LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
                          JOIN seedling_types st ON w.result_item_id = st.id
                          LEFT JOIN seed_sources ss ON w.seed_source_id = ss.id
                          WHERE w.weaning_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Sapih', 'Tanggal', 'Asal PA', 'Hasil Bibit', 'Jumlah', 'Lokasi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['weaning_code', 'weaning_date', 'harvest_code', 'result_name', 'weaned_quantity', 'location', 'mandor', 'manager', 'notes'],
                'date_fields' => ['weaning_date'],
                'number_fields' => ['weaned_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'w.result_item_id',
                'role_nursery_col' => 'w.nursery_id',
                'role_bpdas_col' => 'w.bpdas_id'
            ],
            [
                'title' => 'Entres',
                'sql' => "SELECT e.entres_code, e.entres_date, COALESCE(w.weaning_code, h.harvest_code) as source_code,
                                 st.name as result_name, e.used_quantity, e.location, e.mandor, e.manager, e.notes
                          FROM seedling_entres e
                          LEFT JOIN seedling_weanings w ON e.weaning_id = w.id
                          LEFT JOIN seedling_harvests h ON e.harvest_id = h.id
                          JOIN seedling_types st ON e.result_item_id = st.id
                          WHERE e.entres_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Entres', 'Tanggal', 'Asal PE/PA', 'Hasil Bibit', 'Jumlah', 'Lokasi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['entres_code', 'entres_date', 'source_code', 'result_name', 'used_quantity', 'location', 'mandor', 'manager', 'notes'],
                'date_fields' => ['entres_date'],
                'number_fields' => ['used_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'e.result_item_id',
                'role_nursery_col' => 'e.nursery_id',
                'role_bpdas_col' => 'e.bpdas_id'
            ],
            [
                'title' => 'Mutasi',
                'sql' => "SELECT m.mutation_code, m.mutation_date, m.source_type, 
                                 CASE 
                                     WHEN m.source_type = 'PE' THEN (SELECT weaning_code FROM seedling_weanings WHERE id = m.source_id)
                                     WHEN m.source_type = 'ET' THEN (SELECT entres_code FROM seedling_entres WHERE id = m.source_id)
                                 END as source_code,
                                 m.mutation_type, m.quantity, m.origin_location, m.target_location, m.mandor, m.manager, m.notes,
                                 CASE 
                                     WHEN m.source_type = 'PE' THEN (SELECT st.name FROM seedling_weanings w JOIN seedling_types st ON w.result_item_id = st.id WHERE w.id = m.source_id)
                                     WHEN m.source_type = 'ET' THEN (SELECT st.name FROM seedling_entres e JOIN seedling_types st ON e.result_item_id = st.id WHERE e.id = m.source_id)
                                 END as seedling_name
                          FROM seedling_mutations m
                          WHERE m.mutation_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Mutasi', 'Tanggal', 'Asal Bibit', 'Jenis Mutasi', 'Jumlah', 'Asal Lokasi', 'Tujuan Lokasi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['mutation_code', 'mutation_date', 'seedling_name', 'mutation_type', 'quantity', 'origin_location', 'target_location', 'mandor', 'manager', 'notes'],
                'date_fields' => ['mutation_date'],
                'number_fields' => ['quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'mutation_type_custom',
                'role_nursery_col' => 'm.nursery_id',
                'role_bpdas_col' => 'm.bpdas_id'
            ]
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        foreach ($sheetsInfo as $sIdx => $info) {
            if ($sIdx === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet($sIdx);
            }
            $sheet->setTitle($info['title']);

            // Build SQL & Params with Scoping & Filters
            $sql = $info['sql'];
            $params = $info['params'];

            // Role Scope
            if ($filters['nursery_id'] && !empty($info['role_nursery_col'])) {
                $sql .= " AND " . $info['role_nursery_col'] . " = ?";
                $params[] = $filters['nursery_id'];
            } elseif ($filters['bpdas_id'] && !empty($info['role_bpdas_col'])) {
                $sql .= " AND " . $info['role_bpdas_col'] . " = ?";
                $params[] = $filters['bpdas_id'];
            }

            // Seedling Type Filter (PC, PA, PE, ET, BO)
            if ($seedlingTypeId && $info['filter_seedling_type']) {
                if ($info['title'] === 'Mutasi') {
                    $sql .= " AND (
                        (m.source_type = 'PE' AND m.source_id IN (SELECT id FROM seedling_weanings WHERE result_item_id = ?))
                        OR 
                        (m.source_type = 'ET' AND m.source_id IN (SELECT id FROM seedling_entres WHERE result_item_id = ?))
                    )";
                    $params[] = $seedlingTypeId;
                    $params[] = $seedlingTypeId;
                } else {
                    $sql .= " AND " . $info['seedling_type_col'] . " = ?";
                    $params[] = $seedlingTypeId;
                }
            }

            // Retrieve Data
            $data = $userModel->query($sql, $params);

            // Add Header Metadata
            $sheet->setCellValue('A1', 'LAPORAN DATA PENATAUSAHAAN BIBIT - ' . strtoupper($info['title']));
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('A2', 'Dicetak Oleh : ' . $user['full_name'] . ' (' . ucfirst($user['role']) . ')');
            $sheet->mergeCells('A2:E2');
            
            $sheet->setCellValue('A3', 'Organisasi  : ' . $orgName);
            $sheet->mergeCells('A3:E3');

            $filterText = 'Rentang Waktu: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));
            if ($info['filter_seedling_type'] && $seedlingTypeName) {
                $filterText .= ' | Jenis Tanaman: ' . $seedlingTypeName;
            }
            $sheet->setCellValue('A4', $filterText);
            $sheet->mergeCells('A4:J4');

            $sheet->setCellValue('A5', 'Tanggal Cetak: ' . date('d F Y H:i'));
            $sheet->mergeCells('A5:E5');

            // Table headers
            $col = 'A';
            $startRow = 7;
            foreach ($info['headers'] as $headerTitle) {
                $sheet->setCellValue($col . $startRow, $headerTitle);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Style headers
            $lastCol = chr(ord('A') + count($info['headers']) - 1);
            $sheet->getStyle('A'.$startRow.':'.$lastCol.$startRow)->applyFromArray($headerStyle);

            // Fill data rows
            $row = $startRow + 1;
            foreach ($data as $index => $item) {
                $sheet->setCellValue('A' . $row, $index + 1);
                
                $col = 'B';
                foreach ($info['mappings'] as $colKey) {
                    $val = isset($item[$colKey]) ? $item[$colKey] : null;

                    // Formatting values
                    if (in_array($colKey, $info['date_fields']) && !empty($val)) {
                        $val = date('d-m-Y', strtotime($val));
                    }
                    
                    if (in_array($colKey, $info['number_fields'])) {
                        if ($val === null || $val === '') {
                            $sheet->setCellValueExplicit($col . $row, '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        } else {
                            $sheet->setCellValueExplicit($col . $row, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                            if (strpos((string)$val, '.') !== false) {
                                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                            } else {
                                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
                            }
                        }
                    } else {
                        $sheet->setCellValueExplicit($col . $row, $val ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $col++;
                }
                $row++;
            }

            // Apply borders to whole table
            if ($row > $startRow + 1) {
                $sheet->getStyle('A'.$startRow.':'.$lastCol . ($row - 1))->applyFromArray($borderStyle);
                // Center Serial numbers
                $sheet->getStyle('A'.($startRow+1).':A' . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Set active sheet back to first
        $spreadsheet->setActiveSheetIndex(0);

        // Filename
        $filename = 'Laporan_Keseluruhan_Bibit_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Redirect Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Summary & Detail Laporan to PDF
     */
    public function seedlingPDF() {
        $user = currentUser();
        
        $startDate = $this->get('start_date');
        $endDate = $this->get('end_date');
        $seedlingTypeId = $this->get('seedling_type_id') ? (int)$this->get('seedling_type_id') : null;

        if (empty($startDate) || empty($endDate)) {
            die("Error: Rentang waktu wajib diisi.");
        }

        $userModel = $this->model('User');
        $orgName = 'Seluruh Indonesia (Pusat)';
        $filters = [
            'nursery_id' => null,
            'bpdas_id' => null
        ];

        if ($user['role'] === 'operator_persemaian') {
            $userData = $userModel->getUserWithNursery($user['id']);
            $orgName = $userData['nursery_name'] ?? 'Persemaian';
            $filters['nursery_id'] = $userData['nursery_id'] ?? null;
        } elseif ($user['role'] === 'bpdas') {
            $userData = $userModel->getUserWithBPDAS($user['id']);
            $orgName = $userData['bpdas_name'] ?? 'BPDAS';
            $filters['bpdas_id'] = $user['bpdas_id'];
        }

        // Fetch seedling type name if filtered
        $seedlingTypeName = '';
        if ($seedlingTypeId) {
            $stModel = $this->model('SeedlingType');
            $stData = $stModel->find($seedlingTypeId);
            if ($stData) {
                $seedlingTypeName = $stData['name'];
            }
        }

        // Define the queries structure exactly like Excel
        $sectionsInfo = [
            'bahan_baku' => [
                'title' => 'Bahan Baku IN',
                'sql' => "SELECT t.transaction_id, t.transaction_date, m.name as item_name, m.category as item_category, 
                                 t.quantity, m.unit as item_unit, ss.seed_source_name, t.sender_name, t.receiver_name, t.notes
                          FROM bahan_baku_transactions t
                          JOIN bahan_baku_master m ON t.item_id = m.id
                          LEFT JOIN seed_sources ss ON t.seed_source_id = ss.id
                          WHERE t.transaction_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Transaksi', 'Tanggal', 'Item', 'Kategori', 'Jumlah', 'Satuan', 'Sumber Benih', 'Catatan'],
                'mappings' => ['transaction_id', 'transaction_date', 'item_name', 'item_category', 'quantity', 'item_unit', 'seed_source_name', 'notes'],
                'date_fields' => ['transaction_date'],
                'number_fields' => ['quantity'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 't.nursery_id',
                'role_bpdas_col' => 't.bpdas_id'
            ],
            'media_mixing' => [
                'title' => 'Mixing Media',
                'sql' => "SELECT p.production_code, p.production_date, p.total_production, p.mandor, p.manager, p.notes
                          FROM media_mixing_productions p
                          WHERE p.production_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Produksi', 'Tanggal', 'Total Produksi (m³)', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['production_code', 'production_date', 'total_production', 'mandor', 'manager', 'notes'],
                'date_fields' => ['production_date'],
                'number_fields' => ['total_production'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 'p.nursery_id',
                'role_bpdas_col' => 'p.bpdas_id'
            ],
            'bag_filling' => [
                'title' => 'Pengisian Kantong',
                'sql' => "SELECT f.filling_code, f.filling_date, m.name as bag_name, f.total_production, f.mandor, f.manager, f.notes
                          FROM bag_fillings f
                          JOIN bahan_baku_master m ON f.bag_item_id = m.id
                          WHERE f.filling_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Pengisian', 'Tanggal', 'Jenis Kantong', 'Total Produksi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['filling_code', 'filling_date', 'bag_name', 'total_production', 'mandor', 'manager', 'notes'],
                'date_fields' => ['filling_date'],
                'number_fields' => ['total_production'],
                'filter_seedling_type' => false,
                'role_nursery_col' => 'f.nursery_id',
                'role_bpdas_col' => 'f.bpdas_id'
            ],
            'seed_sowing' => [
                'title' => 'Penaburan Benih',
                'sql' => "SELECT s.sowing_code, s.sowing_date, m.name as seed_name, s.seed_quantity, m.unit as seed_unit,
                                 ss.seed_source_name, 
                                 (SELECT SUM(quantity) FROM seed_sowing_polybags WHERE sowing_id = s.id) as total_polybags,
                                 s.mandor, s.manager, s.notes
                          FROM seed_sowings s
                          JOIN bahan_baku_master m ON s.seed_item_id = m.id
                          LEFT JOIN seed_sources ss ON s.seed_source_id = ss.id
                          WHERE s.sowing_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Tabur', 'Tanggal', 'Jenis Benih', 'Jumlah Benih', 'Satuan', 'Sumber Benih', 'Total Polybag', 'Mandor', 'Catatan'],
                'mappings' => ['sowing_code', 'sowing_date', 'seed_name', 'seed_quantity', 'seed_unit', 'seed_source_name', 'total_polybags', 'mandor', 'notes'],
                'date_fields' => ['sowing_date'],
                'number_fields' => ['seed_quantity', 'total_polybags'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'm.seedling_type_id',
                'role_nursery_col' => 's.nursery_id',
                'role_bpdas_col' => 's.bpdas_id'
            ],
            'seedling_harvest' => [
                'title' => 'Pemanenan Semai',
                'sql' => "SELECT h.harvest_code, h.harvest_date, m.name as seed_name, h.harvested_quantity,
                                 h.location, h.mandor, h.manager, h.notes
                          FROM seedling_harvests h
                          JOIN seed_sowings s ON h.sowing_id = s.id
                          JOIN bahan_baku_master m ON s.seed_item_id = m.id
                          WHERE h.harvest_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Panen', 'Tanggal', 'Jenis Benih', 'Jumlah Anakan', 'Lokasi', 'Mandor', 'Manager', 'Catatan'],
                'mappings' => ['harvest_code', 'harvest_date', 'seed_name', 'harvested_quantity', 'location', 'mandor', 'manager', 'notes'],
                'date_fields' => ['harvest_date'],
                'number_fields' => ['harvested_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'm.seedling_type_id',
                'role_nursery_col' => 'h.nursery_id',
                'role_bpdas_col' => 'h.bpdas_id'
            ],
            'seedling_weaning' => [
                'title' => 'Penyapihan',
                'sql' => "SELECT w.weaning_code, w.weaning_date, h.harvest_code, st.name as result_name, w.weaned_quantity,
                                 w.location, w.mandor, w.manager, w.notes
                          FROM seedling_weanings w
                          LEFT JOIN seedling_harvests h ON w.harvest_id = h.id
                          JOIN seedling_types st ON w.result_item_id = st.id
                          LEFT JOIN seed_sources ss ON w.seed_source_id = ss.id
                          WHERE w.weaning_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Sapih', 'Tanggal', 'Asal PA', 'Hasil Bibit', 'Jumlah', 'Lokasi', 'Mandor', 'Catatan'],
                'mappings' => ['weaning_code', 'weaning_date', 'harvest_code', 'result_name', 'weaned_quantity', 'location', 'mandor', 'notes'],
                'date_fields' => ['weaning_date'],
                'number_fields' => ['weaned_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'w.result_item_id',
                'role_nursery_col' => 'w.nursery_id',
                'role_bpdas_col' => 'w.bpdas_id'
            ],
            'seedling_entres' => [
                'title' => 'Entres',
                'sql' => "SELECT e.entres_code, e.entres_date, COALESCE(w.weaning_code, h.harvest_code) as source_code,
                                 st.name as result_name, e.used_quantity, e.location, e.mandor, e.manager, e.notes
                          FROM seedling_entres e
                          LEFT JOIN seedling_weanings w ON e.weaning_id = w.id
                          LEFT JOIN seedling_harvests h ON e.harvest_id = h.id
                          JOIN seedling_types st ON e.result_item_id = st.id
                          WHERE e.entres_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Entres', 'Tanggal', 'Asal PE/PA', 'Hasil Bibit', 'Jumlah', 'Lokasi', 'Mandor', 'Catatan'],
                'mappings' => ['entres_code', 'entres_date', 'source_code', 'result_name', 'used_quantity', 'location', 'mandor', 'notes'],
                'date_fields' => ['entres_date'],
                'number_fields' => ['used_quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'e.result_item_id',
                'role_nursery_col' => 'e.nursery_id',
                'role_bpdas_col' => 'e.bpdas_id'
            ],
            'seedling_mutation' => [
                'title' => 'Mutasi',
                'sql' => "SELECT m.mutation_code, m.mutation_date, m.source_type, 
                                 CASE 
                                     WHEN m.source_type = 'PE' THEN (SELECT weaning_code FROM seedling_weanings WHERE id = m.source_id)
                                     WHEN m.source_type = 'ET' THEN (SELECT entres_code FROM seedling_entres WHERE id = m.source_id)
                                 END as source_code,
                                 m.mutation_type, m.quantity, m.origin_location, m.target_location, m.mandor, m.manager, m.notes,
                                 CASE 
                                     WHEN m.source_type = 'PE' THEN (SELECT st.name FROM seedling_weanings w JOIN seedling_types st ON w.result_item_id = st.id WHERE w.id = m.source_id)
                                     WHEN m.source_type = 'ET' THEN (SELECT st.name FROM seedling_entres e JOIN seedling_types st ON e.result_item_id = st.id WHERE e.id = m.source_id)
                                 END as seedling_name
                          FROM seedling_mutations m
                          WHERE m.mutation_date BETWEEN ? AND ?",
                'params' => [$startDate, $endDate],
                'headers' => ['No', 'Kode Mutasi', 'Tanggal', 'Asal Bibit', 'Jenis Mutasi', 'Jumlah', 'Asal Lokasi', 'Tujuan', 'Catatan'],
                'mappings' => ['mutation_code', 'mutation_date', 'seedling_name', 'mutation_type', 'quantity', 'origin_location', 'target_location', 'notes'],
                'date_fields' => ['mutation_date'],
                'number_fields' => ['quantity'],
                'filter_seedling_type' => true,
                'seedling_type_col' => 'mutation_type_custom',
                'role_nursery_col' => 'm.nursery_id',
                'role_bpdas_col' => 'm.bpdas_id'
            ]
        ];

        // Fetch detailed data for each section
        $dataDetails = [];
        $summary = [];

        foreach ($sectionsInfo as $key => $info) {
            $sql = $info['sql'];
            $params = $info['params'];

            // Role Scope
            if ($filters['nursery_id'] && !empty($info['role_nursery_col'])) {
                $sql .= " AND " . $info['role_nursery_col'] . " = ?";
                $params[] = $filters['nursery_id'];
            } elseif ($filters['bpdas_id'] && !empty($info['role_bpdas_col'])) {
                $sql .= " AND " . $info['role_bpdas_col'] . " = ?";
                $params[] = $filters['bpdas_id'];
            }

            // Seedling Type Filter
            if ($seedlingTypeId && $info['filter_seedling_type']) {
                if ($key === 'seedling_mutation') {
                    $sql .= " AND (
                        (m.source_type = 'PE' AND m.source_id IN (SELECT id FROM seedling_weanings WHERE result_item_id = ?))
                        OR 
                        (m.source_type = 'ET' AND m.source_id IN (SELECT id FROM seedling_entres WHERE result_item_id = ?))
                    )";
                    $params[] = $seedlingTypeId;
                    $params[] = $seedlingTypeId;
                } else {
                    $sql .= " AND " . $info['seedling_type_col'] . " = ?";
                    $params[] = $seedlingTypeId;
                }
            }

            // Query Details
            $records = $userModel->query($sql, $params);
            $dataDetails[$key] = [
                'title' => $info['title'],
                'headers' => $info['headers'],
                'mappings' => $info['mappings'],
                'date_fields' => $info['date_fields'],
                'number_fields' => $info['number_fields'],
                'records' => $records
            ];

            // Calculate summary stats
            $cnt = count($records);
            $tot = 0;
            $tot_seed = 0;
            $tot_polybags = 0;

            if ($key === 'seed_sowing') {
                foreach ($records as $r) {
                    $tot_seed += $r['seed_quantity'] ?? 0;
                    $tot_polybags += $r['total_polybags'] ?? 0;
                }
            } else {
                $sumCol = ($key === 'bahan_baku') ? 'quantity' : 
                         (($key === 'media_mixing') ? 'total_production' : 
                         (($key === 'bag_filling') ? 'total_production' : 
                         (($key === 'seedling_harvest') ? 'harvested_quantity' : 
                         (($key === 'seedling_weaning') ? 'weaned_quantity' : 
                         (($key === 'seedling_entres') ? 'used_quantity' : 
                         (($key === 'seedling_mutation') ? 'quantity' : ''))))));
                
                foreach ($records as $r) {
                    $tot += $r[$sumCol] ?? 0;
                }
            }

            $summary[$key] = [
                'cnt' => $cnt,
                'tot' => $tot,
                'tot_seed' => $tot_seed,
                'tot_polybags' => $tot_polybags
            ];
        }

        // View Data
        $data = [
            'summary' => $summary,
            'dataDetails' => $dataDetails,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'seedlingTypeName' => $seedlingTypeName,
            'orgName' => $orgName,
            'download_time' => date('d F Y H:i'),
            'user' => $user
        ];
        
        // Load HTML content
        ob_start();
        $this->render('exports/seedling_pdf', $data);
        $html = ob_get_clean();
        
        // Init DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Stream PDF
        $filename = 'Laporan_Keseluruhan_Bibit_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
}

