<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$course = App\Models\Course::find(3);

echo "Course ID 3 Data:\n";
echo "Title: " . $course->title . "\n";
echo "Title Long: " . ($course->title_long ?? 'null') . "\n";
echo "getCourseType(): " . $course->getCourseType() . "\n";
echo "\nAll Course attributes:\n";
print_r($course->toArray());
