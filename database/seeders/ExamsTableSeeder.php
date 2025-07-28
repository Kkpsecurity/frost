<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = [
            [
                'id' => 1,
                'admin_title' => 'Florida D40',
                'num_questions' => 170,
                'num_to_pass' => 128,
                'policy_expire_seconds' => 14400,      // 4 hours
                'policy_wait_seconds' => 86400,        // 24 hours
                'policy_attempts' => 2,
            ],
            [
                'id' => 2,
                'admin_title' => 'Florida G28',
                'num_questions' => 50,
                'num_to_pass' => 35,
                'policy_expire_seconds' => 7200,       // 2 hours
                'policy_wait_seconds' => 86400,        // 24 hours
                'policy_attempts' => 2,
            ],
        ];

        foreach ($exams as $exam) {
            DB::table('exams')->insert($exam);
        }

        // Set the sequence to continue from 2
        DB::statement("SELECT setval('exams_id_seq', 2, true);");
    }
}
