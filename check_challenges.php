<?php

/**
 * Challenge Monitor — quick DB dump for QA testing
 * Usage: php check_challenges.php [last_known_id]
 *
 * Shows last 10 challenges with duration (should be ~215s after Job S).
 * Pass an ID to only show rows newer than that ID.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sinceId = (int) ($argv[1] ?? 0);

$q = \DB::table('challenges')->orderBy('id', 'desc')->limit(10);
if ($sinceId > 0) {
    $q->where('id', '>', $sinceId);
}
$rows = $q->get(['id', 'student_lesson_id', 'is_final', 'is_eol', 'created_at', 'expires_at', 'completed_at', 'failed_at']);

if ($rows->isEmpty()) {
    echo "No challenges found (since ID {$sinceId}).\n";
    exit;
}

$now = new DateTime('now', new DateTimeZone('UTC'));

echo str_repeat('-', 90) . "\n";
printf(
    "%-6s %-8s %-7s %-5s %-5s %-8s %-25s %-10s %-10s\n",
    'ID',
    'SLID',
    'DUR(s)',
    'FINAL',
    'EOL',
    'STATUS',
    'EXPIRES_AT',
    'COMPLETE',
    'FAILED'
);
echo str_repeat('-', 90) . "\n";

foreach ($rows as $r) {
    $created = $r->created_at ? new DateTime($r->created_at, new DateTimeZone('UTC')) : null;
    $expires = $r->expires_at ? new DateTime($r->expires_at, new DateTimeZone('UTC')) : null;
    $dur     = ($created && $expires) ? ($expires->getTimestamp() - $created->getTimestamp()) : '?';

    // Time left (negative = expired)
    if ($expires) {
        $secLeft = $expires->getTimestamp() - $now->getTimestamp();
        $leftStr = $secLeft > 0 ? "+{$secLeft}s left" : abs($secLeft) . "s ago";
    } else {
        $leftStr = '?';
    }

    $status = $r->completed_at ? 'DONE' : ($r->failed_at ? 'FAILED' : 'PENDING');

    printf(
        "%-6s %-8s %-7s %-5s %-5s %-8s %-25s %-10s %-10s\n",
        $r->id,
        $r->student_lesson_id,
        $dur,
        $r->is_final ? 'YES' : 'no',
        $r->is_eol   ? 'YES' : 'no',
        $status,
        $leftStr,
        $r->completed_at ? substr($r->completed_at, 11, 8) : '-',
        $r->failed_at    ? substr($r->failed_at, 11, 8)    : '-'
    );
}

echo str_repeat('-', 90) . "\n";
echo "Checked at: " . $now->format('H:i:s') . " UTC | Expected duration: ~215s (3m 35s)\n";
echo "Config: challenge_time=" . config('challenger.challenge_time')
    . "s  expires_at=" . config('challenger.challenge_expires_at')
    . "s  warning_before=" . config('challenger.warning_before_seconds') . "s\n";
