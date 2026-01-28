<?php
/**
 * Email Sender Utility
 * Sends email notifications using PHP mail() or SMTP
 */

require_once __DIR__ . '/../config/config.php';

class EmailSender {
    
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $this->fromEmail = SMTP_FROM_EMAIL;
        $this->fromName = SMTP_FROM_NAME;
    }
    
    /**
     * Send new request notification to BPDAS
     * 
     * @param array $request Request data
     * @return bool
     */
    public function sendNewRequestNotification($request) {
        $to = $request['bpdas_email'];
        $subject = 'Permintaan Bibit Baru - ' . $request['request_number'];
        
        $message = $this->getNewRequestEmailTemplate($request);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Send approval notification to requester
     * 
     * @param array $request Request data
     * @param string $pdfPath Path to approval letter PDF
     * @return bool
     */
    public function sendApprovalNotification($request, $pdfPath = null) {
        $to = $request['requester_email'];
        $subject = 'Permintaan Bibit Disetujui - ' . $request['request_number'];
        
        $message = $this->getApprovalEmailTemplate($request);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Send rejection notification to requester
     * 
     * @param array $request Request data
     * @param string $reason Rejection reason
     * @return bool
     */
    public function sendRejectionNotification($request, $reason) {
        $to = $request['requester_email'];
        $subject = 'Permintaan Bibit Ditolak - ' . $request['request_number'];
        
        $message = $this->getRejectionEmailTemplate($request, $reason);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Send email using PHP mail() function
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @return bool
     */
    private function send($to, $subject, $message) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        try {
            $result = mail($to, $subject, $message, implode("\r\n", $headers));
            
            if (!$result) {
                logError("Email sending failed to: $to");
            }
            
            return $result;
        } catch (Exception $e) {
            logError("Email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get new request email template
     * 
     * @param array $request
     * @return string
     */
    private function getNewRequestEmailTemplate($request) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2d5016; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .detail { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .button { display: inline-block; padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Permintaan Bibit Baru</h2>
                </div>
                <div class="content">
                    <p>Yth. Kepala <?= $request['bpdas_name'] ?>,</p>
                    <p>Terdapat permintaan bibit baru yang memerlukan persetujuan Anda:</p>
                    
                    <div class="detail">
                        <span class="label">Nomor Permintaan:</span>
                        <span><?= $request['request_number'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Nama Pemohon:</span>
                        <span><?= $request['requester_name'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">NIK:</span>
                        <span><?= $request['requester_nik'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jenis Bibit:</span>
                        <span><?= $request['seedling_name'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jumlah:</span>
                        <span><?= formatNumber($request['quantity']) ?> batang</span>
                    </div>
                    <div class="detail">
                        <span class="label">Tujuan:</span>
                        <span><?= $request['purpose'] ?></span>
                    </div>
                    
                    <p>Silakan login ke dashboard untuk meninjau dan memproses permintaan ini.</p>
                    
                    <a href="<?= url('bpdas/request-detail/' . $request['id']) ?>" class="button">Lihat Detail Permintaan</a>
                </div>
                <div class="footer">
                    <p>Email ini dikirim secara otomatis oleh sistem Dashboard Stok Bibit Persemaian Indonesia.</p>
                    <p>Jangan balas email ini.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get approval email template
     * 
     * @param array $request
     * @return string
     */
    private function getApprovalEmailTemplate($request) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2d5016; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .detail { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .button { display: inline-block; padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Permintaan Bibit Disetujui</h2>
                </div>
                <div class="content">
                    <p>Yth. <?= $request['requester_name'] ?>,</p>
                    
                    <div class="success">
                        <strong>Selamat!</strong> Permintaan bibit Anda telah disetujui.
                    </div>
                    
                    <p>Detail permintaan:</p>
                    
                    <div class="detail">
                        <span class="label">Nomor Permintaan:</span>
                        <span><?= $request['request_number'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jenis Bibit:</span>
                        <span><?= $request['seedling_name'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jumlah:</span>
                        <span><?= formatNumber($request['quantity']) ?> batang</span>
                    </div>
                    
                    <p><strong>Lokasi Pengambilan:</strong></p>
                    <div class="detail">
                        <span class="label">BPDAS:</span>
                        <span><?= $request['bpdas_name'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Alamat:</span>
                        <span><?= $request['bpdas_address'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Telepon:</span>
                        <span><?= $request['bpdas_phone'] ?></span>
                    </div>
                    
                    <?php if (!empty($request['approval_notes'])): ?>
                    <p><strong>Catatan:</strong><br><?= nl2br($request['approval_notes']) ?></p>
                    <?php endif; ?>
                    
                    <p>Silakan login ke dashboard untuk mengunduh surat persetujuan. Bawa surat persetujuan dan KTP asli saat pengambilan bibit.</p>
                    
                    <a href="<?= url('public/request-detail/' . $request['id']) ?>" class="button">Lihat Detail & Unduh Surat</a>
                </div>
                <div class="footer">
                    <p>Email ini dikirim secara otomatis oleh sistem Dashboard Stok Bibit Persemaian Indonesia.</p>
                    <p>Jangan balas email ini.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get rejection email template
     * 
     * @param array $request
     * @param string $reason
     * @return string
     */
    private function getRejectionEmailTemplate($request, $reason) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2d5016; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .detail { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .button { display: inline-block; padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Permintaan Bibit Ditolak</h2>
                </div>
                <div class="content">
                    <p>Yth. <?= $request['requester_name'] ?>,</p>
                    
                    <div class="warning">
                        Mohon maaf, permintaan bibit Anda tidak dapat disetujui.
                    </div>
                    
                    <p>Detail permintaan:</p>
                    
                    <div class="detail">
                        <span class="label">Nomor Permintaan:</span>
                        <span><?= $request['request_number'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jenis Bibit:</span>
                        <span><?= $request['seedling_name'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">Jumlah:</span>
                        <span><?= formatNumber($request['quantity']) ?> batang</span>
                    </div>
                    
                    <p><strong>Alasan Penolakan:</strong></p>
                    <p><?= nl2br(htmlspecialchars($reason)) ?></p>
                    
                    <p>Anda dapat mengajukan permintaan baru dengan menyesuaikan jumlah atau jenis bibit yang tersedia.</p>
                    
                    <a href="<?= url('public/request-form') ?>" class="button">Ajukan Permintaan Baru</a>
                </div>
                <div class="footer">
                    <p>Email ini dikirim secara otomatis oleh sistem Dashboard Stok Bibit Persemaian Indonesia.</p>
                    <p>Jangan balas email ini.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
