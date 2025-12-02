<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\NeedAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NeedAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_need_assessment()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($student);

        $session = MentoringSession::create([
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'status' => 'scheduled',
            'type' => 'academic',
            'schedule' => now()->addDay(),
        ]);

        $response = $this->postJson("/api/mentoring-sessions/{$session->id}/need-assessments", [
            'form_data' => [
                'learning_goals' => 'Learn Laravel',
                'previous_experience' => 'Basic PHP',
                'challenges' => 'Testing',
                'expectations' => 'Mastery',
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'id',
                    'mentoring_session_id',
                    'form_data',
                ]
            ]);
        
        $this->assertDatabaseHas('need_assessments', [
            'mentoring_session_id' => $session->id,
        ]);
    }

    public function test_can_view_need_assessment()
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($mentor);

        $session = MentoringSession::create([
            'mentor_id' => $mentor->id,
            'member_id' => $student->id,
            'status' => 'scheduled',
            'type' => 'academic',
            'schedule' => now()->addDay(),
        ]);

        NeedAssessment::create([
            'mentoring_session_id' => $session->id,
            'form_data' => ['goals' => 'Learn Laravel'],
        ]);

        $response = $this->getJson("/api/mentoring-sessions/{$session->id}/need-assessments", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'id',
                    'form_data',
                    'is_completed',
                ],
            ]);
    }
}
