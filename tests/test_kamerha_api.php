<?php
/**
 * Test Script: Kamerha PostgREST API Connection
 * Jalankan via CLI: php tests/test_kamerha_api.php
 * 
 * Test minimal:
 *   1. Generate JWT HS256
 *   2. GET /batanghari?limit=1 (read test)
 *   3. Verifikasi response 200 OK + JSON valid
 */

echo "=== Kamerha API Integration Test ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n\n";

// --- Config ---
$baseUrl = 'https://postgrestdatatag.ditrh.synology.me';
$secret  = 'inipunyaaplikasikamerha22344hu890cvbccfgzzd90jx';

// --- 1. Generate JWT HS256 ---
echo "[1] Generating JWT HS256 token...\n";

$header  = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
$payload = json_encode(['role' => 'authenticated', 'exp' => time() + 3600]);

$b64Header  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$b64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$signature    = hash_hmac('sha256', "$b64Header.$b64Payload", $secret, true);
$b64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

$jwt = "$b64Header.$b64Payload.$b64Signature";
echo "    JWT: " . substr($jwt, 0, 40) . "...\n";
echo "    ✅ Token generated OK\n\n";

// --- 2. GET /batanghari?limit=1 ---
echo "[2] GET $baseUrl/batanghari?limit=1\n";

$ch = curl_init("$baseUrl/batanghari?limit=1");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER     => [
        "Authorization: Bearer $jwt",
        "apikey: $secret",
        "Accept: application/json",
    ],
]);

$body     = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
$totalTime = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME), 2);
curl_close($ch);

echo "    HTTP Status : $httpCode\n";
echo "    Latency     : {$totalTime}s\n";

if ($error) {
    echo "    ❌ cURL Error: $error\n";
    exit(1);
}

$data = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "    ❌ Invalid JSON: " . json_last_error_msg() . "\n";
    echo "    Raw: " . substr($body, 0, 200) . "\n";
    exit(1);
}

if ($httpCode >= 200 && $httpCode < 300) {
    echo "    ✅ Response 200 OK - JSON valid\n";
    echo "    Records returned: " . count($data) . "\n";
    if (!empty($data)) {
        echo "    Columns: " . implode(', ', array_keys($data[0])) . "\n";
    }
} else {
    echo "    ❌ HTTP $httpCode\n";
    echo "    Body: " . substr($body, 0, 300) . "\n";
    exit(1);
}

echo "\n=== ✅ ALL TESTS PASSED ===\n";
