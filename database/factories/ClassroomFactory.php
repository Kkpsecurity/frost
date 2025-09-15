<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Classroom;
use App\Models\CourseDate;
use App\Models\CourseUnit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_date_id' => CourseDate::factory(),
            'course_unit_id' => CourseUnit::factory(),
            'title' => $this->faker->sentence(3),
            'starts_at' => now()->addHours(1),
            'ends_at' => now()->addHours(9),
            'modality' => $this->faker->randomElement(['online', 'in_person']),
            'location' => $this->faker->optional()->address,
            'status' => 'ready',
            'meeting_url' => $this->faker->optional()->url,
            'meeting_id' => $this->faker->optional()->uuid,
            'meeting_config' => [],
            'join_instructions' => $this->faker->optional()->sentence,
            'capacity' => $this->faker->numberBetween(10, 50),
            'waitlist_policy' => $this->faker->randomElement(['none', 'automatic', 'manual']),
            'late_join_cutoff' => now()->addMinutes(30),
            'classroom_created_at' => now(),
            'created_by' => null,
            'creation_metadata' => [
                'created_by_system' => true,
                'auto_create_version' => '1.0',
            ],
        ];
    }

    /**
     * Configure the model factory for online classrooms.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'modality' => 'online',
            'location' => 'Online',
            'meeting_url' => 'https://zoom.us/j/' . $this->faker->numerify('##########'),
            'meeting_id' => $this->faker->numerify('###-###-####'),
            'join_instructions' => 'Please join the meeting 5 minutes before the scheduled start time.',
        ]);
    }

    /**
     * Configure the model factory for in-person classrooms.
     */
    public function inPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'modality' => 'in_person',
            'location' => $this->faker->randomElement([
                'Main Building - Room 101',
                'Science Building - Lab 205',
                'Conference Center - Hall A',
                'Training Facility - Room B',
            ]),
            'meeting_url' => null,
            'meeting_id' => null,
            'join_instructions' => 'Please arrive 10 minutes early to check in.',
        ]);
    }

    /**
     * Configure the model factory for preparing status.
     */
    public function preparing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'preparing',
            'classroom_created_at' => null,
        ]);
    }

    /**
     * Configure the model factory for live status.
     */
    public function live(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'live',
        ]);
    }

    /**
     * Configure the model factory for completed status.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
