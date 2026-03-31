<?php
$url = 'http://localhost/seedling-dashboard/seedling-dashboard/public/api/stock/push';

// Assuming we use the dummy key
$apiKey = 'dummy-key-for-bpdas-1';

$data = [
    'nursery_id' => 1,
    'seedling_type_id' => 1,
    'quantity' => 500,
    'notes' => 'Tested from API Push',
    'last_sync_timestamp' => '2026-03-11 12:45:00'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
