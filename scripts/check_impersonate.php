<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$user = new App\Models\User();
$methods = get_class_methods($user);

echo "Looking for impersonate methods:\n";
foreach ($methods as $method) {
    if (stripos($method, 'impersonate') !== false) {
        echo "Found: $method\n";
    }
}

// Also check traits
$traits = class_uses($user);
foreach ($traits as $trait) {
    if (stripos($trait, 'impersonate') !== false) {
        echo "Trait: $trait\n";
    }
}
