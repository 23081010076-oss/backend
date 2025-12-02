<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_articles()
    {
        $user = User::factory()->create();
        Article::create([
            'author_id' => $user->id,
            'title' => 'Test Article',
            'content' => 'Content',
        ]);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author_id',
                    ]
                ]
            ]);
    }

    public function test_admin_can_create_article()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = JWTAuth::fromUser($admin);

        $response = $this->postJson('/api/articles', [
            'title' => 'New Article',
            'content' => 'Some content',
            'category' => 'Tech',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', ['title' => 'New Article']);
    }

    public function test_can_show_article()
    {
        $user = User::factory()->create();
        $article = Article::create([
            'author_id' => $user->id,
            'title' => 'Test Article',
            'content' => 'Content',
            'category' => 'Tech',
        ]);

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $article->id,
                    'title' => 'Test Article',
                ]
            ]);
    }
}
