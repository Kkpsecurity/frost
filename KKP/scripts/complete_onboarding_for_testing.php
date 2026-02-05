<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MANUALLY COMPLETE ONBOARDING (FOR TESTING) ===\n\n";

$studentUnitId = 84638;
$userId = 2;

// Add fake ID card to verified data
$studentUnit = DB::table('student_unit')->where('id', $studentUnitId)->first();
$verified = json_decode($studentUnit->verified ?? '{}', true);

// Add fake ID card
$verified['id_card_path'] = 'validations/id_cards/test_id_card.jpg';
$verified['id_card_uploaded'] = true;
$verified['id_card_uploaded_at'] = now()->toIso8601String();
$verified['events'][] = [
    'event' => 'id_card_uploaded',
    'path' => 'validations/id_cards/test_id_card.jpg',
    'at' => now()->toIso8601String(),
    'source' => 'manual_testing'
];

DB::table('student_unit')
    ->where('id', $studentUnitId)
    ->update(['verified' => json_encode($verified)]);

echo "✅ Added fake ID card to verified data\n";

// Track onboarding completion
DB::table('student_activity')->insert([
    'user_id' => $userId,
    'student_unit_id' => $studentUnitId,
    'category' => 'agreement',
    'activity_type' => 'onboarding_completed',
    'description' => 'Onboarding completed (manual for testing)',
    'data' => json_encode([
        'completed_at' => now()->toIso8601String(),
        'test_mode' => true
    ]),
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Created onboarding_completed activity\n";

echo "\n🎉 Onboarding marked as complete for testing!\n";
echo "Student can now proceed to classroom.\n\n";
