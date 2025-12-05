<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCurriculum;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CurriculumTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;
    protected string $adminToken;
    protected string $studentToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->adminToken = JWTAuth::fromUser($this->admin);
        $this->studentToken = JWTAuth::fromUser($this->student);

        $this->course = Course::create([
            'title' => 'Test Course',
            'type' => 'course',
            'level' => 'beginner',
            'access_type' => 'free',
        ]);
    }

    // ==========================================================================
    // CURRICULUM CRUD
    // ==========================================================================

    public function test_authenticated_user_can_list_curriculums()
    {
        CourseCurriculum::create([
            'course_id' => $this->course->id,
            'section' => 'Bab 1',
            'section_order' => 1,
            'title' => 'Materi 1',
            'order' => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->getJson("/api/courses/{$this->course->id}/curriculums");

        $response->assertStatus(200)
            ->assertJson(['sukses' => true]);
    }

    public function test_admin_can_create_curriculum()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/courses/{$this->course->id}/curriculums", [
                'title' => 'Materi Baru',
                'section' => 'Bab 1',
                'description' => 'Deskripsi materi',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('course_curriculums', ['title' => 'Materi Baru']);
    }

    public function test_admin_can_delete_curriculum()
    {
        $curriculum = CourseCurriculum::create([
            'course_id' => $this->course->id,
            'title' => 'To Delete',
            'order' => 1,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson("/api/courses/{$this->course->id}/curriculums/{$curriculum->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('course_curriculums', ['id' => $curriculum->id]);
    }

    // ==========================================================================
    // PROGRESS TRACKING
    // ==========================================================================

    public function test_student_can_mark_curriculum_completed()
    {
        $curriculum = CourseCurriculum::create([
            'course_id' => $this->course->id,
            'title' => 'Materi 1',
            'order' => 1,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'progress' => 0,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson("/api/enrollments/{$enrollment->id}/curriculums/{$curriculum->id}/complete");

        $response->assertStatus(200)
            ->assertJson(['sukses' => true]);

        $this->assertDatabaseHas('curriculum_progress', [
            'enrollment_id' => $enrollment->id,
            'curriculum_id' => $curriculum->id,
            'completed' => true,
        ]);
    }

    public function test_progress_auto_calculated()
    {
        $c1 = CourseCurriculum::create(['course_id' => $this->course->id, 'title' => 'M1', 'order' => 1]);
        $c2 = CourseCurriculum::create(['course_id' => $this->course->id, 'title' => 'M2', 'order' => 2]);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson("/api/enrollments/{$enrollment->id}/curriculums/{$c1->id}/complete");

        $enrollment->refresh();
        $this->assertEquals(50, $enrollment->calculated_progress);
        $this->assertEquals(1, $enrollment->completed_materials);
    }

    public function test_enrollment_completed_when_all_done()
    {
        $curriculum = CourseCurriculum::create([
            'course_id' => $this->course->id,
            'title' => 'Only Material',
            'order' => 1,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'completed' => false,
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson("/api/enrollments/{$enrollment->id}/curriculums/{$curriculum->id}/complete");

        $enrollment->refresh();
        $this->assertEquals(100, $enrollment->calculated_progress);
        $this->assertTrue($enrollment->completed);
    }

    public function test_course_includes_curriculums_and_calculated_fields()
    {
        CourseCurriculum::create([
            'course_id' => $this->course->id,
            'section' => 'Bab 1',
            'title' => 'Materi 1',
            'duration' => '1 jam',
            'order' => 1,
        ]);

        $response = $this->getJson("/api/courses/{$this->course->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.total_materials', 1);
    }
}
