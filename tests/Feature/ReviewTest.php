<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_reviews()
    {
        $user = User::factory()->create();
        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        Review::create([
            'user_id' => $user->id,
            'reviewable_id' => $course->id,
            'reviewable_type' => Course::class,
            'rating' => 5,
            'comment' => 'Great course!',
        ]);

        $response = $this->getJson('/api/reviews');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'rating',
                        'comment',
                    ]
                ]
            ]);
    }

    public function test_user_can_create_review()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $response = $this->postJson('/api/reviews', [
            'reviewable_id' => $course->id,
            'reviewable_type' => Course::class,
            'rating' => 4,
            'comment' => 'Good',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', ['comment' => 'Good']);
    }

    public function test_user_can_update_review()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'reviewable_id' => $course->id,
            'reviewable_type' => Course::class,
            'rating' => 5,
            'comment' => 'Great course!',
        ]);

        $response = $this->putJson("/api/reviews/{$review->id}", [
            'comment' => 'Updated comment',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', ['comment' => 'Updated comment']);
    }

    public function test_user_can_delete_review()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'reviewable_id' => $course->id,
            'reviewable_type' => Course::class,
            'rating' => 5,
            'comment' => 'Great course!',
        ]);

        $response = $this->deleteJson("/api/reviews/{$review->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
