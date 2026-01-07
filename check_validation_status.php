<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Validation;
use App\Models\StudentUnit;

// Find the most recent headshot validation
$headshotValidation = Validation::whereNotNull('student_unit_id')
    ->orderBy('id', 'desc')
    ->first();

echo "=== Headshot Validation ===\n";
if ($headshotValidation) {
    echo "ID: {$headshotValidation->id}\n";
    echo "Status: {$headshotValidation->status}\n";
    echo "Student Unit ID: {$headshotValidation->student_unit_id}\n";
    echo "ID Type: {$headshotValidation->id_type}\n";
    echo "Reject Reason: {$headshotValidation->reject_reason}\n";
    
    $studentUnit = StudentUnit::find($headshotValidation->student_unit_id);
    if ($studentUnit) {
        echo "\n=== StudentUnit ===\n";
        echo "ID: {$studentUnit->id}\n";
        echo "Course Auth ID: {$studentUnit->course_auth_id}\n";
        echo "Verified: " . ($studentUnit->verified ? 'true' : 'false') . "\n";
        
        // Find ID card validation
        $idCardValidation = Validation::where('course_auth_id', $studentUnit->course_auth_id)->first();
        if ($idCardValidation) {
            echo "\n=== ID Card Validation ===\n";
            echo "ID: {$idCardValidation->id}\n";
            echo "Status: {$idCardValidation->status}\n";
            echo "ID Type: {$idCardValidation->id_type}\n";
        } else {
            echo "\nNo ID card validation found\n";
        }
    }
} else {
    echo "No headshot validation found\n";
}
