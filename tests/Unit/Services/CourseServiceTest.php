<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseService $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseService = new CourseService();
    }

    /** @test */
    public function it_can_get_courses_with_pagination()
    {
        Course::factory()->count(20)->create();

        $result = $this->courseService->getCourses([], 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(20, $result->total());
    }

    /** @test */
    public function it_can_filter_courses_by_level()
    {
        Course::factory()->count(5)->create(['level' => 'beginner']);
        Course::factory()->count(3)->create(['level' => 'intermediate']);

        $result = $this->courseService->getCourses(['level' => 'beginner']);

        $this->assertEquals(5, $result->total());
    }

    /** @test */
    public function it_can_search_courses_by_title()
    {
        Course::factory()->create(['title' => 'Laravel Basics']);
        Course::factory()->create(['title' => 'Vue.js Tutorial']);
        Course::factory()->create(['title' => 'Advanced Laravel']);

        $result = $this->courseService->getCourses(['search' => 'Laravel']);

        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_create_a_course()
    {
        $courseData = [
            'title'       => 'Test Course',
            'description' => 'Test Description',
            'level'       => 'beginner',
            'price'       => 100000,
            'instructor'  => 'John Doe',
        ];

        $course = $this->courseService->createCourse($courseData);

        $this->assertDatabaseHas('courses', ['title' => 'Test Course']);
        $this->assertEquals('Test Course', $course->title);
    }

    /** @test */
    public function it_can_update_a_course()
    {
        $course = Course::factory()->create(['title' => 'Old Title']);

        $updatedCourse = $this->courseService->updateCourse($course, [
            'title' => 'New Title',
        ]);

        $this->assertEquals('New Title', $updatedCourse->title);
        $this->assertDatabaseHas('courses', ['title' => 'New Title']);
    }

    /** @test */
    public function it_can_delete_a_course()
    {
        $course = Course::factory()->create();

        $result = $this->courseService->deleteCourse($course);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    /** @test */
    public function it_caches_course_statistics()
    {
        // Clear cache first
        Cache::flush();

        Course::factory()->count(5)->create();
        User::factory()->count(3)->create();
        
        // First call - should hit database
        $stats1 = $this->courseService->getStatistics();
        
        // Second call - should hit cache
        $stats2 = $this->courseService->getStatistics();

        $this->assertEquals($stats1, $stats2);
        $this->assertEquals(5, $stats1['total']);
    }

    /** @test */
    public function it_clears_cache_on_course_creation()
    {
        Cache::put('courses:statistics', ['cached' => true], 3600);

        $this->courseService->createCourse([
            'title'       => 'New Course',
            'description' => 'Description',
            'level'       => 'beginner',
            'price'       => 50000,
            'instructor'  => 'John Doe',
        ]);

        $this->assertFalse(Cache::has('courses:statistics'));
    }
}
