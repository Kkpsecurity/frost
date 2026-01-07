<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorQuestion extends Model
{
    protected $table = 'instructor_questions';

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'course_date_id' => 'integer',
        'student_id' => 'integer',
        'answered_by' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'held_at' => 'timestamp',
        'answered_at' => 'timestamp',
        'ai_generated_at' => 'timestamp',
        'ai_sources' => 'array',
    ];
}
