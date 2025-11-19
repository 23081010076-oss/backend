<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ScholarshipController;
use App\Http\Controllers\Api\MentoringSessionController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CorporateContactController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\NeedAssessmentController;
use App\Http\Controllers\CoachingFileController;


// PUBLIC ROUTES (No Authentication Required)


// Authentication endpoints
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Public resources (read-only) - Anyone can view
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
Route::get('/scholarships/{id}', [ScholarshipController::class, 'show'])->name('scholarships.show');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Corporate Contact (public - companies can submit inquiry)
Route::post('/corporate-contact', [CorporateContactController::class, 'store'])->name('corporate-contact.store');


// PROTECTED ROUTES (Authentication Required)

Route::middleware('auth:api')->group(function () {
    
    
    // AUTHENTICATION & PROFILE MANAGEMENT
    
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
        Route::post('/profile/photo', [AuthController::class, 'uploadProfilePhoto'])->name('upload-photo');
        Route::get('/portfolio', [AuthController::class, 'portfolio'])->name('portfolio');
        Route::get('/activity-history', [AuthController::class, 'activityHistory'])->name('activity-history');
    });

    
    // USER MANAGEMENT (Admin Only)
    
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
    });

    
    // ACHIEVEMENTS MANAGEMENT
    
    Route::apiResource('achievements', AchievementController::class)->names([
        'index' => 'achievements.index',
        'store' => 'achievements.store',
        'show' => 'achievements.show',
        'update' => 'achievements.update',
        'destroy' => 'achievements.destroy',
    ]);

    
    // EXPERIENCES MANAGEMENT
    
    Route::apiResource('experiences', ExperienceController::class)->names([
        'index' => 'experiences.index',
        'store' => 'experiences.store',
        'show' => 'experiences.show',
        'update' => 'experiences.update',
        'destroy' => 'experiences.destroy',
    ]);

    
    // ORGANIZATIONS MANAGEMENT
    
    Route::apiResource('organizations', OrganizationController::class)->names([
        'index' => 'organizations.index',
        'store' => 'organizations.store',
        'show' => 'organizations.show',
        'update' => 'organizations.update',
        'destroy' => 'organizations.destroy',
    ]);

    
    // SUBSCRIPTIONS MANAGEMENT
    
    Route::apiResource('subscriptions', SubscriptionController::class)->names([
        'index' => 'subscriptions.index',
        'store' => 'subscriptions.store',
        'show' => 'subscriptions.show',
        'update' => 'subscriptions.update',
        'destroy' => 'subscriptions.destroy',
    ]);
    Route::post('/subscriptions/{id}/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade');

    
    // REVIEWS MANAGEMENT
    
    Route::apiResource('reviews', ReviewController::class, ['only' => ['store', 'show', 'update', 'destroy']])->names([
        'store' => 'reviews.store',
        'show' => 'reviews.show',
        'update' => 'reviews.update',
        'destroy' => 'reviews.destroy',
    ]);

    
    // COURSES & ENROLLMENT
    
    Route::prefix('courses')->name('courses.')->group(function () {
        // Course Management (Admin Only)
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [CourseController::class, 'store'])->name('store');
            Route::put('/{id}', [CourseController::class, 'update'])->name('update');
            Route::delete('/{id}', [CourseController::class, 'destroy'])->name('destroy');
        });

        // Enrollment endpoints
        Route::post('/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('enroll');
    });

    Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('my-courses');
    Route::apiResource('enrollments', EnrollmentController::class)->names([
        'index' => 'enrollments.index',
        'show' => 'enrollments.show',
        'store' => 'enrollments.store',
        'update' => 'enrollments.update',
        'destroy' => 'enrollments.destroy',
    ]);
    Route::put('/enrollments/{id}/progress', [EnrollmentController::class, 'updateProgress'])->name('enrollments.update-progress');

    
    // SCHOLARSHIPS
    
    Route::prefix('scholarships')->name('scholarships.')->group(function () {
        // Scholarship Management (Admin/Corporate)
        Route::middleware('role:admin,corporate')->group(function () {
            Route::post('/', [ScholarshipController::class, 'store'])->name('store');
            Route::put('/{id}', [ScholarshipController::class, 'update'])->name('update');
            Route::delete('/{id}', [ScholarshipController::class, 'destroy'])->name('destroy');
        });

        // Apply & Manage Applications
        Route::post('/{id}/apply', [ScholarshipController::class, 'apply'])->name('apply');
    });

    Route::get('/my-applications', [ScholarshipController::class, 'myApplications'])->name('my-applications');
    Route::put('/scholarship-applications/{id}/status', [ScholarshipController::class, 'updateStatus'])
        ->middleware('role:admin,corporate')
        ->name('scholarship-applications.update-status');

    
    // MENTORING SESSIONS
    
    Route::apiResource('mentoring-sessions', MentoringSessionController::class)->names([
        'index' => 'mentoring-sessions.index',
        'store' => 'mentoring-sessions.store',
        'show' => 'mentoring-sessions.show',
        'update' => 'mentoring-sessions.update',
        'destroy' => 'mentoring-sessions.destroy',
    ]);
    Route::post('/mentoring-sessions/{id}/schedule', [MentoringSessionController::class, 'schedule'])->name('mentoring-sessions.schedule');
    Route::put('/mentoring-sessions/{id}/status', [MentoringSessionController::class, 'updateStatus'])->name('mentoring-sessions.update-status');
    Route::get('/my-mentoring-sessions', [MentoringSessionController::class, 'mySessions'])->name('my-mentoring-sessions');

    
    // ARTICLES (Admin/Corporate Can Create)
    
    Route::middleware('role:admin,corporate')->group(function () {
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });

    
    // CORPORATE CONTACT MANAGEMENT (Admin Only)
    
    Route::middleware('role:admin')->group(function () {
        Route::prefix('corporate-contacts')->name('corporate-contacts.')->group(function () {
            Route::get('/', [CorporateContactController::class, 'index'])->name('index');
            Route::get('/{id}', [CorporateContactController::class, 'show'])->name('show');
            Route::put('/{id}/status', [CorporateContactController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{id}', [CorporateContactController::class, 'destroy'])->name('destroy');
        });
    });

    

    // NEED ASSESSMENT (for mentoring sessions)
    
    Route::prefix('mentoring-sessions/{mentoringSessionId}/need-assessments')->name('need-assessments.')->group(function () {
        Route::get('/', [NeedAssessmentController::class, 'show'])->name('show');
        Route::post('/', [NeedAssessmentController::class, 'store'])->name('store');
        Route::put('/mark-completed', [NeedAssessmentController::class, 'markCompleted'])->name('mark-completed');
        Route::delete('/', [NeedAssessmentController::class, 'destroy'])->name('destroy');
    });

    
    // COACHING FILES (for mentoring sessions)
    
    Route::prefix('mentoring-sessions/{mentoringSessionId}/coaching-files')->name('coaching-files.')->group(function () {
        Route::get('/', [CoachingFileController::class, 'index'])->name('index');
        Route::post('/', [CoachingFileController::class, 'store'])->name('store');
        Route::get('/{fileId}', [CoachingFileController::class, 'show'])->name('show');
        Route::get('/{fileId}/download', [CoachingFileController::class, 'download'])->name('download');
        Route::delete('/{fileId}', [CoachingFileController::class, 'destroy'])->name('destroy');
        Route::delete('/', [CoachingFileController::class, 'destroyAll'])->name('destroy-all');
    });

});




