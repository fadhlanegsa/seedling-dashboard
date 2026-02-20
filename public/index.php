<?php
/**
 * Application Entry Point
 * Dashboard Stok Bibit Persemaian Indonesia
 */

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
$request = $_SERVER['REQUEST_URI'];
$basePath = BASE_PATH;

// Remove base path and query string
$path = str_replace($basePath, '', parse_url($request, PHP_URL_PATH));
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
