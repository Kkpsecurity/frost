<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseUnitLessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseUnitLessons = [
            ['id' => 1, 'course_unit_id' => 1, 'lesson_id' => 1, 'progress_minutes' => 360, 'instr_seconds' => 21600, 'ordering' => 1],
            ['id' => 2, 'course_unit_id' => 1, 'lesson_id' => 2, 'progress_minutes' => 120, 'instr_seconds' => 7200, 'ordering' => 2],
            ['id' => 3, 'course_unit_id' => 2, 'lesson_id' => 3, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 4, 'course_unit_id' => 2, 'lesson_id' => 4, 'progress_minutes' => 180, 'instr_seconds' => 10800, 'ordering' => 2],
            ['id' => 5, 'course_unit_id' => 2, 'lesson_id' => 5, 'progress_minutes' => 240, 'instr_seconds' => 14400, 'ordering' => 3],
            ['id' => 6, 'course_unit_id' => 3, 'lesson_id' => 6, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 7, 'course_unit_id' => 3, 'lesson_id' => 7, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 2],
            ['id' => 8, 'course_unit_id' => 3, 'lesson_id' => 8, 'progress_minutes' => 120, 'instr_seconds' => 7200, 'ordering' => 3],
            ['id' => 9, 'course_unit_id' => 3, 'lesson_id' => 9, 'progress_minutes' => 90, 'instr_seconds' => 5400, 'ordering' => 4],
            ['id' => 10, 'course_unit_id' => 3, 'lesson_id' => 10, 'progress_minutes' => 150, 'instr_seconds' => 9000, 'ordering' => 5],
            ['id' => 11, 'course_unit_id' => 4, 'lesson_id' => 11, 'progress_minutes' => 270, 'instr_seconds' => 16200, 'ordering' => 1],
            ['id' => 12, 'course_unit_id' => 4, 'lesson_id' => 12, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 2],
            ['id' => 13, 'course_unit_id' => 4, 'lesson_id' => 13, 'progress_minutes' => 150, 'instr_seconds' => 9000, 'ordering' => 3],
            ['id' => 14, 'course_unit_id' => 5, 'lesson_id' => 14, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 15, 'course_unit_id' => 5, 'lesson_id' => 15, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 2],
            ['id' => 16, 'course_unit_id' => 5, 'lesson_id' => 16, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 3],
            ['id' => 17, 'course_unit_id' => 5, 'lesson_id' => 17, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 4],
            ['id' => 18, 'course_unit_id' => 5, 'lesson_id' => 18, 'progress_minutes' => 240, 'instr_seconds' => 14400, 'ordering' => 5],
            ['id' => 39, 'course_unit_id' => 16, 'lesson_id' => 19, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 40, 'course_unit_id' => 16, 'lesson_id' => 20, 'progress_minutes' => 180, 'instr_seconds' => 10800, 'ordering' => 2],
            ['id' => 41, 'course_unit_id' => 16, 'lesson_id' => 21, 'progress_minutes' => 180, 'instr_seconds' => 10800, 'ordering' => 3],
            ['id' => 42, 'course_unit_id' => 17, 'lesson_id' => 22, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 43, 'course_unit_id' => 17, 'lesson_id' => 23, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 2],
            ['id' => 44, 'course_unit_id' => 17, 'lesson_id' => 24, 'progress_minutes' => 120, 'instr_seconds' => 7200, 'ordering' => 3],
            ['id' => 45, 'course_unit_id' => 17, 'lesson_id' => 25, 'progress_minutes' => 120, 'instr_seconds' => 7200, 'ordering' => 4],
            ['id' => 46, 'course_unit_id' => 17, 'lesson_id' => 26, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 5],
            ['id' => 47, 'course_unit_id' => 18, 'lesson_id' => 27, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 1],
            ['id' => 48, 'course_unit_id' => 18, 'lesson_id' => 28, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 2],
            ['id' => 49, 'course_unit_id' => 18, 'lesson_id' => 29, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 3],
            ['id' => 50, 'course_unit_id' => 18, 'lesson_id' => 30, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 4],
            ['id' => 51, 'course_unit_id' => 18, 'lesson_id' => 31, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 5],
            ['id' => 52, 'course_unit_id' => 18, 'lesson_id' => 32, 'progress_minutes' => 60, 'instr_seconds' => 3600, 'ordering' => 6],
        ];

        DB::table('course_unit_lessons')->insert($courseUnitLessons);

        // Set the sequence to the correct value
        DB::statement("SELECT setval('course_unit_lessons_id_seq', 52, true)");
    }
}
