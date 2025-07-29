<?php

namespace App\Models;

/**
 * @file ExamQuestionSpec.php
 * @brief Model for exam_question_spec table.
 * @details This model represents specifications for exam questions, including attributes like name.
 * It provides relationships to courses and exam questions.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\Course;
use App\Models\ExamQuestion;

use App\Traits\StaticModel;


class ExamQuestionSpec extends Model
{

    use StaticModel;


    protected $table        = 'exam_question_spec';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'name'              => 'string',  // 16

    ];

    protected $fillable     = [];  // static model

    public function __toString()
    {
        return $this->name;
    }


    //
    // relationships
    //


    public function Courses()
    {
        return $this->hasMany(Course::class, 'eq_spec_id');
    }

    public function ExamQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'eq_spec_id');
    }
}
