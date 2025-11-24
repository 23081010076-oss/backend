<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ScholarshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_scholarships()
    {
        $user = User::factory()->create();
        $org = Organization::create([
            'user_id' => $user->id,
            'name' => 'Test Org',
            'type' => 'company',
        ]);

        Scholarship::create([
            'organization_id' => $org->id,
            'name' => 'Test Scholarship',
            'status' => 'open',
        ]);

        $response = $this->getJson('/api/scholarships');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'organization_id',
                    ]
                ]
            ]);
    }

    public function test_corporate_can_create_scholarship()
    {
        $user = User::factory()->create(['role' => 'corporate']);
        $token = JWTAuth::fromUser($user);
        
        $org = Organization::create([
            'user_id' => $user->id,
            'name' => 'Test Org',
            'type' => 'company',
        ]);

        $response = $this->postJson('/api/scholarships', [
            'organization_id' => $org->id,
            'name' => 'New Scholarship',
            'description' => 'Description',
            'status' => 'open',
            'deadline' => '2025-12-31',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('scholarships', ['name' => 'New Scholarship']);
    }

    public function test_student_can_apply_for_scholarship()
    {
        $corp = User::factory()->create(['role' => 'corporate']);
        $org = Organization::create([
            'user_id' => $corp->id,
            'name' => 'Test Org',
            'type' => 'company',
        ]);

        $scholarship = Scholarship::create([
            'organization_id' => $org->id,
            'name' => 'Test Scholarship',
            'status' => 'open',
        ]);

        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($student);

        $response = $this->postJson("/api/scholarships/{$scholarship->id}/apply", [
            'cover_letter' => 'I want this.',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('scholarship_applications', [
            'scholarship_id' => $scholarship->id,
            'user_id' => $student->id,
        ]);
    }
}
