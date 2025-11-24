<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CoachingFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_coaching_file()
    {
        Storage::fake('public');

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

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson("/api/mentoring-sessions/{$session->id}/coaching-files", [
            'file' => $file,
            'file_name' => 'My Plan',
            'file_type' => 'pdf',
            'uploaded_by' => $mentor->id,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        Storage::disk('public')->assertExists('coaching-files/My Plan');
    }

    public function test_can_list_coaching_files()
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

        $response = $this->getJson("/api/mentoring-sessions/{$session->id}/coaching-files", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }
}
