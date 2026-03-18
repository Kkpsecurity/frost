<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// User 17 = Student Two, User 2 = Richard Clark (instructor doing payment test)
$userId = (int) ($_SERVER['argv'][1] ?? 17);

echo "=== CourseAuths for user {$userId} ===\n\n";

$courseAuths = DB::table('course_auths as ca')
    ->leftJoin('courses as c', 'c.id', '=', 'ca.course_id')
    ->where('ca.user_id', $userId)
    ->orderBy('ca.created_at', 'desc')
    ->get([
        'ca.id',
        'ca.user_id',
        'ca.course_id',
        'c.title as course_title',
        'ca.start_date',
        'ca.completed_at',
        'ca.disabled_at',
        'ca.created_at',
        'ca.updated_at',
    ]);

foreach ($courseAuths as $ca) {
    echo "CourseAuth ID: {$ca->id}\n";
    echo "Course:        [{$ca->course_id}] {$ca->course_title}\n";
    echo "start_date:    {$ca->start_date}\n";
    echo "completed_at:  {$ca->completed_at}\n";
    echo "disabled_at:   {$ca->disabled_at}\n";
    echo "created_at:    {$ca->created_at}\n";
    // Link to order
    $order = DB::table('orders')->where('course_auth_id', $ca->id)->first();
    if ($order) {
        echo "Order:         #{$order->id} | completed={$order->completed_at} | created={$order->created_at}\n";
    } else {
        echo "Order:         (none linked)\n";
    }
    echo str_repeat('-', 45) . "\n";
}

echo "\nTotal: " . count($courseAuths) . " course_auth(s)\n";

// Also check orders for this user with no course_auth_id (orphaned)
echo "\n=== Orders for user {$userId} (all) ===\n\n";
$orders = DB::table('orders')
    ->where('user_id', $userId)
    ->orderBy('created_at', 'desc')
    ->get();
foreach ($orders as $o) {
    $payments = DB::table('payments')->where('order_id', $o->id)->get();
    $payStr = [];
    foreach ($payments as $p) {
        $payStr[] = "pay#{$p->id}[{$p->status}]";
    }
    echo "Order #{$o->id} | course={$o->course_id} | ca={$o->course_auth_id} | done={$o->completed_at} | payments=[" . implode(', ', $payStr) . "] | created={$o->created_at}\n";
}
echo "\nTotal: " . count($orders) . " order(s)\n";
