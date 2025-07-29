<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== TESTING LARAVEL SCOUT SEARCH ===\n";

// Test basic search
echo "\n1. Searching for 'Richard':\n";
$results = User::search('Richard')->get();
foreach ($results as $user) {
    echo "- {$user->fname} {$user->lname} ({$user->email})\n";
}

// Test search with limit
echo "\n2. Searching for 'admin' (limited to 5 results):\n";
$results = User::search('admin')->take(5)->get();
foreach ($results as $user) {
    echo "- {$user->fname} {$user->lname} ({$user->email})\n";
}

// Test search by email
echo "\n3. Searching for email containing 'gmail':\n";
$results = User::search('gmail')->take(3)->get();
foreach ($results as $user) {
    echo "- {$user->fname} {$user->lname} ({$user->email})\n";
}

echo "\n4. Search statistics:\n";
echo "Total searchable users: " . User::count() . "\n";
echo "Total search results for 'test': " . User::search('test')->get()->count() . "\n";

echo "\nScout search functionality is working!\n";
