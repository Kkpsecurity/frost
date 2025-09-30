<?php

// Simple test to simulate what React is doing
echo "🌐 Testing HTTP Request\n";
echo "========================\n\n";

// Use cURL to test the endpoint
$url = 'http://localhost/admin/instructors/data/lessons/today';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "Response:\n";
echo $response;
echo "\n\n";

// Check if it's valid JSON
$jsonData = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "✅ Valid JSON response\n";
    if (isset($jsonData['lessons']) && count($jsonData['lessons']) > 0) {
        echo "✅ Found " . count($jsonData['lessons']) . " lessons\n";
    } else {
        echo "❌ No lessons found in response\n";
    }
} else {
    echo "❌ Invalid JSON response\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
}
