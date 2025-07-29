<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'id' => 1,
                'is_active' => true,
                'exam_id' => 1,
                'eq_spec_id' => 1,
                'title' => 'Florida D40 (Dy)',
                'title_long' => 'Florida Class \'D\' 40 Hour (5 Days)',
                'price' => 99.00,
                'total_minutes' => 144000,
                'policy_expire_days' => 365,
                'dates_template' => json_encode([
                    'week_1' => [
                        ['course_unit_id' => 1, 'wday' => 1, 'start' => '08:00', 'end' => '17:00'],
                        ['course_unit_id' => 2, 'wday' => 2, 'start' => '08:00', 'end' => '17:00'],
                        ['course_unit_id' => 3, 'wday' => 3, 'start' => '08:00', 'end' => '17:00'],
                        ['course_unit_id' => 4, 'wday' => 4, 'start' => '08:00', 'end' => '17:00'],
                        ['course_unit_id' => 5, 'wday' => 5, 'start' => '08:00', 'end' => '17:00']
                    ]
                ]),
                'zoom_creds_id' => 2,
                'needs_range' => false
            ],
            [
                'id' => 2,
                'is_active' => false,
                'exam_id' => 1,
                'eq_spec_id' => 1,
                'title' => 'Florida D40 (Nt)',
                'title_long' => 'Florida Class \'D\' 40 Hour (10 Nights)',
                'price' => 99.00,
                'total_minutes' => 144000,
                'policy_expire_days' => 365,
                'dates_template' => json_encode([
                    'week_1' => [
                        ['course_unit_id' => 6, 'wday' => 1, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 7, 'wday' => 2, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 8, 'wday' => 3, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 9, 'wday' => 4, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 10, 'wday' => 5, 'start' => '18:00', 'end' => '22:00']
                    ],
                    'week_2' => [
                        ['course_unit_id' => 11, 'wday' => 1, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 12, 'wday' => 2, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 13, 'wday' => 3, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 14, 'wday' => 4, 'start' => '18:00', 'end' => '22:00'],
                        ['course_unit_id' => 15, 'wday' => 5, 'start' => '18:00', 'end' => '22:00']
                    ]
                ]),
                'zoom_creds_id' => 2,
                'needs_range' => false
            ],
            [
                'id' => 3,
                'is_active' => true,
                'exam_id' => 2,
                'eq_spec_id' => 1,
                'title' => 'Florida G28',
                'title_long' => 'Florida Class \'G\' 28 Hour',
                'price' => 100.00,
                'total_minutes' => 72000,
                'policy_expire_days' => 365,
                'dates_template' => json_encode([
                    'week_1' => [
                        ['course_unit_id' => 16, 'wday' => 1, 'start' => '09:00', 'end' => '17:00'],
                        ['course_unit_id' => 17, 'wday' => 2, 'start' => '09:00', 'end' => '17:00'],
                        ['course_unit_id' => 18, 'wday' => 3, 'start' => '09:00', 'end' => '16:00']
                    ]
                ]),
                'zoom_creds_id' => 3,
                'needs_range' => true
            ]
        ];

        DB::table('courses')->insert($courses);

        // Set the sequence to the correct value
        DB::statement("SELECT setval('courses_id_seq', 3, true)");
    }
}
