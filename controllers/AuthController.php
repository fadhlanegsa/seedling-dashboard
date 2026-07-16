<?php
/**
 * Auth Controller
 * Handles authentication (login, register, logout)
 */

require_once CORE_PATH . 'Controller.php';

class AuthController extends Controller {
    
    /**
     * Login page
     */
    public function login() {
        // Clear any stale session data except flash messages
        if (isLoggedIn()) {
            // If already logged in, redirect to dashboard
            $this->redirectToDashboard();
            return;
        }
        
        // Clean up any leftover session data (except flash)
        $flash = $_SESSION['flash'] ?? null;
        $_SESSION = [];
        if ($flash) {
            $_SESSION['flash'] = $flash;
        }
        
        $data = [
            'title' => 'Login'
        ];
        
        $this->render('auth/login', $data, null);
    }
    
    /**
     * Process login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
            return;
        }
        
        // Check rate limit (5 attempts per 5 minutes)
        $rateLimit = checkRateLimit('login', 5, 300);
        if (!$rateLimit['allowed']) {
            $this->setFlash('error', $rateLimit['message']);
            $this->redirect('auth/login');
            return;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRF()) {
            return;
        }
        
        $username = sanitize($this->post('username'));
        $password = $this->post('password');
        $remember = $this->post('remember');
        
        // Validate input
        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username dan password harus diisi');
            $this->redirect('auth/login');
            return;
        }

        // Validate reCAPTCHA
        $recaptchaResponse = $this->post('g-recaptcha-response');
        if (empty($recaptchaResponse)) {
            $this->setFlash('error', 'Silakan centang reCAPTCHA untuk membuktikan Anda bukan robot.');
            $this->redirect('auth/login');
            return;
        }

        $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        // Gunakan cURL untuk outbound request (file_get_contents diblokir firewall hosting)
        $ch = curl_init($recaptchaVerifyUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($recaptchaData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false, // Bypass SSL verification jika CA bundle di hosting tidak terkonfigurasi/outdated
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) reCAPTCHA-Verify/1.0',
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $verifyResult = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($verifyResult === false) {
            error_log('reCAPTCHA cURL error: ' . $curlError);
            $this->setFlash('error', 'Gagal menghubungi server verifikasi. Silakan coba lagi.');
            $this->redirect('auth/login');
            return;
        }

        $captchaSuccess = json_decode($verifyResult);

        if (!$captchaSuccess || !$captchaSuccess->success) {
            $errorDetail = $verifyResult ? trim($verifyResult) : 'Empty response';
            logError('reCAPTCHA validation failed. Response: ' . $errorDetail);
            $this->setFlash('error', 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
            $this->redirect('auth/login');
            return;
        }
        
        // Authenticate user
        $userModel = $this->model('User');
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            // Log successful login
            $userModel->logLogin($user['id'], $username, 'success');
            
            // Reset rate limit on successful login
            resetRateLimit('login');
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            
            // Set remember me cookie if checked
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
            }
            
            // Redirect to appropriate dashboard
            $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
                exit;
            }
            
            $this->redirectToDashboard();
        } else {
            // Try to find if user exists to log their ID even on failure
            $existingUser = $userModel->queryOne("SELECT id FROM users WHERE username = ? OR email = ? OR phone = ? OR nik = ? LIMIT 1", [$username, $username, $username, $username]);
            $userId = $existingUser ? $existingUser['id'] : null;
            
            // Log failed login
            $userModel->logLogin($userId, $username, 'failed');
            
            $this->setFlash('error', 'Username atau password salah');
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Register page
     */
    public function register() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $data = [
            'title' => 'Registrasi'
        ];
        
        $this->render('auth/register', $data, 'auth');
    }
    
    /**
     * Process registration
     */
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/register');
            return;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRF()) {
            return;
        }
        
        $data = [
            'username'         => sanitize($this->post('username')),
            'email'            => sanitize($this->post('email')),
            'password'         => $this->post('password'),
            'password_confirm' => $this->post('password_confirm'),
            'full_name'        => sanitize($this->post('full_name')),
            'phone'            => sanitize($this->post('phone')),
            'nik'              => sanitize($this->post('nik')),
            'user_type'        => $this->post('user_type')
        ];

        // Normalisasi email: string kosong → null (email opsional, masyarakat tidak wajib punya email)
        $data['email'] = (isset($data['email']) && trim($data['email']) !== '') ? strtolower(trim($data['email'])) : null;
        
        // Validate required fields
        $errors = $this->validateRequired($data, [
            'username', 'password', 'password_confirm', 
            'full_name', 'phone', 'nik', 'user_type'
        ]);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Semua field wajib harus diisi (email boleh dikosongkan)');
            $this->redirect('auth/register');
            return;
        }

        // Validate user_type
        if (!in_array($data['user_type'], ['perorangan', 'kelompok'])) {
            $this->setFlash('error', 'Jenis pemohon tidak valid');
            $this->redirect('auth/register');
            return;
        }
        
        // Validate email format — HANYA jika email diisi (email opsional)
        if ($data['email'] !== null && !$this->validateEmail($data['email'])) {
            $this->setFlash('error', 'Format email tidak valid');
            $this->redirect('auth/register');
            return;
        }
        
        // Validate password length
        if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $this->setFlash('error', 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter');
            $this->redirect('auth/register');
            return;
        }
        
        // Validate password confirmation
        if ($data['password'] !== $data['password_confirm']) {
            $this->setFlash('error', 'Konfirmasi password tidak cocok');
            $this->redirect('auth/register');
            return;
        }
        
        // Validate NIK (16 digits)
        if (!preg_match('/^\d{16}$/', $data['nik'])) {
            $this->setFlash('error', 'NIK harus 16 digit angka');
            $this->redirect('auth/register');
            return;
        }
        
        $userModel = $this->model('User');
        
        // Check if username exists
        if ($userModel->usernameExists($data['username'])) {
            $this->setFlash('error', 'Username sudah digunakan');
            $this->redirect('auth/register');
            return;
        }
        
        // Check if email exists — HANYA jika email diisi (NULL tidak dianggap duplikat di MySQL)
        if ($data['email'] !== null && $userModel->emailExists($data['email'])) {
            $this->setFlash('error', 'Email sudah terdaftar. Gunakan email lain atau biarkan kosong');
            $this->redirect('auth/register');
            return;
        }
        
        // Remove password confirmation from data
        unset($data['password_confirm']);
        
        // Register user
        $userId = $userModel->register($data);
        
        if ($userId) {
            $this->setFlash('success', 'Registrasi berhasil! Silakan login');
            $this->redirect('auth/login');
        } else {
            $this->setFlash('error', 'Registrasi gagal. Silakan coba lagi');
            $this->redirect('auth/register');
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        // Clear all session variables
        $_SESSION = [];
        
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Start a new clean session for flash message
        session_start();
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Anda telah logout. Silakan login kembali.'
        ];
        
        // Redirect to login
        header('Location: ' . url('auth/login'));
        exit;
    }
    
    /**
     * Unauthorized access page
     */
    public function unauthorized() {
        $data = [
            'title' => 'Akses Ditolak'
        ];
        
        $this->render('auth/unauthorized', $data, null);
    }
    
    /**
     * Forgot password page
     */
    public function forgotPassword() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $data = [
            'title' => 'Lupa Sandi'
        ];
        
        $this->render('auth/forgot_password', $data, null);
    }
    
    /**
     * Process forgot password request
     */
    public function processForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/forgot-password');
            return;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRF()) {
            return;
        }
        
        $email = sanitize($this->post('email'));
        
        if (empty($email) || !$this->validateEmail($email)) {
            $this->setFlash('error', 'Masukkan format email yang valid');
            $this->redirect('auth/forgot-password');
            return;
        }
        
        // Find user by email
        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);
        
        if ($user) {
            // Generate unique secure token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration
            
            // Delete old tokens for this email
            $userModel->execute("DELETE FROM password_resets WHERE email = ?", [$email]);
            
            // Insert new token
            $userModel->execute(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)",
                [$email, $token, $expiresAt]
            );
            
            // Send email
            require_once UTILS_PATH . 'EmailSender.php';
            $emailSender = new EmailSender();
            $resetLink = url('auth/reset-password?token=' . $token);
            
            if ($emailSender->sendPasswordResetLink($email, $resetLink)) {
                $this->setFlash('success', 'Link reset password telah dikirim ke email Anda.');
            } else {
                $this->setFlash('error', 'Gagal mengirim email reset password. Silakan hubungi admin.');
            }
        } else {
            // For security, do not reveal if the email exists or not
            $this->setFlash('success', 'Link reset password telah dikirim ke email Anda jika email tersebut terdaftar.');
        }
        
        $this->redirect('auth/forgot-password');
    }
    
    /**
     * Reset password page
     */
    public function resetPassword() {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $token = sanitize($this->get('token'));
        
        if (empty($token)) {
            $this->setFlash('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
            $this->redirect('auth/login');
            return;
        }
        
        // Validate token
        $userModel = $this->model('User');
        $reset = $userModel->queryOne(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1",
            [$token]
        );
        
        if (!$reset) {
            $this->setFlash('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
            $this->redirect('auth/login');
            return;
        }
        
        $data = [
            'title' => 'Reset Sandi',
            'token' => $token,
            'email' => $reset['email']
        ];
        
        $this->render('auth/reset_password', $data, null);
    }
    
    /**
     * Process reset password
     */
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRF()) {
            return;
        }
        
        $token = sanitize($this->post('token'));
        $password = $this->post('password');
        $passwordConfirm = $this->post('password_confirm');
        
        if (empty($token) || empty($password) || empty($passwordConfirm)) {
            $this->setFlash('error', 'Semua field wajib diisi.');
            $this->redirect('auth/reset-password?token=' . $token);
            return;
        }
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $this->setFlash('error', 'Password minimal ' . PASSWORD_MIN_LENGTH . ' karakter.');
            $this->redirect('auth/reset-password?token=' . $token);
            return;
        }
        
        if ($password !== $passwordConfirm) {
            $this->setFlash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('auth/reset-password?token=' . $token);
            return;
        }
        
        // Validate token in database
        $userModel = $this->model('User');
        $reset = $userModel->queryOne(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1",
            [$token]
        );
        
        if (!$reset) {
            $this->setFlash('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
            $this->redirect('auth/login');
            return;
        }
        
        // Find user by email
        $user = $userModel->findByEmail($reset['email']);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('auth/login');
            return;
        }
        
        // Update user's password
        if ($userModel->changePassword($user['id'], $password)) {
            // Delete used token
            $userModel->execute("DELETE FROM password_resets WHERE email = ?", [$reset['email']]);
            
            $this->setFlash('success', 'Password Anda berhasil diperbarui. Silakan login dengan password baru.');
            $this->redirect('auth/login');
        } else {
            $this->setFlash('error', 'Gagal memperbarui password. Silakan coba lagi.');
            $this->redirect('auth/reset-password?token=' . $token);
        }
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        $user = currentUser();
        
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        
        switch ($user['role']) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'bpdas':
                $this->redirect('bpdas/dashboard');
                break;
            case 'operator_persemaian':
                $this->redirect('operator/dashboard');
                break;
            case 'pelaku_usaha':
                $this->redirect('pdb/langkah1');
                break;
            case 'public':
                $this->redirect('public/dashboard');
                break;
            default:
                $this->redirect('home');
        }
    }
}
