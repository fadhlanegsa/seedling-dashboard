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
     * Send cancellation notification to requester
     * 
     * @param array $request Request data
     * @param string $reason Cancellation reason
     * @return bool
     */
    public function sendCancellationNotification($request, $reason) {
        $to = $request['requester_email'];
        $subject = 'Permintaan Bibit Dibatalkan - ' . $request['request_number'];
        
        $message = $this->getCancellationEmailTemplate($request, $reason);
        
        return $this->send($to, $subject, $message);
    }
    
    /**
     * Send password reset notification
     * 
     * @param string $to Recipient email
     * @param string $resetLink The password reset link
     * @return bool
     */
    public function sendPasswordResetLink($to, $resetLink) {
        $subject = 'Reset Password Anda - Dashboard Bibit Gratis';
        $message = $this->getPasswordResetEmailTemplate($resetLink);
        
        return $this->send($to, $subject, $message);
    }

    /**
     * Get password reset email template
     * 
     * @param string $resetLink
     * @return string
     */
    private function getPasswordResetEmailTemplate($resetLink) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1B5E20; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #eee; border-top: none; border-radius: 0 0 8px 8px; }
                .button-container { text-align: center; margin: 30px 0; }
                .button { display: inline-block; padding: 12px 24px; background: #F59E0B; color: white !important; text-decoration: none; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .link-text { word-break: break-all; font-size: 0.85rem; color: #777; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Permintaan Reset Password</h2>
                </div>
                <div class="content">
                    <p>Halo,</p>
                    <p>Kami menerima permintaan untuk mereset password akun Anda di **Dashboard Bibit Gratis**. Jika Anda membuat permintaan ini, silakan klik tombol di bawah untuk mereset password Anda:</p>
                    
                    <div class="button-container">
                        <a href="<?= $resetLink ?>" class="button">Reset Password</a>
                    </div>
                    
                    <p>Link reset password ini hanya berlaku selama **1 jam** dari waktu email ini dikirimkan.</p>
                    <p>Jika Anda tidak meminta reset password, abaikan email ini dan password Anda tidak akan berubah.</p>
                    
                    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                    
                    <p>Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:</p>
                    <p class="link-text"><a href="<?= $resetLink ?>"><?= $resetLink ?></a></p>
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
     * Send email using PHPMailer (SMTP) or PHP mail() function
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @return bool
     */
    private function send($to, $subject, $message) {
        if (!defined('ENABLE_EMAIL') || !ENABLE_EMAIL) {
            return true; // Email disabled, pretend it sent successfully
        }

        // Use PHPMailer if SMTP is configured
        if (defined('SMTP_HOST') && SMTP_HOST !== '') {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                
                $encryption = strtolower(SMTP_ENCRYPTION);
                if ($encryption === 'ssl') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = SMTP_PORT ?: 465;
                } elseif ($encryption === 'tls') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = SMTP_PORT ?: 587;
                } else {
                    $mail->SMTPSecure = '';
                    $mail->Port       = SMTP_PORT ?: 25;
                }

                // Disable SSL verification for local self-signed certs (common on XAMPP development)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
                
                // Recipients
                $mail->setFrom($this->fromEmail, $this->fromName);
                $mail->addAddress($to);
                $mail->addReplyTo($this->fromEmail);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->CharSet = 'UTF-8';
                
                return $mail->send();
            } catch (Exception $e) {
                logError("PHPMailer Error sending to $to: " . $e->getMessage() . " | Mailer ErrorInfo: " . $mail->ErrorInfo);
                // Fallback to PHP mail()
            } catch (Throwable $t) {
                logError("PHPMailer Throwable Error sending to $to: " . $t->getMessage());
                // Fallback to PHP mail()
            }
        }

        // Native PHP mail() fallback
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        try {
            if (!function_exists('mail')) {
                logError("mail() function not available on this server.");
                return false;
            }
            
            $result = mail($to, $subject, $message, implode("\r\n", $headers));
            
            if (!$result) {
                logError("Email sending failed to: $to");
            }
            
            return $result;
        } catch (Throwable $e) {
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
                        <span><?= $request['seedling_name'] ?? '' ?></span>
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
                        <span><?= $request['seedling_name'] ?? '' ?></span>
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
                        <span><?= $request['seedling_name'] ?? '' ?></span>
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
    
    /**
     * Get cancellation email template
     * 
     * @param array $request
     * @param string $reason
     * @return string
     */
    private function getCancellationEmailTemplate($request, $reason) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #c0392b; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .danger { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .detail { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .button { display: inline-block; padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Permintaan Bibit Dibatalkan</h2>
                </div>
                <div class="content">
                    <p>Yth. <?= $request['requester_name'] ?>,</p>
                    
                    <div class="danger">
                        Mohon maaf, permintaan bibit Anda telah <strong>dibatalkan</strong> karena tidak ada pengambilan.
                    </div>
                    
                    <p>Detail permintaan:</p>
                    
                    <div class="detail">
                        <span class="label">Nomor Permintaan:</span>
                        <span><?= $request['request_number'] ?></span>
                    </div>
                    <div class="detail">
                        <span class="label">BPDAS:</span>
                        <span><?= $request['bpdas_name'] ?></span>
                    </div>
                    
                    <p><strong>Alasan Pembatalan:</strong></p>
                    <p><?= nl2br(htmlspecialchars($reason)) ?></p>
                    
                    <p>Jika Anda masih membutuhkan bibit, silakan ajukan permintaan baru dan pastikan hadir pada waktu yang telah ditentukan.</p>
                    
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
