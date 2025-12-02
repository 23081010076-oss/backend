<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticlePolicyTest extends TestCase
{
    use RefreshDatabase;

    private ArticlePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ArticlePolicy();
    }

    /** @test */
    public function anyone_can_view_articles()
    {
        $user = User::factory()->create(['role' => 'student']);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    /** @test */
    public function anyone_can_view_a_single_article()
    {
        $user = User::factory()->create(['role' => 'student']);
        $author = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        $result = $this->policy->view($user, $article);

        $this->assertTrue($result);
    }

    /** @test */
    public function admin_can_create_article()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $result = $this->policy->create($admin);

        $this->assertTrue($result);
    }

    /** @test */
    public function mentor_can_create_article()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);

        $result = $this->policy->create($mentor);

        $this->assertTrue($result);
    }

    /** @test */
    public function student_cannot_create_article()
    {
        $student = User::factory()->create(['role' => 'student']);

        $result = $this->policy->create($student);

        $this->assertFalse($result);
    }

    /** @test */
    public function student_user_cannot_create_article()
    {
        $user = User::factory()->create(['role' => 'student']);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    /** @test */
    public function admin_can_update_any_article()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        $result = $this->policy->update($admin, $article);

        $this->assertTrue($result);
    }

    /** @test */
    public function author_can_update_own_article()
    {
        $author = User::factory()->create(['role' => 'mentor']);
        $article = Article::factory()->create(['author_id' => $author->id]);

        $result = $this->policy->update($author, $article);

        $this->assertTrue($result);
    }

    /** @test */
    public function author_cannot_update_other_article()
    {
        $author1 = User::factory()->create(['role' => 'mentor']);
        $author2 = User::factory()->create(['role' => 'mentor']);
        $article = Article::factory()->create(['author_id' => $author1->id]);

        $result = $this->policy->update($author2, $article);

        $this->assertFalse($result);
    }

    /** @test */
    public function admin_can_delete_any_article()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Article::factory()->create();

        $result = $this->policy->delete($admin, $article);

        $this->assertTrue($result);
    }

    /** @test */
    public function author_can_delete_own_article()
    {
        $author = User::factory()->create(['role' => 'mentor']);
        $article = Article::factory()->create(['author_id' => $author->id]);

        $result = $this->policy->delete($author, $article);

        $this->assertTrue($result);
    }

    /** @test */
    public function student_cannot_delete_article()
    {
        $user = User::factory()->create(['role' => 'student']);
        $article = Article::factory()->create();

        $result = $this->policy->delete($user, $article);

        $this->assertFalse($result);
    }
}
