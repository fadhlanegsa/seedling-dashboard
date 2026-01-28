<?php
/**
 * PDF Generator Utility
 * Generates approval letters as HTML files that can be printed to PDF
 */

require_once __DIR__ . '/../config/config.php';

class PDFGenerator {
    
    /**
     * Generate approval letter for seedling request
     * 
     * @param array $request Request data with full details
     * @return string PDF filename
     */
    public function generateApprovalLetter($request) {
        return $this->generatePrintableHTML($request);
    }
    
    /**
     * Generate printable HTML that can be saved as PDF
     * 
     * @param array $request
     * @return string
     */
    private function generatePrintableHTML($request) {
        $html = $this->getApprovalLetterHTML($request);
        
        // Save as PDF-ready HTML file
        $filename = 'approval_' . ($request['request_number'] ?? 'unknown') . '_' . time() . '.pdf';
        $filepath = UPLOAD_PATH . $filename;
        
        // Create a proper PDF header
        $pdfContent = "%PDF-1.4\n";
        $pdfContent .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdfContent .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pdfContent .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources 4 0 R /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
        $pdfContent .= "4 0 obj\n<< /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >>\nendobj\n";
        
        // Convert HTML to simple text for PDF
        $text = strip_tags($html);
        $text = html_entity_decode($text);
        $text = str_replace(["\r\n", "\r", "\n"], " ", $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        $stream = "BT\n/F1 12 Tf\n50 750 Td\n";
        $lines = explode("\n", wordwrap($text, 80, "\n"));
        $y = 750;
        foreach ($lines as $line) {
            $stream .= "(" . addslashes($line) . ") Tj\n";
            $y -= 15;
            $stream .= "0 -15 Td\n";
            if ($y < 50) break;
        }
        $stream .= "ET\n";
        
        $pdfContent .= "5 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n";
        $pdfContent .= $stream;
        $pdfContent .= "\nendstream\nendobj\n";
        
        $pdfContent .= "xref\n0 6\n";
        $pdfContent .= "0000000000 65535 f\n";
        $pdfContent .= "0000000009 00000 n\n";
        $pdfContent .= "0000000058 00000 n\n";
        $pdfContent .= "0000000115 00000 n\n";
        $pdfContent .= "0000000214 00000 n\n";
        $pdfContent .= "0000000308 00000 n\n";
        $pdfContent .= "trailer\n<< /Size 6 /Root 1 0 R >>\n";
        $pdfContent .= "startxref\n" . strlen($pdfContent) . "\n%%EOF";
        
        file_put_contents($filepath, $pdfContent);
        
        return $filename;
    }
    
    /**
     * Get approval letter HTML template
     * 
     * @param array $request
     * @return string
     */
    private function getApprovalLetterHTML($request) {
        $requestNumber = $request['request_number'] ?? 'N/A';
        $requesterName = $request['requester_name'] ?? 'N/A';
        $requesterNik = $request['requester_nik'] ?? 'N/A';
        $requesterPhone = $request['requester_phone'] ?? 'N/A';
        $seedlingName = $request['seedling_name'] ?? 'N/A';
        $quantity = isset($request['quantity']) ? formatNumber($request['quantity']) : '0';
        $purpose = $request['purpose'] ?? 'N/A';
        $bpdasName = $request['bpdas_name'] ?? 'N/A';
        $bpdasAddress = $request['bpdas_address'] ?? 'N/A';
        $bpdasPhone = $request['bpdas_phone'] ?? 'N/A';
        $provinceName = $request['province_name'] ?? 'N/A';
        $approvalDate = isset($request['approval_date']) ? formatDate($request['approval_date']) : date('d/m/Y');
        $approverName = $request['approver_name'] ?? 'N/A';
        
        $html = <<<HTML
KEMENTERIAN LINGKUNGAN HIDUP DAN KEHUTANAN
SURAT PERSETUJUAN PENGAMBILAN BIBIT
Nomor: {$requestNumber}

Dengan ini menyatakan bahwa permohonan pengambilan bibit dari:

Nama: {$requesterName}
NIK: {$requesterNik}
No. Telepon: {$requesterPhone}

Telah DISETUJUI untuk mengambil bibit dengan rincian sebagai berikut:

Jenis Bibit: {$seedlingName}
Jumlah: {$quantity} batang
Tujuan: {$purpose}

Pengambilan bibit dapat dilakukan di:

BPDAS: {$bpdasName}
Alamat: {$bpdasAddress}
Telepon: {$bpdasPhone}

Catatan: Harap membawa surat ini beserta KTP asli saat pengambilan bibit. 
Surat ini berlaku 30 hari sejak tanggal persetujuan.

{$provinceName}, {$approvalDate}
Kepala {$bpdasName}



{$approverName}

Verifikasi: {$requestNumber}
HTML;
        
        return $html;
    }
}
