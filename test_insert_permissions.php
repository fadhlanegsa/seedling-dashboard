<?php
$BASE_URL = 'https://postgrestdatatag.ditrh.synology.me';
$TOKEN    = 'inipunyaaplikasikamerha22344hu890cvbccfgzzd90jx';

function kHit(string $method, string $url, array $headers = [], array $body = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CUSTOMREQUEST  => $method,
    ]);
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $resp    = curl_exec($ch);
    $code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $resp];
}

$tables = [
    'barito', 'batanghari', 'web_sessions', 'remu_ransiki', 'users',
    'direktorat', 'memberamo', 'musi', 'kahayan', 'ketahun', 'palu_poso',
    'gabungan_geotagging', 'kapuas', 'kegiatan_rhl'
];

$headers = [
    "apikey: $TOKEN",
    "Content-Type: application/json",
    "Accept: application/json"
];

echo "Testing write permissions (POST) on all tables...\n";
foreach ($tables as $table) {
    // Attempt POSTing empty array (becomes {} in JSON if we force it, or [] which is array of objects in PostgREST)
    // PostgREST accepts both single object or array of objects. Let's send a single object by passing a non-empty array
    // or just an empty array which encodes to [] in json.
    $r = kHit('POST', "$BASE_URL/$table", $headers, ['dummy_field' => 'test']);
    $data = json_decode($r['body'], true);
    
    echo "Table '$table' -> HTTP {$r['code']}: ";
    if (isset($data['code'])) {
        echo "PostgREST Code: {$data['code']} - " . ($data['message'] ?? '') . "\n";
    } else {
        echo "Response: " . substr($r['body'], 0, 150) . "\n";
    }
}
?>
