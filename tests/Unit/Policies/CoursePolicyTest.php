<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CoursePolicyTest extends TestCase
{
    use RefreshDatabase;

    private CoursePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CoursePolicy();
    }

    /** @test */
    public function admin_can_view_any_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $result = $this->policy->viewAny($admin);

        $this->assertTrue($result);
    }

    /** @test */
    public function any_user_can_view_a_course()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $result = $this->policy->view($user, $course);

        $this->assertTrue($result);
    }

    /** @test */
    public function admin_can_create_a_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $result = $this->policy->create($admin);

        $this->assertTrue($result);
    }

    /** @test */
    public function mentor_cannot_create_a_course()
    {
        // Based on CoursePolicy, only admin can create courses
        $mentor = User::factory()->create(['role' => 'mentor']);

        $result = $this->policy->create($mentor);

        $this->assertFalse($result);
    }

    /** @test */
    public function student_cannot_create_a_course()
    {
        $user = User::factory()->create(['role' => 'student']);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    /** @test */
    public function admin_can_update_any_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::factory()->create();

        $result = $this->policy->update($admin, $course);

        $this->assertTrue($result);
    }

    /** @test */
    public function mentor_cannot_update_course()
    {
        // Based on CoursePolicy, only admin can update courses
        $mentor = User::factory()->create(['role' => 'mentor']);
        $course = Course::factory()->create();

        $result = $this->policy->update($mentor, $course);

        $this->assertFalse($result);
    }

    /** @test */
    public function student_cannot_update_course()
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $result = $this->policy->update($student, $course);

        $this->assertFalse($result);
    }

    /** @test */
    public function admin_can_delete_any_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::factory()->create();

        $result = $this->policy->delete($admin, $course);

        $this->assertTrue($result);
    }

    /** @test */
    public function student_cannot_delete_a_course()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $result = $this->policy->delete($user, $course);

        $this->assertFalse($result);
    }
}
