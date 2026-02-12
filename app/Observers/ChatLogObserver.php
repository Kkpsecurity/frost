<?php

namespace App\Observers;

use App\Models\ChatLog;
use App\Classes\Admin\ChatLogCache;


class ChatLogObserver
{

    public function saved(ChatLog $ChatLog)
    {
        ChatLogCache::observer_saved($ChatLog);
    }

    public function deleted(ChatLog $ChatLog)
    {
        ChatLogCache::observer_deleted($ChatLog);
    }
}
