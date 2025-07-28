<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessons = [
            ['id' => 1, 'title' => 'Legal Aspects of Private Security', 'credit_minutes' => 360, 'video_seconds' => 21600],
            ['id' => 2, 'title' => 'Role of Private Security Officers', 'credit_minutes' => 120, 'video_seconds' => 7200],
            ['id' => 3, 'title' => 'Communication Systems', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 4, 'title' => 'Security Officer Conduct', 'credit_minutes' => 180, 'video_seconds' => 10800],
            ['id' => 5, 'title' => 'Observation and Incident Reporting', 'credit_minutes' => 240, 'video_seconds' => 14400],
            ['id' => 6, 'title' => 'Patrolling', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 7, 'title' => 'Interviewing Techniques', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 8, 'title' => 'Principles of Communications', 'credit_minutes' => 120, 'video_seconds' => 7200],
            ['id' => 9, 'title' => 'Emergency Preparedness', 'credit_minutes' => 90, 'video_seconds' => 5400],
            ['id' => 10, 'title' => 'Safety Awareness', 'credit_minutes' => 150, 'video_seconds' => 9000],
            ['id' => 11, 'title' => 'Medical Emergencies', 'credit_minutes' => 270, 'video_seconds' => 16200],
            ['id' => 12, 'title' => 'Principles of Safeguarding Information', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 13, 'title' => 'Terrorism', 'credit_minutes' => 150, 'video_seconds' => 9000],
            ['id' => 14, 'title' => 'Principles of Access Control', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 15, 'title' => 'Physical Security', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 16, 'title' => 'Event Security and Special Assignments', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 17, 'title' => 'Introduction to Weapons', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 18, 'title' => 'Special Issues', 'credit_minutes' => 240, 'video_seconds' => 14400],
            ['id' => 19, 'title' => 'Security Officer and Private Investigator Licensure', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 20, 'title' => 'Definitions and Legal Concepts', 'credit_minutes' => 180, 'video_seconds' => 10800],
            ['id' => 21, 'title' => 'Use of Force', 'credit_minutes' => 180, 'video_seconds' => 10800],
            ['id' => 22, 'title' => 'Firearms Safety', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 23, 'title' => 'Firearms Familiarization', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 24, 'title' => 'Fundamentals of Marksmanship', 'credit_minutes' => 120, 'video_seconds' => 7200],
            ['id' => 25, 'title' => 'Firearms Mechanics', 'credit_minutes' => 120, 'video_seconds' => 7200],
            ['id' => 26, 'title' => 'Malfunctions', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 27, 'title' => 'Ammunition Use', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 28, 'title' => 'Cover and Concealment', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 29, 'title' => 'Survival Shooting', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 30, 'title' => 'Weapon Cleaning', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 31, 'title' => 'Qualification Course Overview', 'credit_minutes' => 60, 'video_seconds' => 3600],
            ['id' => 32, 'title' => 'Review and Discussion', 'credit_minutes' => 60, 'video_seconds' => 3600],
        ];

        foreach ($lessons as $lesson) {
            DB::table('lessons')->insert($lesson);
        }

        // Set the sequence to continue from 32
        DB::statement("SELECT setval('lessons_id_seq', 32, true);");
    }
}
