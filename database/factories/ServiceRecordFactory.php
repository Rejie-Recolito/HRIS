<?php

namespace Database\Factories;

use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRecord>
 */
class ServiceRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(18, 65),
            'salary' => $this->faker->numberBetween(30000, 150000),
            'date_of_birth' => $this->faker->date('1980-01-01', '2000-12-31'),
            'job_title' => $this->faker->jobTitle(),
            'place_of_birth' => $this->faker->city(),
            'office' => $this->faker->randomElement(['IT Department', 'HR Department', 'Finance Department', 'Operations', 'Marketing']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
            'date_of_service' => $this->faker->date('2000-01-01'),
            'place_of_assignment' => $this->faker->randomElement(['Main Office', 'Branch A', 'Branch B', 'Remote']),
            'request_status' => $this->faker->randomElement(['pending', 'ready']),
        ];
    }

    /**
     * Indicate that the service record request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'request_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the service record is ready.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'request_status' => 'ready',
        ]);
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the employee is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}