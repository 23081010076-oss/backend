<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_course_transaction()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $course = Course::create([
            'title' => 'Test Course',
            'price' => 100000,
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'regular',
        ]);

        $response = $this->postJson("/api/transactions/courses/{$course->id}", [
            'payment_method' => 'manual',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'transaction_code',
                    'amount',
                    'status',
                ],
            ]);
        
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 100000,
            'type' => 'course_enrollment',
        ]);
    }

    public function test_can_list_transactions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/transactions', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }
}
