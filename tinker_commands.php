<?php

// Test the service directly using tinker commands
echo "Testing StudentDashboardService structure...\n\n";

// These commands would be run in tinker:
echo "Commands to run in php artisan tinker:\n";
echo "======================================\n\n";

echo '$user = App\Models\User::first();' . "\n";
echo '$service = new App\Services\StudentDashboardService($user);' . "\n";
echo '$data = $service->getDashboardData();' . "\n";
echo 'print_r(array_keys($data));' . "\n";
echo 'echo "Stats: " . json_encode($data["stats"]);' . "\n";
echo 'echo "IncompleteAuths count: " . $data["incompleteAuths"]->count();' . "\n";
echo 'echo "CompletedAuths count: " . $data["completedAuths"]->count();' . "\n";
echo 'echo "MergedAuths count: " . $data["mergedAuths"]->count();' . "\n";

?>
