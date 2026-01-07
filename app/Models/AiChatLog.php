<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatLog extends Model
{
    public $timestamps = false;

    protected $table = 'ai_chat_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'instructor_question_id' => 'integer',
        'requested_by' => 'integer',
        'created_at' => 'timestamp',
        'sources' => 'array',
        'response' => 'array',
    ];
}
