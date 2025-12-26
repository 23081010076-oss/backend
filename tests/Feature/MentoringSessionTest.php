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
            'type' => 'academic',
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

    public function test_student_can_give_feedback_and_visible_in_response()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        
        $session = MentoringSession::create([
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'status' => 'completed',
            'type' => 'academic',
            'schedule' => now()->subDay(),
        ]);

        $token = JWTAuth::fromUser($student);

        $response = $this->postJson("/api/mentoring-sessions/{$session->id}/feedback", [
            'rating' => 5,
            'feedback' => 'Great session!',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

        // Assert feedback is saved in reviews table
        $this->assertDatabaseHas('reviews', [
            'reviewable_id' => $session->id,
            'reviewable_type' => MentoringSession::class,
            'user_id' => $student->id,
            'rating' => 5,
            'comment' => 'Great session!',
        ]);

        // Assert feedback is visible in session detail
        $mentorToken = JWTAuth::fromUser($mentor);
        $response = $this->getJson("/api/mentoring-sessions/{$session->id}", [
            'Authorization' => 'Bearer ' . $mentorToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.reviews.0.rating', 5)
            ->assertJsonPath('data.reviews.0.comment', 'Great session!');
    }
}
