<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_enroll_in_free_course()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $course = Course::create([
            'title' => 'Free Course',
            'price' => 0,
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $response = $this->postJson("/api/courses/{$course->id}/enroll", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_cannot_enroll_twice()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $course = Course::create([
            'title' => 'Free Course',
            'price' => 0,
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $response = $this->postJson("/api/courses/{$course->id}/enroll", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_progress()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        $response = $this->putJson("/api/enrollments/{$enrollment->id}/progress", [
            'progress' => 50,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'progress' => 50,
        ]);
    }
}
