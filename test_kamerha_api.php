<?php
/**
 * Test Script: Kamerha PostgREST API
 * 
 * Jalankan via browser: http://localhost/seedling-dashboard/test_kamerha_api.php
 * 
 * Menguji berbagai endpoint dan metode autentikasi untuk
 * menemukan kombinasi yang berhasil terhubung ke Kamerha API.
 */

$BASE_URL = 'https://postgrestdatatag.ditrh.synology.me';
$TOKEN    = 'inipunyaaplikasikamerha22344hu890cvbccfgzzd90jx';

/**
 * Fungsi helper untuk HTTP request
 */
function kHit(string $method, string $url, array $headers = [], array $body = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false, // untuk debug
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_VERBOSE        => false,
    ]);
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $resp    = curl_exec($ch);
    $code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err     = curl_error($ch);
    curl_close($ch);
    return ['code' => $code, 'body' => $resp, 'error' => $err];
}

function showResult(string $label, array $result, bool $expectSuccess = false): void {
    $isOk   = $result['code'] >= 200 && $result['code'] < 300;
    $color   = $isOk ? '#00b894' : '#d63031';
    $icon    = $isOk ? '✅' : '❌';
    $preview = $result['error'] ?: (strlen($result['body']) > 300 ? substr($result['body'], 0, 300) . '...' : $result['body']);
    echo "<div style='margin:8px 0; padding:10px 14px; background:#1e272e; border-radius:6px; border-left:3px solid $color;'>";
    echo "<strong style='color:$color;'>$icon HTTP {$result['code']}</strong> &nbsp; <span style='color:#b2bec3;'>$label</span><br>";
    echo "<pre style='margin:6px 0 0; font-size:11px; color:#dfe6e9; white-space:pre-wrap;'>" . htmlspecialchars($preview) . "</pre>";
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kamerha API Test</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Consolas', monospace; 
            background: #0f1923; 
            color: #ecf0f1; 
            padding: 24px;
            max-width: 900px;
            margin: 0 auto;
        }
        h1 { color: #00cec9; }
        h2 { color: #a29bfe; border-bottom: 1px solid #2d3436; padding-bottom: 8px; margin-top: 30px; }
        .info { background: #1e272e; border-left: 3px solid #0984e3; padding: 10px 14px; border-radius: 6px; margin: 10px 0; }
        .warning { background: #1e272e; border-left: 3px solid #fdcb6e; padding: 10px 14px; border-radius: 6px; margin: 10px 0; }
        code { background: #2d3436; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>
<h1>🔌 Kamerha PostgREST API — Test Suite</h1>

<div class="info">
    <strong>Base URL:</strong> <code><?= $BASE_URL ?></code><br>
    <strong>Token:</strong> <code><?= substr($TOKEN, 0, 20) ?>...</code>
</div>

<div class="warning">
    <strong>⚠️ Error yang Diketahui:</strong> PostgREST mengharapkan JWT (3 bagian: header.payload.signature),
    token saat ini hanya 1 bagian → <code>PGRST301</code>.<br>
    Test ini mengeksplorasi berbagai cara autentikasi untuk menemukan yang berhasil.
</div>

<?php
/* ================================================================
   TEST 1: Bearer token langsung (kemungkinan akan 401)
   ================================================================ */
echo "<h2>1️⃣  Bearer Token Standard</h2>";
$r = kHit('GET', $BASE_URL . '/', ["Authorization: Bearer $TOKEN", "Accept: application/json"]);
showResult("GET / dengan Bearer Token", $r);

/* ================================================================
   TEST 2: Tanpa auth (anon access?)
   ================================================================ */
echo "<h2>2️⃣  Tanpa Autentikasi (Anonymous)</h2>";
$r = kHit('GET', $BASE_URL . '/', ["Accept: application/json"]);
showResult("GET / tanpa auth header", $r);

/* ================================================================
   TEST 3: Coba beberapa nama tabel umum
   ================================================================ */
echo "<h2>3️⃣  Probe Nama Tabel Umum</h2>";
$tables = [
    'datatag', 'geotag', 'scan_log', 'seedling_qr', 'qr_index',
    'seedling_index', 'tree_tag', 'tagging', 'pohon', 'bibit',
    'planting_record', 'distribution', 'seedling_qr_index', 'geotag_records'
];
foreach ($tables as $tbl) {
    $r = kHit('GET', "$BASE_URL/$tbl", ["Authorization: Bearer $TOKEN", "Accept: application/json", "Range-Unit: items", "Range: 0-0"]);
    showResult("GET /$tbl", $r);
}

/* ================================================================
   TEST 4: Token sebagai apikey header (alternatif)
   ================================================================ */
echo "<h2>4️⃣  Alternatif: apikey Header</h2>";
$r = kHit('GET', $BASE_URL . '/', ["apikey: $TOKEN", "Accept: application/json"]);
showResult("GET / dengan apikey header", $r);

/* ================================================================
   TEST 5: Token sebagai x-api-key header
   ================================================================ */
$r = kHit('GET', $BASE_URL . '/', ["x-api-key: $TOKEN", "Accept: application/json"]);
showResult("GET / dengan x-api-key header", $r);

/* ================================================================
   TEST 6: Token sebagai query parameter
   ================================================================ */
$r = kHit('GET', "$BASE_URL/?token=$TOKEN", ["Accept: application/json"]);
showResult("GET /?token=... (query param)", $r);

/* ================================================================
   TEST 7: Basic auth
   ================================================================ */
$r = kHit('GET', $BASE_URL . '/', ["Authorization: Basic " . base64_encode("api:$TOKEN"), "Accept: application/json"]);
showResult("GET / dengan Basic Auth (api:token)", $r);

/* ================================================================
   TEST 8: PostgREST RPC endpoints
   ================================================================ */
echo "<h2>5️⃣  PostgREST RPC Endpoints</h2>";
$rpcNames = ['login', 'signup', 'authenticate', 'get_token', 'postgrest_version'];
foreach ($rpcNames as $rpc) {
    $r = kHit('POST', "$BASE_URL/rpc/$rpc", 
              ["Authorization: Bearer $TOKEN", "Content-Type: application/json", "Accept: application/json"],
              ['token' => $TOKEN]);
    showResult("POST /rpc/$rpc", $r);
}

/* ================================================================
   Ringkasan & Rekomendasi
   ================================================================ */
?>

<h2>📋 Ringkasan & Rekomendasi</h2>
<div class="info">
    <p><strong>Error <code>PGRST301</code>:</strong> PostgREST bukan menggunakan plain API key di Authorization header.
    Ia menggunakan JWT (JSON Web Token) yang di-generate dari secret key yang dikonfigurasi saat deploy PostgREST server.</p>
    
    <p><strong>Request ke Tim Kamerha:</strong></p>
    <ol>
        <li>Kirimkan <strong>JWT Token yang valid</strong> (format: <code>xxxxx.yyyyy.zzzzz</code> — tiga bagian dipisah titik)</li>
        <li>Atau aktifkan <strong>anonymous role</strong> di PostgREST config mereka: <code>PGRST_DB_ANON_ROLE=anon</code></li>
        <li>Konfirmasi <strong>nama-nama tabel</strong> yang harus kita POST/GET (<code>seedling_qr_index</code>? <code>geotag_records</code>? dll)</li>
        <li>Konfirmasi <strong>schema kolom</strong> tabel penerima QR index (field apa yang wajib diisi)</li>
    </ol>
    
    <p>Semua code integrasi sudah siap di:</p>
    <ul>
        <li><code>/utils/KamerhaService.php</code> — Service class (tinggal update nama tabel + JWT)</li>
        <li><code>/controllers/KamerhaController.php</code> — Controller dengan endpoints push/pull</li>
        <li><code>/views/kamerha/sync-log.php</code> — Dashboard monitoring</li>
        <li><code>/database/migrate_kamerha_integration.php</code> — Migrasi DB</li>
    </ul>
</div>

</body>
</html>
