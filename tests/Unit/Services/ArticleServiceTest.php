<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    private ArticleService $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = new ArticleService();
    }

    /** @test */
    public function it_can_get_articles_with_pagination()
    {
        $user = User::factory()->create();
        Article::factory()->count(15)->create(['author_id' => $user->id]);

        $result = $this->articleService->getArticles([], 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $result->total());
    }

    /** @test */
    public function it_can_filter_articles_by_category()
    {
        $user = User::factory()->create();
        Article::factory()->count(5)->create([
            'author_id' => $user->id,
            'category'  => 'technology',
        ]);
        Article::factory()->count(3)->create([
            'author_id' => $user->id,
            'category'  => 'business',
        ]);

        $result = $this->articleService->getArticles(['category' => 'technology']);

        $this->assertEquals(5, $result->total());
    }

    /** @test */
    public function it_can_search_articles_by_title()
    {
        $user = User::factory()->create();
        Article::factory()->create([
            'author_id' => $user->id,
            'title'     => 'Laravel Tutorial',
        ]);
        Article::factory()->create([
            'author_id' => $user->id,
            'title'     => 'Vue.js Guide',
        ]);

        $result = $this->articleService->getArticles(['search' => 'Laravel']);

        $this->assertEquals(1, $result->total());
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $user = User::factory()->create();

        $articleData = [
            'title'     => 'Test Article',
            'content'   => 'Test content for the article',
            'category'  => 'technology',
        ];

        $article = $this->articleService->createArticle($articleData, $user);

        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
        $this->assertEquals('Test Article', $article->title);
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'title'     => 'Old Title',
        ]);

        $updatedArticle = $this->articleService->updateArticle($article, [
            'title' => 'New Title',
        ]);

        $this->assertEquals('New Title', $updatedArticle->title);
        $this->assertDatabaseHas('articles', ['title' => 'New Title']);
    }

    /** @test */
    public function it_can_delete_an_article()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id]);

        $result = $this->articleService->deleteArticle($article);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /** @test */
    public function it_can_get_popular_articles()
    {
        Cache::flush();
        
        $user = User::factory()->create();
        Article::factory()->count(10)->create([
            'author_id'  => $user->id,
        ]);

        $result = $this->articleService->getPopularArticles(5);

        $this->assertCount(5, $result);
    }

    /** @test */
    public function it_clears_cache_on_article_creation()
    {
        Cache::put('articles:popular:5', ['cached' => true], 3600);

        $user = User::factory()->create();
        $this->articleService->createArticle([
            'title'     => 'New Article',
            'content'   => 'Content',
            'category'  => 'tech',
        ], $user);

        $this->assertFalse(Cache::has('articles:popular:5'));
    }
}
