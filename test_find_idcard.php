<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Validation Record Details ===" . PHP_EOL . PHP_EOL;

$validation = App\Models\Validation::where('course_auth_id', 2)->first();

if ($validation) {
    echo "Validation Record:" . PHP_EOL;
    echo "ID: {$validation->id}" . PHP_EOL;
    echo "Course Auth ID: {$validation->course_auth_id}" . PHP_EOL;
    echo "Student Unit ID: {$validation->student_unit_id}" . PHP_EOL;
    echo "Status: {$validation->status}" . PHP_EOL;
    echo "Type: {$validation->type}" . PHP_EOL;
    echo "Path: {$validation->path}" . PHP_EOL;
    echo "Created: {$validation->created_at}" . PHP_EOL;
    echo "Updated: {$validation->updated_at}" . PHP_EOL . PHP_EOL;

    // Get all attributes
    echo "All Attributes:" . PHP_EOL;
    print_r($validation->getAttributes());
    echo PHP_EOL;
}

// Check StudentUnit for ID card in verified JSON
echo "=== StudentUnit Verified Data ===" . PHP_EOL . PHP_EOL;
$studentUnit = App\Models\StudentUnit::where('course_auth_id', 2)
    ->orderByDesc('course_date_id')
    ->first();

if ($studentUnit) {
    $verified = json_decode($studentUnit->getRawOriginal('verified'), true) ?: [];
    echo "StudentUnit ID: {$studentUnit->id}" . PHP_EOL;
    echo "Verified JSON:" . PHP_EOL;
    print_r($verified);
    echo PHP_EOL;

    if (!empty($verified['id_card_path'])) {
        $fullPath = storage_path('app/public/' . $verified['id_card_path']);
        echo "ID Card Path from verified: {$verified['id_card_path']}" . PHP_EOL;
        echo "Full path: {$fullPath}" . PHP_EOL;
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . PHP_EOL;
    }
}

// Search for any ID card files
echo PHP_EOL . "=== Searching for ID Card Files ===" . PHP_EOL;
$searchPaths = [
    storage_path('app/public/idcards'),
    storage_path('app/public/id_cards'),
    storage_path('app/public/validations'),
    storage_path('app/public/media/validations/idcards'),
];

foreach ($searchPaths as $path) {
    if (is_dir($path)) {
        echo "Checking: {$path}" . PHP_EOL;
        $files = glob($path . '/*2*');
        if (!empty($files)) {
            foreach ($files as $file) {
                echo "  Found: " . basename($file) . PHP_EOL;
            }
        }
    }
}
