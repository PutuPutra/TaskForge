<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
    $endDate = fake()->dateTimeBetween($startDate, '+2 months');

    return [
      'name' => fake()->sentence(3),
      'start_date' => $startDate->format('Y-m-d'),
      'end_date' => $endDate->format('Y-m-d'),
      'status' => fake()->numberBetween(1, 3),
    ];
  }
}
