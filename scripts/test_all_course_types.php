<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing all courses:\n";
echo "====================\n";

$courses = App\Models\Course::all();

foreach ($courses as $course) {
    echo "ID: {$course->id}\n";
    echo "Title: {$course->title}\n";
    echo "Title Long: " . ($course->title_long ?? 'null') . "\n";
    echo "Type: {$course->getCourseType()}\n";
    echo "---\n";
}
