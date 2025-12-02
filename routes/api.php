<?php

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
use App\Http\Controllers\Api\NeedAssessmentController;
use App\Http\Controllers\Api\CoachingFileController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Api\MidtransWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route API menggunakan prefix /api/ (otomatis dari Laravel)
| Contoh: POST /api/register, GET /api/courses
|
| Struktur Route:
| - Public Routes: Bisa diakses tanpa login
| - Protected Routes: Butuh JWT token (auth:api)
| - Role-based Routes: Butuh role tertentu (admin, mentor, corporate)
|
*/

// ==========================================================================
// WEBHOOK ROUTES (Public - untuk callback dari payment gateway)
// ==========================================================================

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handleNotification'])
    ->name('midtrans.webhook');

// ==========================================================================
// PUBLIC ROUTES (Tanpa Autentikasi)
// ==========================================================================

// Authentication
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:register')
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

// Public Resources (read-only)
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
Route::get('/scholarships/{id}', [ScholarshipController::class, 'show'])->name('scholarships.show');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Corporate Contact (public - perusahaan bisa submit inquiry)
Route::post('/corporate-contact', [CorporateContactController::class, 'store'])
    ->name('corporate-contact.store');

// ==========================================================================
// PROTECTED ROUTES (Butuh Autentikasi)
// ==========================================================================

Route::middleware('auth:api')->group(function () {

    // ======================================================================
    // AUTH & PROFILE MANAGEMENT
    // ======================================================================
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::match(['put', 'patch'], '/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
        Route::post('/profile/photo', [AuthController::class, 'uploadProfilePhoto'])
            ->middleware('throttle:uploads')
            ->name('upload-photo');
        Route::post('/profile/cv', [AuthController::class, 'uploadCv'])
            ->middleware('throttle:uploads')
            ->name('upload-cv');
        Route::get('/recommendations', [AuthController::class, 'recommendations'])->name('recommendations');
        Route::get('/portfolio', [AuthController::class, 'portfolio'])->name('portfolio');
        Route::get('/activity-history', [AuthController::class, 'activityHistory'])->name('activity-history');
    });

    // ======================================================================
    // USER MANAGEMENT (Admin Only)
    // ======================================================================
    Route::middleware('role:admin')->prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
        Route::get('/mentors', [UserController::class, 'mentors'])->name('mentors');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/status', [UserController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/suspend', [UserController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/activate', [UserController::class, 'activate'])->name('activate');
    });

    // ======================================================================
    // PORTFOLIO: ACHIEVEMENTS, EXPERIENCES, ORGANIZATIONS
    // ======================================================================
    Route::apiResource('achievements', AchievementController::class);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('organizations', OrganizationController::class);

    // ======================================================================
    // SUBSCRIPTIONS
    // ======================================================================
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{id}/upgrade', [SubscriptionController::class, 'upgrade'])
        ->name('subscriptions.upgrade');

    // ======================================================================
    // REVIEWS
    // ======================================================================
    Route::apiResource('reviews', ReviewController::class)->only(['store', 'show', 'update', 'destroy']);

    // ======================================================================
    // COURSES & ENROLLMENT
    // ======================================================================
    // Course CRUD (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // Enrollment
    Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('my-courses');
    Route::apiResource('enrollments', EnrollmentController::class);
    Route::put('/enrollments/{id}/progress', [EnrollmentController::class, 'updateProgress'])
        ->name('enrollments.update-progress');

    // ======================================================================
    // SCHOLARSHIPS
    // ======================================================================
    // Scholarship CRUD (Admin/Corporate)
    Route::middleware('role:admin,corporate')->group(function () {
        Route::post('/scholarships', [ScholarshipController::class, 'store'])->name('scholarships.store');
        Route::put('/scholarships/{id}', [ScholarshipController::class, 'update'])->name('scholarships.update');
        Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');
    });

    // Scholarship Application
    Route::post('/scholarships/{id}/apply', [ScholarshipController::class, 'apply'])->name('scholarships.apply');
    Route::get('/my-applications', [ScholarshipController::class, 'myApplications'])->name('my-applications');
    Route::put('/scholarship-applications/{id}/status', [ScholarshipController::class, 'updateStatus'])
        ->middleware('role:admin,corporate')
        ->name('scholarship-applications.update-status');

    // ======================================================================
    // MENTORING SESSIONS
    // ======================================================================
    Route::apiResource('mentoring-sessions', MentoringSessionController::class);
    Route::get('/mentoring-sessions/{mentorId}/schedule', [MentoringSessionController::class, 'schedule'])
        ->name('mentoring-sessions.schedule');
    Route::put('/mentoring-sessions/{id}/status', [MentoringSessionController::class, 'updateStatus'])
        ->name('mentoring-sessions.update-status');
    Route::post('/mentoring-sessions/{id}/feedback', [MentoringSessionController::class, 'feedback'])
        ->name('mentoring-sessions.feedback');
    Route::get('/my-mentoring-sessions', [MentoringSessionController::class, 'mySessions'])
        ->name('my-mentoring-sessions');

    // Need Assessment (nested under mentoring sessions)
    Route::prefix('mentoring-sessions/{mentoringSessionId}/need-assessments')->name('need-assessments.')->group(function () {
        Route::get('/', [NeedAssessmentController::class, 'show'])->name('show');
        Route::post('/', [NeedAssessmentController::class, 'store'])->name('store');
        Route::put('/', [NeedAssessmentController::class, 'update'])->name('update');
        Route::put('/mark-completed', [NeedAssessmentController::class, 'markCompleted'])->name('mark-completed');
        Route::delete('/', [NeedAssessmentController::class, 'destroy'])->name('destroy');
    });

    // Coaching Files (nested under mentoring sessions)
    Route::prefix('mentoring-sessions/{mentoringSessionId}/coaching-files')->name('coaching-files.')->group(function () {
        Route::get('/', [CoachingFileController::class, 'index'])->name('index');
        Route::post('/', [CoachingFileController::class, 'store'])->name('store');
        Route::get('/{fileId}', [CoachingFileController::class, 'show'])->name('show');
        Route::get('/{fileId}/download', [CoachingFileController::class, 'download'])->name('download');
        Route::delete('/{fileId}', [CoachingFileController::class, 'destroy'])->name('destroy');
        Route::delete('/', [CoachingFileController::class, 'destroyAll'])->name('destroy-all');
    });

    // ======================================================================
    // ARTICLES (Admin/Corporate)
    // ======================================================================
    Route::middleware('role:admin,corporate')->group(function () {
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });

    // Article additional routes (authenticated)
    Route::get('/articles/popular', [ArticleController::class, 'popular'])->name('articles.popular');
    Route::get('/articles/category/{category}', [ArticleController::class, 'byCategory'])->name('articles.by-category');

    // ======================================================================
    // CORPORATE CONTACT MANAGEMENT (Admin Only)
    // ======================================================================
    Route::middleware('role:admin')->prefix('corporate-contacts')->name('corporate-contacts.')->group(function () {
        Route::get('/', [CorporateContactController::class, 'index'])->name('index');
        Route::get('/statistics', [CorporateContactController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [CorporateContactController::class, 'show'])->name('show');
        Route::put('/{id}', [CorporateContactController::class, 'update'])->name('update');
        Route::put('/{id}/status', [CorporateContactController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [CorporateContactController::class, 'destroy'])->name('destroy');
    });

    // ======================================================================
    // TRANSACTIONS
    // ======================================================================
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');

        // Admin Only - harus di atas route dengan parameter {id}
        Route::middleware('role:admin')->group(function () {
            Route::get('/statistics', [TransactionController::class, 'statistics'])->name('statistics');
        });

        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');

        // Create Transactions
        Route::post('/courses/{courseId}', [TransactionController::class, 'createCourseTransaction'])
            ->name('course.store');
        Route::post('/subscriptions', [TransactionController::class, 'createSubscriptionTransaction'])
            ->name('subscription.store');
        Route::post('/mentoring-sessions/{sessionId}', [TransactionController::class, 'createMentoringTransaction'])
            ->name('mentoring.store');

        // Payment Operations
        Route::post('/{id}/payment-proof', [TransactionController::class, 'uploadPaymentProof'])
            ->name('payment-proof.upload');
        Route::post('/{id}/refund', [TransactionController::class, 'requestRefund'])
            ->name('refund.request');

        // Admin Only
        Route::middleware('role:admin')->group(function () {
            Route::post('/{id}/confirm', [TransactionController::class, 'confirmPayment'])->name('confirm-payment');
        });
    });

});
