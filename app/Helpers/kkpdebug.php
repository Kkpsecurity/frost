<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

if (!function_exists('kkpdebug')) {
    function kkpdebug($facility, $msg)
    {
        switch (true) {
            case App::environment('production'):
                break;

            case is_null(config('kkpdebug')):
                throw new Exception('Need to create config/kkpdebug.php');

            case is_null(config("kkpdebug.{$facility}")):
                Log::error("kkpdebug: undefined facility '{$facility}'");
                Log::info("({$facility}) {$msg}");
                break;

            case config("kkpdebug.{$facility}"):
                Log::info("({$facility}) {$msg}");
                break;
        }
    }
}
