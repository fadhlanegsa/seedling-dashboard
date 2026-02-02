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
        
        // Authenticate user
        $userModel = $this->model('User');
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
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
            'username' => sanitize($this->post('username')),
            'email' => sanitize($this->post('email')),
            'password' => $this->post('password'),
            'password_confirm' => $this->post('password_confirm'),
            'full_name' => sanitize($this->post('full_name')),
            'phone' => sanitize($this->post('phone')),
            'nik' => sanitize($this->post('nik'))
        ];
        
        // Validate required fields
        $errors = $this->validateRequired($data, [
            'username', 'email', 'password', 'password_confirm', 
            'full_name', 'phone', 'nik'
        ]);
        
        if (!empty($errors)) {
            $this->setFlash('error', 'Semua field harus diisi');
            $this->redirect('auth/register');
            return;
        }
        
        // Validate email
        if (!$this->validateEmail($data['email'])) {
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
        
        // Check if email exists
        if ($userModel->emailExists($data['email'])) {
            $this->setFlash('error', 'Email sudah terdaftar');
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
            case 'public':
                $this->redirect('public/dashboard');
                break;
            default:
                $this->redirect('home');
        }
    }
}
