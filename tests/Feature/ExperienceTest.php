<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_experiences()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Experience::create([
            'user_id' => $user->id,
            'title' => 'Developer',
            'company' => 'Tech Corp',
            'start_date' => '2020-01-01',
        ]);

        $response = $this->getJson('/api/experiences', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'user_id',
                    ]
                ]
            ]);
    }

    public function test_user_can_create_experience()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/experiences', [
            'title' => 'New Job',
            'company' => 'New Corp',
            'start_date' => '2021-01-01',
            'type' => 'work',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('experiences', ['title' => 'New Job']);
    }

    public function test_user_can_update_experience()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $experience = Experience::create([
            'user_id' => $user->id,
            'title' => 'Old Job',
            'company' => 'Old Corp',
            'start_date' => '2020-01-01',
        ]);

        $response = $this->putJson("/api/experiences/{$experience->id}", [
            'title' => 'Updated Job',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('experiences', ['title' => 'Updated Job']);
    }

    public function test_user_can_delete_experience()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $experience = Experience::create([
            'user_id' => $user->id,
            'title' => 'To Delete',
            'company' => 'Corp',
            'start_date' => '2020-01-01',
        ]);

        $response = $this->deleteJson("/api/experiences/{$experience->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('experiences', ['id' => $experience->id]);
    }
}
