<?php

$ch = curl_init('https://opendata.moph.go.th/api/report_data');
$payload = json_encode([
    'tableName' => 's_anc_quality',
    'year' => '2569',
    'province' => '34',
    'type' => 'json'
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
}
curl_close($ch);

$data = json_decode($response, true);
echo "Total records in array: " . count($data) . "\n";
if (is_array($data) && count($data) > 0) {
    echo "First item:\n";
    print_r($data[0]);
    echo "Second item:\n";
    print_r($data[1]);
}
