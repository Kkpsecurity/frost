<?php

// Simple test using Laravel's artisan tinker approach

echo "Testing CourseDatesService via HTTP endpoint...\n";

// Use curl to test the endpoint instead - Laragon typically uses port 80
$url = 'http://frost.test/test-service';
echo "Making request to: {$url}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Test Script');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "CURL ERROR: {$error}\n";
} else {
    echo "HTTP Status: {$httpCode}\n";
    echo "Response:\n";
    echo $response . "\n";
}
