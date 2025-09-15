<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ClassroomParticipant;
use App\Models\Classroom;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassroomParticipant>
 */
class ClassroomParticipantFactory extends Factory
{
    protected $model = ClassroomParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'user_id' => User::factory(),
            'role' => 'student',
            'status' => 'enrolled',
            'joined_at' => null,
            'last_activity' => null,
            'metadata' => [],
        ];
    }

    /**
     * Configure the model factory for instructors.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instructor',
            'metadata' => [
                'call_time_minutes_before' => 30,
                'assigned_by_system' => true,
            ],
        ]);
    }

    /**
     * Configure the model factory for students.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
            'metadata' => [
                'enrollment_source' => 'course_auth',
                'auto_enrolled' => true,
            ],
        ]);
    }

    /**
     * Configure the model factory for present participants.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
            'joined_at' => now()->subMinutes($this->faker->numberBetween(5, 60)),
            'last_activity' => now()->subMinutes($this->faker->numberBetween(1, 10)),
        ]);
    }

    /**
     * Configure the model factory for absent participants.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'joined_at' => null,
            'last_activity' => null,
        ]);
    }
}
