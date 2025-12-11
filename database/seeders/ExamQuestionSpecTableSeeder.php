<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamQuestionSpecTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examQuestionSpecs = [
            ['id' => 1, 'name' => 'Florida'],
            ['id' => 2, 'name' => 'Georgia'],
        ];

        DB::table('exam_question_spec')->insert($examQuestionSpecs);

        // Set the sequence to the correct value
        DB::statement("SELECT setval('exam_question_spec_id_seq', 2, true)");
    }
}
