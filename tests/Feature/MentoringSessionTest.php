<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MentoringSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_mentoring_sessions()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($mentor);

        MentoringSession::create([
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'status' => 'pending',
            'type' => 'academic',
            'schedule' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/mentoring-sessions', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    public function test_student_can_request_mentoring_session()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($student);

        $response = $this->postJson('/api/mentoring-sessions', [
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'type' => 'academic',
            'schedule' => '2025-12-01 10:00:00',
            'status' => 'pending',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mentoring_sessions', [
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
        ]);
    }

    public function test_mentor_can_update_status()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($mentor);

        $session = MentoringSession::create([
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'status' => 'pending',
            'type' => 'academic',
            'schedule' => now()->addDay(),
        ]);

        $response = $this->putJson("/api/mentoring-sessions/{$session->id}/status", [
            'status' => 'scheduled', // 'accepted' is not in validation list: pending,scheduled,completed,cancelled,refunded
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('mentoring_sessions', [
            'id' => $session->id,
            'status' => 'scheduled',
        ]);
    }
}
