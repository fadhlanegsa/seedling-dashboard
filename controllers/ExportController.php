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
        $headers = ['No', 'BPDAS', 'Provinsi', 'Jenis Bibit', 'Nama Ilmiah', 'Kategori', 'Jumlah Stok', 'Terakhir Update'];
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
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // Fill Data
        $row = 2;
        foreach ($stockData as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['bpdas_name'] ?? '-');
            $sheet->setCellValue('C' . $row, $item['province_name'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['seedling_name'] ?? '-');
            $sheet->setCellValue('E' . $row, $item['scientific_name'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['category'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['quantity'] ?? 0);
            $sheet->setCellValue('H' . $row, isset($item['last_update_date']) ? date('d-m-Y', strtotime($item['last_update_date'])) : '-');
            $row++;
        }
        
        // Border Style
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray($borderStyle);
        
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
}
