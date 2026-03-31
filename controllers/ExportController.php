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
            $filters['province_id'] = $this->get('province_id');
            $filters['bpdas_id'] = $this->get('bpdas_id');
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
            $filters['category'] = $this->get('category');
        } elseif ($user['role'] === 'operator_persemaian') {
            $userModel = $this->model('User');
            $userData = $userModel->getUserWithNursery($user['id']);
            $filters['nursery_id'] = $userData['nursery_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
        } else {
            // BPDAS User - Locked to their BPDAS
            $filters['bpdas_id'] = $user['bpdas_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
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
            $filters['province_id'] = $this->get('province_id');
            $filters['bpdas_id'] = $this->get('bpdas_id');
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
            $filters['category'] = $this->get('category');
        } elseif ($user['role'] === 'operator_persemaian') {
            $userModel = $this->model('User');
            $userData = $userModel->getUserWithNursery($user['id']);
            $filters['nursery_id'] = $userData['nursery_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
        } else {
            $filters['bpdas_id'] = $user['bpdas_id'];
            $filters['seedling_type_id'] = $this->get('seedling_type_id');
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
        // Cek hanya bisa diakses BPDAS/Admin
        if ($user['role'] !== 'bpdas' && $user['role'] !== 'admin') {
             $this->redirect('dashboard');
             return;
        }

        $bpdasId = $user['bpdas_id'];
        $status = $this->get('status');
        
        $requestModel = $this->model('Request');
        // Ambil data (tidak peduli pagination, ambil semua karena export)
        // Kita bisa panggil getByBPDAS yang ada di Request Model yg unpaginated
        // Note: kalau data terlalu besar, mungkin butuh chunking
        $requests = $requestModel->getByBPDAS($bpdasId, $status);
        
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
        $sheet->setCellValue('A3', 'BPDAS : ' . ($user['bpdas_name'] ?? 'Seluruh BPDAS'));
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
        // J is no longer the end, now we have O columns
        $sheet->getStyle('A'.$startRow.':O'.$startRow)->applyFromArray($headerStyle);
        
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
            
            $sheet->setCellValue('K' . $row, $req['item_quantity'] ?? $req['quantity'] ?? 0);
            
            // Kolom Tambahan Detail
            // Luas Lahan (biasanya tersimpan sebagai land_area)
            $sheet->setCellValue('L' . $row, ($req['land_area'] ?? '-') . ' Ha');
            $sheet->setCellValue('M' . $row, $req['planting_address'] ?? '-');
            $koordinat = (isset($req['latitude']) && isset($req['longitude'])) ? $req['latitude'].', '.$req['longitude'] : '-';
            $sheet->setCellValue('N' . $row, $koordinat);
            
            $sheet->setCellValue('O' . $row, status_text($req['status'] ?? 'pending'));
            $row++;
        }
        
        // Border Style
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A'.$startRow.':O' . ($row - 1))->applyFromArray($borderStyle);
        
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
        if ($user['role'] !== 'bpdas' && $user['role'] !== 'admin') {
             $this->redirect('dashboard');
             return;
        }

        $bpdasId = $user['bpdas_id'];
        $status = $this->get('status');
        
        $requestModel = $this->model('Request');
        $requests = $requestModel->getByBPDAS($bpdasId, $status);
        
        // Get details per request
        foreach($requests as &$req) {
            $req['items'] = $requestModel->getRequestItems($req['id']);
        }
        unset($req);

        // Fetch BPDAS Name if available
        if (!isset($user['bpdas_name']) && isset($bpdasId)) {
            $bpdasModel = $this->model('BPDAS');
            $bpdas = $bpdasModel->find($bpdasId);
            $user['bpdas_name'] = $bpdas ? $bpdas['name'] : 'Seluruh BPDAS';
        }

        // View Data
        $data = [
            'requests' => $requests,
            'status' => $status,
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
}
