<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_courses()
    {
        Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'level',
                    ]
                ]
            ]);
    }

    public function test_can_show_course()
    {
        $course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $course->id,
                    'title' => 'Test Course',
                ]
            ]);
    }

    public function test_admin_can_create_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = JWTAuth::fromUser($admin);

        $response = $this->postJson('/api/courses', [
            'title' => 'New Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Course created successfully']);

        $this->assertDatabaseHas('courses', ['title' => 'New Course']);
    }

    public function test_non_admin_cannot_create_course()
    {
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/courses', [
            'title' => 'New Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_course()
    {
        $course = Course::create([
            'title' => 'Old Title',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = JWTAuth::fromUser($admin);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title' => 'New Title',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('courses', ['title' => 'New Title']);
    }

    public function test_admin_can_delete_course()
    {
        $course = Course::create([
            'title' => 'To Delete',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = JWTAuth::fromUser($admin);

        $response = $this->deleteJson("/api/courses/{$course->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
