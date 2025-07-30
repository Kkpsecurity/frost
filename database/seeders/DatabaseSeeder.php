<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed reference tables first (no dependencies)
        $this->call([
            CountriesTableSeeder::class,
            ExamQuestionSpecTableSeeder::class,
            LessonsTableSeeder::class,          // Lessons are referenced by exam_questions
        ]);

        // Seed course-related tables (depends on no foreign keys for basic courses)
        $this->call([
            CoursesTableSeeder::class,
            CourseUnitsTableSeeder::class,      // depends on courses
            CourseUnitLessonsTableSeeder::class, // depends on course_units
        ]);

        // Seed exam questions (depends on course_unit_lessons, lessons, and exam_question_spec)
        $this->call([
            ExamQuestionsTableSeeder::class,
        ]);

        // Seed exam configurations (independent)
        $this->call([
            ExamsTableSeeder::class,
        ]);

        // Seed range/location data (independent)
        $this->call([
            RangesTableSeeder::class,
            RangeDatesTableSeeder::class,      // depends on ranges
        ]);

        // Seed payment processing data (independent)
        $this->call([
            PaymentTypesTableSeeder::class,
        ]);

        // Seed admin users (depends on roles table being populated)
        $this->call([
            AdminUsersTableSeeder::class,
        ]);

        // Seed Zoom credentials for virtual training (independent)
        $this->call([
            ZoomCredsTableSeeder::class,
        ]);

        // Seed AdminLTE configuration settings
        $this->call([
            AdminLteConfigSeeder::class,
        ]);
    }
}
