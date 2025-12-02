<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'title'     => fake()->sentence(),
            'content'   => fake()->paragraphs(3, true),
            'category'  => fake()->randomElement(['technology', 'business', 'education', 'lifestyle']),
            'author'    => fake()->name(),
        ];
    }
}
