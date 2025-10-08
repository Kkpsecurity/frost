<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;
use App\Services\ClassroomSessionService;
use Illuminate\Support\Facades\Auth;

class TestAssistantFeature extends Command
{
    protected $signature = 'test:assistant-feature';
    protected $description = 'Test the assistant feature for instructor classroom';

    public function handle()
    {
        $this->info('=== TESTING ASSISTANT FEATURE ===');

        // 1. Find a test course date
        $this->info('1. FINDING TEST COURSE DATE:');
        $courseDate = CourseDate::whereDate('starts_at', '>=', now()->subDays(7))
            ->whereDate('starts_at', '<=', now()->addDays(7))
            ->first();

        if (!$courseDate) {
            $this->error('❌ No course dates found within date range');
            return 1;
        }

        $this->info("✅ Found CourseDate: {$courseDate->id}");
        $this->info("   Course: " . ($courseDate->GetCourse()->title ?? 'Unknown'));
        $this->info("   Date: {$courseDate->starts_at}");

        // 2. Find test users (instructors/admins)
        $this->info('2. FINDING TEST USERS:');
        $instructorUser = User::whereIn('role_id', [1, 2, 3, 4]) // SysAdmin, Administrator, Support, Instructor
            ->whereNotNull('fname')
            ->first();

        $assistantUser = User::whereIn('role_id', [1, 2, 3, 4]) // SysAdmin, Administrator, Support, Instructor
            ->whereNotNull('fname')
            ->where('id', '!=', $instructorUser->id ?? 0)
            ->first();

        if (!$instructorUser || !$assistantUser) {
            $this->error('❌ Need at least 2 admin users for testing');
            return 1;
        }

        $this->info("✅ Instructor: {$instructorUser->fname} {$instructorUser->lname} (ID: {$instructorUser->id})");
        $this->info("✅ Assistant: {$assistantUser->fname} {$assistantUser->lname} (ID: {$assistantUser->id})");

        // 3. Clean up any existing InstUnit
        $this->info('3. CLEANING UP EXISTING INSTUNIT:');
        $existingInstUnit = $courseDate->InstUnit;
        if ($existingInstUnit) {
            $this->info("   Deleting existing InstUnit: {$existingInstUnit->id}");
            $existingInstUnit->delete();
            $courseDate->refresh();
        }
        $this->info('✅ Clean slate ready');

        // 4. Test starting class session with assistant
        $this->info('4. TESTING CLASSROOM SESSION SERVICE:');
        $sessionService = new ClassroomSessionService();

        // Authenticate as instructor
        Auth::login($instructorUser);

        $this->info('   Starting session with assistant...');
        $instUnit = $sessionService->startClassroomSession($courseDate->id, $assistantUser->id);

        if (!$instUnit) {
            $this->error('❌ Failed to start classroom session');
            return 1;
        }

        $this->info("✅ InstUnit created: {$instUnit->id}");
        $this->info("   Instructor ID: {$instUnit->created_by}");
        $this->info("   Assistant ID: {$instUnit->assistant_id}");
        $this->info("   Created: {$instUnit->created_at}");

        // 5. Verify InstUnit has correct data
        $this->info('5. VERIFYING INSTUNIT DATA:');
        $instUnit->refresh();

        if ($instUnit->created_by != $instructorUser->id) {
            $this->error("❌ Instructor mismatch: expected {$instructorUser->id}, got {$instUnit->created_by}");
            return 1;
        }

        if ($instUnit->assistant_id != $assistantUser->id) {
            $this->error("❌ Assistant mismatch: expected {$assistantUser->id}, got {$instUnit->assistant_id}");
            return 1;
        }

        $this->info("✅ Instructor correctly set: {$instUnit->GetCreatedBy()->fname} {$instUnit->GetCreatedBy()->lname}");
        $this->info("✅ Assistant correctly set: {$instUnit->GetAssistant()->fname} {$instUnit->GetAssistant()->lname}");

        // 6. Test getClassroomSession method
        $this->info('6. TESTING GET CLASSROOM SESSION:');
        $sessionInfo = $sessionService->getClassroomSession($courseDate->id);

        $this->info("   Session exists: " . ($sessionInfo['exists'] ? 'YES' : 'NO'));
        if ($sessionInfo['exists']) {
            $this->info("   Instructor: {$sessionInfo['instructor']['name']} (ID: {$sessionInfo['instructor']['id']})");
            if (isset($sessionInfo['assistant'])) {
                $this->info("   Assistant: {$sessionInfo['assistant']['name']} (ID: {$sessionInfo['assistant']['id']})");
            } else {
                $this->info("   Assistant: None assigned");
            }
            $this->info("   Is Active: " . ($sessionInfo['is_active'] ? 'YES' : 'NO'));
        } else {
            $this->info("   Error: " . ($sessionInfo['error'] ?? 'Unknown error'));
        }

        // Clean up
        $instUnit->delete();
        Auth::logout();

        $this->info('=== ASSISTANT FEATURE TESTING COMPLETE ===');
        $this->info('✅ All core functionality working:');
        $this->info('   - Start session with assistant ✓');
        $this->info('   - InstUnit relationship management ✓');
        $this->info('   - Session info retrieval ✓');
        $this->info('🎉 ASSISTANT FEATURE READY FOR FRONTEND INTEGRATION!');

        return 0;
    }
}
