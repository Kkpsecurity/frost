<?php

namespace App\Observers;

use App\Classes\ChatLogCache;
use App\Models\ChatLog;


class ChatLogObserver
{

    public function saved( ChatLog $ChatLog )
    {
        ChatLogCache::observer_saved( $ChatLog );
    }

    public function deleted( ChatLog $ChatLog )
    {
        ChatLogCache::observer_deleted( $ChatLog );
    }

}
