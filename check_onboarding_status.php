<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ONBOARDING STATUS CHECK ===\n\n";

$userId = 2; // Richard Clark
$courseDateId = 10757;

// Get student unit
$studentUnit = DB::table('student_unit')
    ->where('course_date_id', $courseDateId)
    ->where('course_auth_id', 2)
    ->first();

if (!$studentUnit) {
    echo "❌ No StudentUnit found\n";
    exit;
}

echo "StudentUnit ID: {$studentUnit->id}\n\n";

// Check course agreement
$courseAuth = DB::table('course_auths')
    ->where('id', $studentUnit->course_auth_id)
    ->first();

$termsAccepted = !empty($courseAuth->agreed_at);
echo "1. Terms Accepted: " . ($termsAccepted ? "✅ YES" : "❌ NO") . "\n";
if ($courseAuth) {
    echo "   - CourseAuth agreed_at: " . ($courseAuth->agreed_at ?? 'NULL') . "\n";
}

// Check rules acceptance
$rulesAccepted = DB::table('student_activity')
    ->where('user_id', $userId)
    ->where('student_unit_id', $studentUnit->id)
    ->where('activity_type', 'rules_accepted')
    ->exists();

echo "\n2. Rules Accepted: " . ($rulesAccepted ? "✅ YES" : "❌ NO") . "\n";

if ($rulesAccepted) {
    $activity = DB::table('student_activity')
        ->where('user_id', $userId)
        ->where('student_unit_id', $studentUnit->id)
        ->where('activity_type', 'rules_accepted')
        ->first();
    echo "   - Accepted at: {$activity->created_at}\n";
}

// Check identity verification
$verified = json_decode($studentUnit->verified ?? '{}', true);
$idCardPath = $verified['id_card_path'] ?? null;
$headshotPath = $verified['headshot_path'] ?? null;

echo "\n3. Identity Verified: ";
if ($idCardPath && $headshotPath) {
    echo "✅ YES\n";
} else {
    echo "❌ NO\n";
}
echo "   - ID Card: " . ($idCardPath ? "✅ {$idCardPath}" : "❌ Missing") . "\n";
echo "   - Headshot: " . ($headshotPath ? "✅ {$headshotPath}" : "❌ Missing") . "\n";

echo "\n=== SUMMARY ===\n";
if ($termsAccepted && $rulesAccepted && $idCardPath && $headshotPath) {
    echo "✅ ALL REQUIREMENTS MET - Onboarding can complete\n";
} else {
    echo "❌ MISSING REQUIREMENTS:\n";
    if (!$termsAccepted) echo "   - Need to accept terms\n";
    if (!$rulesAccepted) echo "   - Need to accept rules\n";
    if (!$idCardPath) echo "   - Need to upload ID card\n";
    if (!$headshotPath) echo "   - Need to take headshot\n";
}

echo "\n";
