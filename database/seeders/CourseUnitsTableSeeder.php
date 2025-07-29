<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseUnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseUnits = [
            // Course 1 (Day Course) - 5 Days
            ['id' => 1, 'course_id' => 1, 'title' => 'Day 1', 'admin_title' => 'FL-D40-D1', 'ordering' => 1],
            ['id' => 2, 'course_id' => 1, 'title' => 'Day 2', 'admin_title' => 'FL-D40-D2', 'ordering' => 2],
            ['id' => 3, 'course_id' => 1, 'title' => 'Day 3', 'admin_title' => 'FL-D40-D3', 'ordering' => 3],
            ['id' => 4, 'course_id' => 1, 'title' => 'Day 4', 'admin_title' => 'FL-D40-D4', 'ordering' => 4],
            ['id' => 5, 'course_id' => 1, 'title' => 'Day 5', 'admin_title' => 'FL-D40-D5', 'ordering' => 5],

            // Course 2 (Night Course) - 10 Nights
            ['id' => 6, 'course_id' => 2, 'title' => 'Night 1', 'admin_title' => 'FL-D40-N1', 'ordering' => 1],
            ['id' => 7, 'course_id' => 2, 'title' => 'Night 2', 'admin_title' => 'FL-D40-N2', 'ordering' => 2],
            ['id' => 8, 'course_id' => 2, 'title' => 'Night 3', 'admin_title' => 'FL-D40-N3', 'ordering' => 3],
            ['id' => 9, 'course_id' => 2, 'title' => 'Night 4', 'admin_title' => 'FL-D40-N4', 'ordering' => 4],
            ['id' => 10, 'course_id' => 2, 'title' => 'Night 5', 'admin_title' => 'FL-D40-N5', 'ordering' => 5],
            ['id' => 11, 'course_id' => 2, 'title' => 'Night 6', 'admin_title' => 'FL-D40-N6', 'ordering' => 6],
            ['id' => 12, 'course_id' => 2, 'title' => 'Night 7', 'admin_title' => 'FL-D40-N7', 'ordering' => 7],
            ['id' => 13, 'course_id' => 2, 'title' => 'Night 8', 'admin_title' => 'FL-D40-N8', 'ordering' => 8],
            ['id' => 14, 'course_id' => 2, 'title' => 'Night 9', 'admin_title' => 'FL-D40-N9', 'ordering' => 9],
            ['id' => 15, 'course_id' => 2, 'title' => 'Night 10', 'admin_title' => 'FL-D40-N10', 'ordering' => 10],

            // Course 3 (G License Course) - 3 Days
            ['id' => 16, 'course_id' => 3, 'title' => 'Day 1', 'admin_title' => 'FL-G28-D1', 'ordering' => 1],
            ['id' => 17, 'course_id' => 3, 'title' => 'Day 2', 'admin_title' => 'FL-G28-D2', 'ordering' => 2],
            ['id' => 18, 'course_id' => 3, 'title' => 'Day 3', 'admin_title' => 'FL-G28-D3', 'ordering' => 3],
        ];

        DB::table('course_units')->insert($courseUnits);

        // Set the sequence to the correct value
        DB::statement("SELECT setval('course_units_id_seq', 18, true)");
    }
}
