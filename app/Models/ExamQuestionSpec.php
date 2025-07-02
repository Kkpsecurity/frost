<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Course;
use App\Models\ExamQuestion;
use KKP\Laravel\ModelTraits\StaticModel;


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

    protected $fillable     = [ ];  // static model

    public function __toString() { return $this->name; }


    //
    // relationships
    //


    public function Courses()
    {
        return $this->hasMany( Course::class, 'eq_spec_id' );
    }

    public function ExamQuestions()
    {
        return $this->hasMany( ExamQuestion::class, 'eq_spec_id' );
    }


}
