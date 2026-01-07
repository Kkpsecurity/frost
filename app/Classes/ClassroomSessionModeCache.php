<?php

declare(strict_types=1);

namespace App\Classes;

use Illuminate\Support\Facades\Redis;

class ClassroomSessionModeCache
{
    public const EXPIRE_SECONDS = 86400; // 24h

    public const MODE_TEACHING = 'TEACHING';
    public const MODE_QA = 'Q&A';
    public const MODE_BREAK = 'BREAK';

    public static function Redis(): object
    {
        return Redis::connection('cache');
    }

    public static function RedisKey(int $courseDateId): string
    {
        return "classroom_session_mode:{$courseDateId}";
    }

    public static function Get(int $courseDateId): string
    {
        $value = (string) (self::Redis()->get(self::RedisKey($courseDateId)) ?? '');
        $value = strtoupper(trim($value));

        if ($value === strtoupper(self::MODE_QA) || $value === 'QA') {
            return self::MODE_QA;
        }

        if ($value === self::MODE_BREAK) {
            return self::MODE_BREAK;
        }

        return self::MODE_TEACHING;
    }

    public static function Set(int $courseDateId, string $mode): void
    {
        $modeNorm = strtoupper(trim($mode));
        if ($modeNorm === 'QA' || $modeNorm === strtoupper(self::MODE_QA)) {
            $mode = self::MODE_QA;
        } elseif ($modeNorm === self::MODE_BREAK) {
            $mode = self::MODE_BREAK;
        } else {
            $mode = self::MODE_TEACHING;
        }

        self::Redis()->set(self::RedisKey($courseDateId), $mode, 'EX', self::EXPIRE_SECONDS);
    }

    public static function Cycle(int $courseDateId): string
    {
        $current = self::Get($courseDateId);
        $next = match ($current) {
            self::MODE_TEACHING => self::MODE_QA,
            self::MODE_QA => self::MODE_BREAK,
            default => self::MODE_TEACHING,
        };
        self::Set($courseDateId, $next);
        return $next;
    }
}
