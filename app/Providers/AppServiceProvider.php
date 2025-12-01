<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// Models
use App\Models\Achievement;
use App\Models\Article;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Experience;
use App\Models\MentoringSession;
use App\Models\Organization;
use App\Models\Review;
use App\Models\Scholarship;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;

// Policies
use App\Policies\AchievementPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ExperiencePolicy;
use App\Policies\MentoringSessionPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ScholarshipPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;

/**
 * ==========================================================================
 * APP SERVICE PROVIDER
 * ==========================================================================
 * 
 * FUNGSI: Tempat untuk mendaftarkan service-service aplikasi.
 * 
 * DI SINI KITA DAFTARKAN:
 * 1. Policies - Aturan akses untuk setiap Model
 * 2. Gate definitions - Aturan akses custom
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftar Policy untuk setiap Model
     * 
     * PENJELASAN:
     * - Key (kiri): Nama Model
     * - Value (kanan): Nama Policy yang mengatur model tersebut
     * 
     * Contoh: Achievement::class => AchievementPolicy::class
     * Artinya: Model Achievement diatur oleh AchievementPolicy
     */
    protected array $policies = [
        Achievement::class => AchievementPolicy::class,
        Article::class => ArticlePolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Experience::class => ExperiencePolicy::class,
        MentoringSession::class => MentoringSessionPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Review::class => ReviewPolicy::class,
        Scholarship::class => ScholarshipPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        Transaction::class => TransactionPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan semua Policy
        $this->registerPolicies();

        // Daftarkan Gate untuk admin
        $this->defineAdminGate();
    }

    /**
     * Mendaftarkan semua Policy ke Laravel
     */
    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Mendefinisikan Gate untuk admin
     * 
     * PENGGUNAAN:
     * - Gate::allows('admin') → return true jika user adalah admin
     * - @can('admin') di Blade
     */
    protected function defineAdminGate(): void
    {
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('mentor', function ($user) {
            return in_array($user->role, ['admin', 'mentor']);
        });
    }
}
