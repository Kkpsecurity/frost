<?php

namespace App\Traits;


trait CommandLoggerTrait
{

    private function _Log(string $msg): void
    {

        if ($this->getOutput()->isVerbose()) {

            print "{$msg}\n";
        } else if (! $this->getOutput()->isQuiet()) {

            logger(substr(strrchr(get_class($this), '\\'), 1) . ': ' . $msg);
        }
    }
}
