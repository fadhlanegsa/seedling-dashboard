<?php
/**
 * Application Entry Point
 * Dashboard Stok Bibit Persemaian Indonesia
 */

// ─── Security Headers ──────────────────────────────────────────────────────
// Hide PHP version from response headers
header_remove('X-Powered-By');
@ini_set('expose_php', 'Off');


header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
header("Content-Security-Policy: default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; "
    . "style-src 'self' 'unsafe-inline' https:; "
    . "img-src 'self' data: blob: https:; "
    . "font-src 'self' data: https:; "
    . "connect-src 'self' https:; "
    . "frame-src 'self' https:; "
    . "object-src 'none';"
);
// ───────────────────────────────────────────────────────────────────────────

// Configure session cookies for security before starting session
session_set_cookie_params([
    'lifetime' => 7200,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Composer Autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load core classes
require_once CORE_PATH . 'Model.php';
require_once CORE_PATH . 'View.php';
require_once CORE_PATH . 'Controller.php';

// Simple router
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName); // e.g., /seedling-dashboard/seedling-dashboard/public
$appRoot = str_replace('/public', '', $basePath); // e.g., /seedling-dashboard/seedling-dashboard

// The path we want is everything after the appRoot, minus the potential /public prefix
$path = $request;
if ($appRoot !== '/' && strpos($path, $appRoot) === 0) {
    $path = substr($path, strlen($appRoot));
}
$path = ltrim($path, '/');
if (strpos($path, 'public/') === 0) {
    $path = substr($path, 7);
} elseif ($path === 'public') {
    $path = '';
}
$path = trim($path, '/');

// Default route - landing page
if (empty($path) || $path === 'home') {
    $path = 'public/landing';
}

// Parse route
$parts = explode('/', $path);

// Handle special routing for seed-sources
if ((($parts[0] === 'admin' || $parts[0] === 'bpdas') && isset($parts[1]) && $parts[1] === 'seed-sources')) {
    $controllerName = 'SeedSourceController';
    $action = isset($parts[2]) ? $parts[2] : 'index';
    $params = array_slice($parts, 3);
} 
// Handle special routing for admin/nurseries/*
elseif ($parts[0] === 'admin' && isset($parts[1]) && $parts[1] === 'nurseries') {
    $controllerName = 'AdminController';
    $subAction = isset($parts[2]) ? $parts[2] : null;
    
    if (!$subAction) {
        $action = 'nurseries';
        $params = [];
    } elseif ($subAction === 'create') {
        $action = 'createNursery';
        $params = [];
    } elseif ($subAction === 'store') {
        $action = 'storeNursery';
        $params = [];
    } elseif ($subAction === 'edit') {
        $action = 'editNursery';
        $params = array_slice($parts, 3);
    } elseif ($subAction === 'update') {
        $action = 'updateNursery';
        $params = [];
    } elseif ($subAction === 'delete') {
        $action = 'deleteNursery';
        $params = array_slice($parts, 3);
    } else {
        $action = 'nurseries';
        $params = [];
    }
}
// Handle special routing for operator/*
elseif ($parts[0] === 'operator') {
    $controllerName = 'OperatorController';
    $action = isset($parts[1]) ? $parts[1] : 'dashboard';
    
    if ($action === 'stock') {
        $subAction = isset($parts[2]) ? $parts[2] : null;
        if (!$subAction) {
            $action = 'stock';
            $params = [];
        } elseif ($subAction === 'add' || $subAction === 'form') {
            $action = 'stockForm';
            $params = [];
        } elseif ($subAction === 'edit') {
            $action = 'stockForm';
            $params = array_slice($parts, 3);
        } elseif ($subAction === 'save') {
            $action = 'saveStock';
            $params = [];
        } else {
            $params = array_slice($parts, 2);
        }
    } else {
        // Convert kebab-case to camelCase
        if (strpos($action, '-') !== false) {
            $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
        }
        $params = array_slice($parts, 2);
    }
}
// Handle special routing for api/*
elseif ($parts[0] === 'api') {
    $subModule = isset($parts[1]) ? $parts[1] : '';
    
    // Disable CSRF for API routes generally or handle via headers
    // Actually, in our ApiController, we don't enforce CSRF, we enforce x-api-key
    
    if ($subModule === 'stock') {
        $controllerName = 'ApiStockController';
        $action = isset($parts[2]) ? $parts[2] : 'index';
        $params = array_slice($parts, 3);
    } else {
        $controllerName = 'ApiController';
        $action = 'index';
        $params = [];
    }
}
// Handle special routing for seedling-admin/*
elseif ($parts[0] === 'seedling-admin') {
    $controllerName = 'SeedlingAdminController';
    $action = isset($parts[1]) ? $parts[1] : 'index';
    // Convert kebab-case to camelCase for action
    if (strpos($action, '-') !== false) {
        $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
    }
    $params = array_slice($parts, 2);
}
else {
    // Handle special cases for acronyms
    $controller = $parts[0];
    if ($controller === 'bpdas') {
        $controllerName = 'BPDASController';
    } else {
        $controllerName = ucfirst($controller) . 'Controller';
    }
    
    // Convert kebab-case to camelCase for action
    $action = isset($parts[1]) ? $parts[1] : 'index';
    if (strpos($action, '-') !== false) {
        $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
    }
    
    $params = array_slice($parts, 2);
}


// Controller file path
$controllerFile = CONTROLLERS_PATH . $controllerName . '.php';

// Check if controller exists
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        // Check if method exists
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
        } else {
            // Method not found - 404
            http_response_code(404);
            require_once CONTROLLERS_PATH . 'ErrorController.php';
            $errorController = new ErrorController();
            $errorController->notFound();
        }
    } else {
        // Class not found - 404
        http_response_code(404);
        require_once CONTROLLERS_PATH . 'ErrorController.php';
        $errorController = new ErrorController();
        $errorController->notFound();
    }
} else {
    // Controller not found - 404
    http_response_code(404);
    require_once CONTROLLERS_PATH . 'ErrorController.php';
    $errorController = new ErrorController();
    $errorController->notFound();
}
