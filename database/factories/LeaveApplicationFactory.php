<?php

namespace Database\Factories;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveApplication>
 */
class LeaveApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lastname' => $this->faker->lastName(),
            'firstname' => $this->faker->firstName(),
            'middlename' => $this->faker->lastName(),
            'date_of_filing' => $this->faker->date(),
            'position' => $this->faker->jobTitle(),
            'salary' => $this->faker->numberBetween(30000, 100000),
            'type_of_leave' => $this->faker->randomElement(['vacation', 'sick', 'emergency', 'maternity', 'paternity']),
            'others' => null,
            'number_of_days' => $this->faker->numberBetween(1, 30),
            'inclusive_dates' => $this->faker->date() . ' to ' . $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    /**
     * Indicate that the leave application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the leave application is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the leave application is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}