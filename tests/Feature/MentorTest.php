<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MentorTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_mentors()
    {
        // Create mentors
        User::factory()->count(3)->create(['role' => 'mentor']);
        
        // Create regular user
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/mentors', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ]
                ],
                'meta'
            ]);
    }

    public function test_authenticated_user_can_view_mentor_detail()
    {
        // Create mentor
        $mentor = User::factory()->create(['role' => 'mentor']);
        
        // Create regular user
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/mentors/' . $mentor->id, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ]
            ]);
    }

    public function test_authenticated_user_can_view_mentor_schedule()
    {
        // Create mentor
        $mentor = User::factory()->create(['role' => 'mentor']);
        
        // Create regular user
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/mentors/' . $mentor->id . '/schedule', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_list_mentors()
    {
        User::factory()->count(3)->create(['role' => 'mentor']);

        $response = $this->getJson('/api/mentors');

        $response->assertStatus(401);
    }

    public function test_admin_can_list_mentors_via_admin_endpoint()
    {
        // Create mentors
        User::factory()->count(3)->create(['role' => 'mentor']);
        
        // Create admin
        $admin = User::factory()->create(['role' => 'admin']);
        $token = JWTAuth::fromUser($admin);

        $response = $this->getJson('/api/admin/users/mentors', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_mentor_endpoint()
    {
        User::factory()->count(3)->create(['role' => 'mentor']);
        
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/admin/users/mentors', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403);
    }
}
