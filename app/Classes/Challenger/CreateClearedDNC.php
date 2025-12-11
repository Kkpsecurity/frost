<?php

declare(strict_types=1);

namespace App\Classes\Challenger;

use Illuminate\Support\Carbon;

use App\Models\Challenge;

use function App\Helpers\kkpdebug;

trait CreateClearedDNC
{

    public static function CreateClearedDNC(int $student_lesson_id): void
    {

        self::init();

        kkpdebug('Challenger_Msg', "CreateClearedDNC() : creating Challenge");

        $Challenge = Challenge::create([
            'student_lesson_id' => $student_lesson_id,
            'is_final'          => true,
            'expires_at'        => Carbon::now()->addSeconds(self::$_config->challenge_expires_at),
        ]);

        kkpdebug('Challenger_Msg', "CreateClearedDNC() : ChallengeID {$Challenge->id}");
    }
}
