<?php
/**
 * Debug URL Generation
 * Test file to check if url() and asset() functions generate correct paths
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>URL Generation Debug</h1>";
echo "<hr>";

echo "<h2>Configuration:</h2>";
echo "<strong>APP_URL:</strong> " . APP_URL . "<br>";
echo "<strong>BASE_PATH:</strong> " . BASE_PATH . "<br>";
echo "<hr>";

echo "<h2>URL Function Tests:</h2>";
echo "<strong>url(''):</strong> " . url('') . "<br>";
echo "<strong>url('home'):</strong> " . url('home') . "<br>";
echo "<strong>url('home/search'):</strong> " . url('home/search') . "<br>";
echo "<strong>url('auth/login'):</strong> " . url('auth/login') . "<br>";
echo "<hr>";

echo "<h2>Asset Function Tests:</h2>";
echo "<strong>asset('css/style.css'):</strong> " . asset('css/style.css') . "<br>";
echo "<strong>asset('js/main.js'):</strong> " . asset('js/main.js') . "<br>";
echo "<hr>";

echo "<h2>Expected URLs:</h2>";
echo "<strong>Homepage:</strong> http://localhost/seedling-dashboard/seedling-dashboard/public/<br>";
echo "<strong>CSS File:</strong> http://localhost/seedling-dashboard/seedling-dashboard/public/css/style.css<br>";
echo "<strong>JS File:</strong> http://localhost/seedling-dashboard/seedling-dashboard/public/js/main.js<br>";
