<?php
/**
 * Application Configuration File
 * Dashboard Stok Bibit Persemaian Indonesia
 */

// Prevent direct access
defined('APP_PATH') or define('APP_PATH', dirname(__DIR__));

// Application Settings
define('APP_NAME', 'Dashboard Stok Bibit Persemaian Indonesia');
define('APP_VERSION', '1.0.0');

// Dynamic URL Detection
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']); // e.g., /seedling-dashboard/public

// Remove '/public' from the script dir to get the app root URL if it exists in the path
$appPath = str_replace('/public', '', $scriptDir);

// Define APP_URL dynamically
// If running at root (bibitgratis.com), appPath might be empty or /
define('APP_URL', $protocol . '://' . $host . rtrim($appPath, '/\\'));
define('BASE_PATH', $scriptDir);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'wast6986_db_bibit');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_NAME', 'seedling_session');
define('SESSION_LIFETIME', 7200); // 2 hours in seconds

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File Upload Settings
define('UPLOAD_PATH', APP_PATH . '/public/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);

// Email Configuration (Update with your SMTP settings)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@seedling-dashboard.id');
define('SMTP_FROM_NAME', 'Seedling Dashboard');
define('SMTP_ENCRYPTION', 'tls'); // tls or ssl

// Pagination Settings
define('ITEMS_PER_PAGE', 10);
define('SEARCH_RESULTS_PER_PAGE', 12);

// Date & Time Settings
define('TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(TIMEZONE);
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// Application Paths
define('VIEWS_PATH', APP_PATH . '/views/');
define('MODELS_PATH', APP_PATH . '/models/');
define('CONTROLLERS_PATH', APP_PATH . '/controllers/');
define('CORE_PATH', APP_PATH . '/core/');
define('UTILS_PATH', APP_PATH . '/utils/');

// Asset Paths
define('CSS_PATH', BASE_PATH . '/css/');
define('JS_PATH', BASE_PATH . '/js/');
define('IMG_PATH', BASE_PATH . '/images/');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Logging
define('LOG_PATH', APP_PATH . '/logs/');
define('LOG_ERRORS', true);

// Request Number Format
define('REQUEST_NUMBER_PREFIX', 'REQ');
define('REQUEST_NUMBER_FORMAT', 'Y-m'); // Year-Month format

// PDF Settings
define('PDF_LOGO_PATH', APP_PATH . '/public/images/logo-kementerian.png');
define('PDF_FONT', 'Arial');
define('PDF_FONT_SIZE', 11);

// Seedling Categories
define('SEEDLING_CATEGORIES', [
    'Tanaman Kayu-Kayuan',
    'HHBK',
    'Tanaman Obat',
    'Bambu',
    'Mangrove',
    'Estetika, Pakan, Dll'
]);

// Request Status
define('REQUEST_STATUS', [
    'pending' => 'Menunggu Persetujuan',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    'delivered' => 'Sudah Diserahkan'
]);

// User Roles
define('USER_ROLES', [
    'admin' => 'Administrator',
    'bpdas' => 'BPDAS',
    'operator_persemaian' => 'Operator Persemaian',
    'public' => 'Masyarakat'
]);

// Helper function to get full URL
function url($path = '') {
    $base = APP_URL . '/public';
    return $base . '/' . ltrim($path, '/');
}

// Helper function to get asset URL
function asset($path = '') {
    $base = APP_URL . '/public';
    return $base . '/' . ltrim($path, '/');
}

// Helper function to redirect
function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to get current user
function currentUser() {
    return $_SESSION['user'] ?? null;
}

// Helper function to check user role
function hasRole($role) {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

// Helper function to format date
function formatDate($date, $format = DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false || $timestamp < 0) {
        return '-';
    }
    return date($format, $timestamp);
}

// Helper function to format number
function formatNumber($number) {
    return number_format((float)($number ?? 0), 0, ',', '.');
}

// Helper function to format land area (remove trailing zeros)
function formatLandArea($area) {
    // Format with 3 decimals first
    $formatted = number_format($area, 3, '.', '');
    // Remove trailing zeros and decimal point if not needed
    $formatted = rtrim($formatted, '0');
    $formatted = rtrim($formatted, '.');
    return $formatted;
}

// Helper function to sanitize input
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Helper function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Helper function to verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Helper function to generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

// Helper function to log errors
function logError($message) {
    if (LOG_ERRORS) {
        $logFile = LOG_PATH . 'error_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

// Rate limiting for anti-DDoS protection
function checkRateLimit($action = 'login', $maxAttempts = 5, $timeWindow = 300) {
    // Use IP address as identifier
    $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = "rate_limit_{$action}_{$identifier}";
    
    // Initialize session array if not exists
    if (!isset($_SESSION['rate_limits'])) {
        $_SESSION['rate_limits'] = [];
    }
    
    // Get current attempts
    $now = time();
    $attempts = $_SESSION['rate_limits'][$key] ?? ['count' => 0, 'first_attempt' => $now, 'locked_until' => 0];
    
    // Check if currently locked
    if ($attempts['locked_until'] > $now) {
        $remainingTime = $attempts['locked_until'] - $now;
        $minutes = ceil($remainingTime / 60);
        return [
            'allowed' => false,
            'message' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$minutes} menit.",
            'remaining_time' => $remainingTime
        ];
    }
    
    // Reset if time window has passed
    if ($now - $attempts['first_attempt'] > $timeWindow) {
        $attempts = ['count' => 0, 'first_attempt' => $now, 'locked_until' => 0];
    }
    
    // Increment attempt count
    $attempts['count']++;
    
    // Check if exceeded max attempts
    if ($attempts['count'] > $maxAttempts) {
        // Lock for 15 minutes
        $lockDuration = 900; // 15 minutes
        $attempts['locked_until'] = $now + $lockDuration;
        $_SESSION['rate_limits'][$key] = $attempts;
        
        $minutes = ceil($lockDuration / 60);
        return [
            'allowed' => false,
            'message' => "Terlalu banyak percobaan gagal. Akun diblokir sementara selama {$minutes} menit.",
            'remaining_time' => $lockDuration
        ];
    }
    
    // Update attempts
    $_SESSION['rate_limits'][$key] = $attempts;
    
    return [
        'allowed' => true,
        'attempts' => $attempts['count'],
        'max_attempts' => $maxAttempts
    ];
}

// Reset rate limit (call after successful login)
function resetRateLimit($action = 'login') {
    $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = "rate_limit_{$action}_{$identifier}";
    
    if (isset($_SESSION['rate_limits'][$key])) {
        unset($_SESSION['rate_limits'][$key]);
    }
}

// Clean up old rate limit entries (call periodically)
function cleanupRateLimits() {
    if (!isset($_SESSION['rate_limits'])) {
        return;
    }
    
    $now = time();
    foreach ($_SESSION['rate_limits'] as $key => $data) {
        // Remove entries older than 1 hour
        if (isset($data['first_attempt']) && ($now - $data['first_attempt']) > 3600) {
            unset($_SESSION['rate_limits'][$key]);
        }
    }
}

// Create necessary directories if they don't exist
$directories = [UPLOAD_PATH, LOG_PATH];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
