<?php

namespace App\Traits;


trait NoString
{

    public function __toString(): string
    {
        return 'There is no string representation for ' . get_class($this);
    }
}
