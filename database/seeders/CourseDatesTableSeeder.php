<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseDatesTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // id, is_active, course_unit_id, starts_at, ends_at
            [
                'id' => 10000, 'is_active' => true, 'course_unit_id' => 1,
                'starts_at' => '2023-07-24 08:00:00', 'ends_at' => '2023-07-24 17:00:00',
            ],
            [
                'id' => 10001, 'is_active' => true, 'course_unit_id' => 2,
                'starts_at' => '2023-07-25 08:00:00', 'ends_at' => '2023-07-25 17:00:00',
            ],
            [
                'id' => 10002, 'is_active' => true, 'course_unit_id' => 3,
                'starts_at' => '2023-07-26 08:00:00', 'ends_at' => '2023-07-26 17:00:00',
            ],
            [
                'id' => 10003, 'is_active' => true, 'course_unit_id' => 4,
                'starts_at' => '2023-07-27 08:00:00', 'ends_at' => '2023-07-27 17:00:00',
            ],
            [
                'id' => 10004, 'is_active' => true, 'course_unit_id' => 5,
                'starts_at' => '2023-07-28 08:00:00', 'ends_at' => '2023-07-28 17:00:00',
            ],
            [
                'id' => 10492, 'is_active' => true, 'course_unit_id' => 1,
                'starts_at' => '2024-08-24 17:00:00', 'ends_at' => '2024-08-24 18:59:59',
            ],
            [
                'id' => 10007, 'is_active' => true, 'course_unit_id' => 3,
                'starts_at' => '2023-08-02 08:00:00', 'ends_at' => '2023-08-02 17:00:00',
            ],
            [
                'id' => 10008, 'is_active' => true, 'course_unit_id' => 4,
                'starts_at' => '2023-08-03 08:00:00', 'ends_at' => '2023-08-03 17:00:00',
            ],
            [
                'id' => 10009, 'is_active' => true, 'course_unit_id' => 5,
                'starts_at' => '2023-08-04 08:00:00', 'ends_at' => '2023-08-04 17:00:00',
            ],
            [
                'id' => 10010, 'is_active' => true, 'course_unit_id' => 1,
                'starts_at' => '2023-08-07 08:00:00', 'ends_at' => '2023-08-07 17:00:00',
            ],
            // ... (add more rows as needed from the dump)
        ];

        foreach ($data as $row) {
            DB::table('course_dates')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'is_active' => $row['is_active'],
                    'course_unit_id' => $row['course_unit_id'],
                    'starts_at' => Carbon::parse($row['starts_at']),
                    'ends_at' => Carbon::parse($row['ends_at']),
                ]
            );
        }
    }
}
