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
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'id',
                    'file_name',
                    'file_path',
                    'file_type',
                ]
            ]);
        
        // Assert file exists in subdirectory
        $this->assertTrue(
            count(Storage::disk('public')->files("coaching-files/{$session->id}")) > 0
        );
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

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'files',
                    'total'
                ]
            ]);
    }
}
