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

    /**
     * Test creating a course transaction
     */
    public function test_can_create_course_transaction()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $course = Course::factory()->create([
            'price' => 100000,
        ]);

        $response = $this->postJson("/api/transactions/courses/{$course->id}", [
            'payment_method' => 'manual',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'transaction' => [
                        'id',
                        'transaction_code',
                        'type',
                        'amount',
                        'payment_method',
                        'status',
                    ],
                    'instructions' => [
                        'bank_name',
                        'account_number',
                        'account_holder',
                        'instructions',
                    ],
                ],
            ])
            ->assertJson([
                'sukses' => true,
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
