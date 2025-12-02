<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 * 
 * Database enum values:
 * - type: bootcamp, course
 * - level: beginner, intermediate, advanced
 * - access_type: free, regular, premium
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type'        => fake()->randomElement(['bootcamp', 'course']),
            'level'       => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'access_type' => fake()->randomElement(['free', 'regular', 'premium']),
            'price'       => fake()->randomFloat(2, 0, 500),
            'duration'    => fake()->randomElement(['2 weeks', '4 weeks', '6 weeks', '20 hours', '40 hours']),
            'instructor'  => fake()->name(),
        ];
    }
}
