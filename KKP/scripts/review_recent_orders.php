<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== All Orders (most recent 20) ===\n\n";

$orders = DB::table('orders')
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

foreach ($orders as $o) {
    echo "Order ID:      {$o->id}\n";
    echo "User ID:       {$o->user_id}\n";
    echo "Course ID:     {$o->course_id}\n";
    echo "course_auth_id:{$o->course_auth_id}\n";
    echo "completed_at:  {$o->completed_at}\n";
    echo "created_at:    {$o->created_at}\n";
    echo str_repeat('-', 40) . "\n";
}
echo "\nTotal: " . count($orders) . " order(s)\n";

// Also show associated payments
if (count($orders) > 0) {
    $orderIds = array_column((array) $orders, 'id');
    echo "\n=== Payments for these orders ===\n\n";
    $payments = DB::table('payments')
        ->whereIn('order_id', $orderIds)
        ->orderBy('created_at', 'desc')
        ->get();
    foreach ($payments as $p) {
        echo "Payment ID:    {$p->id}\n";
        echo "Order ID:      {$p->order_id}\n";
        echo "Status:        {$p->status}\n";
        echo "Amount:        {$p->amount}\n";
        echo "created_at:    {$p->created_at}\n";
        echo str_repeat('-', 40) . "\n";
    }
}

// Also show associated course_auths
if (count($orders) > 0) {
    $caIds = array_filter(array_column((array) $orders, 'course_auth_id'));
    if (count($caIds) > 0) {
        echo "\n=== CourseAuths linked to these orders ===\n\n";
        $courseAuths = DB::table('course_auths')
            ->whereIn('id', $caIds)
            ->get();
        foreach ($courseAuths as $ca) {
            echo "CourseAuth ID: {$ca->id}\n";
            echo "User ID:       {$ca->user_id}\n";
            echo "Course ID:     {$ca->course_id}\n";
            echo "completed_at:  {$ca->completed_at}\n";
            echo "created_at:    {$ca->created_at}\n";
            echo str_repeat('-', 40) . "\n";
        }
    } else {
        echo "\n(No linked course_auths — no access was granted)\n";
    }
}
