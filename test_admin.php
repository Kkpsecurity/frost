<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

try {
    echo "Testing Admin model...\n";
    $admin = App\Models\Admin::first();

    if ($admin) {
        echo "Admin found: " . $admin->fname . " " . $admin->lname . "\n";
        echo "Role: " . ($admin->Role ? $admin->Role->name : 'N/A') . "\n";
        echo "Avatar: " . ($admin->avatar ?: 'None') . "\n";
        echo "Use Gravatar: " . ($admin->use_gravatar ? 'Yes' : 'No') . "\n";
    } else {
        echo "No admin users found\n";
    }

    echo "\nTesting DataTables query...\n";
    $admins = App\Models\Admin::with('Role')->select('users.*')->get();
    echo "Found " . $admins->count() . " admin users\n";

    foreach ($admins as $admin) {
        echo "- " . $admin->fname . " " . $admin->lname . " (" . ($admin->Role->name ?? 'N/A') . ")\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
