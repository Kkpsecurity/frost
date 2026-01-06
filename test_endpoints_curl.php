<?php
// Test the instructor endpoints directly via curl

echo "=== Testing /admin/instructors/instructor/data ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin/instructors/instructor/data');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $httpCode\n";
echo "Response:\n$response\n\n";

echo "=== Testing /admin/instructors/classroom/data ===\n";
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin/instructors/classroom/data');
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $httpCode2\n";
echo "Response:\n$response2\n";

curl_close($ch);
