<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Class SubscriptionService
 * 
 * Handles all business logic related to subscriptions.
 * Provides methods for creating, updating, and upgrading subscriptions.
 * 
 * @package App\Services
 */
class SubscriptionService
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    /**
     * Create a new subscription for a user
     *
     * @param array $data Subscription data
     * @param User $user User who is subscribing
     * @return array
     * @throws InvalidArgumentException
     */
    public function createSubscription(array $data, User $user): array
    {
        try {
            DB::beginTransaction();

            // ✅ FIX: Check for duplicate active subscription
            $existingSubscription = $user->subscriptions()
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first();

            if ($existingSubscription) {
                // ✅ EXCEPTION: Allow upgrading from Free plan by creating new subscription
                // Free plan can be replaced because it's a default/trial plan
                if ($existingSubscription->plan === 'free') {
                    // Cancel old Free subscription before creating new one
                    $existingSubscription->update(['status' => 'cancelled']);
                    Log::info('Free subscription cancelled for upgrade', [
                        'old_subscription_id' => $existingSubscription->id,
                        'user_id' => $user->id,
                    ]);
                } else {
                    // For paid plans (regular, premium), must use upgrade endpoint
                    $planName = ucfirst($existingSubscription->plan);
                    $endDate = \Carbon\Carbon::parse($existingSubscription->end_date)->format('d M Y');
                    
                    if ($existingSubscription->plan === 'premium') {
                        throw new InvalidArgumentException("Anda sudah berlangganan paket {$planName} (paket tertinggi) hingga {$endDate}. Tidak perlu berlangganan lagi.");
                    } else {
                        throw new InvalidArgumentException("Anda sudah berlangganan paket {$planName} hingga {$endDate}. Jika ingin upgrade, gunakan fitur Upgrade Subscription.");
                    }
                }
            }

            $data['user_id'] = $user->id;
            $data['status'] = 'pending'; // ✅ FIX: Start as pending, activate after payment
            
            // Validate course selection for single_course package
            if ($data['package_type'] === 'single_course') {
                $this->validateSingleCoursePackage($data);
            }
            
            $subscription = Subscription::create($data);

            // Create transaction
            $paymentMethod = $data['payment_method'] ?? 'manual';
            $transaction = $this->transactionService->createSubscriptionTransaction(
                $user,
                $subscription->plan,
                $paymentMethod
            );

            DB::commit();

            Log::info('Subscription created successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan' => $subscription->plan,
                'package_type' => $subscription->package_type,
                'transaction_id' => $transaction['transaction']->id,
                'payment_method' => $paymentMethod,
            ]);

            return [
                'subscription' => $subscription,
                'transaction' => $transaction['transaction'],
            ];
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Subscription creation failed: validation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription creation failed: unexpected error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException('Failed to create subscription. Please try again later.');
        }
    }
    
    /**
     * Update an existing subscription
     *
     * @param Subscription $subscription Subscription to update
     * @param array $data Update data
     * @return Subscription
     */
    public function updateSubscription(Subscription $subscription, array $data): Subscription
    {
        try {
            DB::beginTransaction();

            $subscription->update($data);

            DB::commit();

            Log::info('Subscription updated successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);

            return $subscription->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription update failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to update subscription. Please try again later.');
        }
    }
    
    /**
     * Upgrade a subscription to a higher plan
     *
     * @param Subscription $subscription Subscription to upgrade
     * @param string $plan New plan name
     * @return Subscription
     * @throws InvalidArgumentException
     */
    public function upgradeSubscription(Subscription $subscription, string $plan): Subscription
    {
        try {
            DB::beginTransaction();

            // Validate upgrade eligibility
            $this->validateUpgrade($subscription, $plan);
            
            // Calculate new end_date: extend from current end_date based on duration
            $newEndDate = $this->calculateNewEndDate($subscription);
            
            $oldPlan = $subscription->plan;
            
            // ✅ FIX: Status tetap pending sampai admin konfirmasi pembayaran upgrade
            $subscription->update([
                'plan' => $plan,
                'status' => 'pending',
                'end_date' => $newEndDate,
            ]);

            // ⚠️ CATATAN: AUTO-ENROLL tidak dilakukan di sini
            // Auto-enroll akan dilakukan di TransactionService->confirmPayment()
            // setelah admin mengkonfirmasi pembayaran upgrade

            DB::commit();

            Log::info('Subscription upgraded successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'old_plan' => $oldPlan,
                'new_plan' => $plan,
            ]);
            
            return $subscription->fresh();
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Subscription upgrade failed: validation error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription upgrade failed: unexpected error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to upgrade subscription. Please try again later.');
        }
    }

    /**
     * Validate single course package requirements
     *
     * @param array $data Subscription data
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateSingleCoursePackage(array $data): void
    {
        if (empty($data['courses_ids'])) {
            throw new InvalidArgumentException('Course selection is required for single course package');
        }
        
        if (!$this->validateCourseSelection($data['courses_ids'])) {
            throw new InvalidArgumentException('One or more selected courses do not exist');
        }
    }

    /**
     * Validate upgrade eligibility
     *
     * @param Subscription $subscription Current subscription
     * @param string $newPlan New plan to upgrade to
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateUpgrade(Subscription $subscription, string $newPlan): void
    {
        // Cannot upgrade expired subscription
        if ($subscription->status === 'expired') {
            throw new InvalidArgumentException('Tidak dapat upgrade subscription yang sudah expired. Silakan buat subscription baru.');
        }
        
        // Validate upgrade path - no downgrades
        if ($subscription->plan === 'premium' && $newPlan !== 'premium') {
            throw new InvalidArgumentException('Anda sudah berlangganan paket Premium (paket tertinggi). Tidak bisa downgrade ke paket yang lebih rendah.');
        }
        
        if ($subscription->plan === 'regular' && $newPlan === 'free') {
            throw new InvalidArgumentException('Tidak bisa downgrade dari paket Regular ke Free.');
        }

        // Prevent "upgrading" to the same plan
        if ($subscription->plan === $newPlan) {
            $planName = ucfirst($newPlan);
            throw new InvalidArgumentException("Anda sudah berlangganan paket {$planName}. Tidak perlu upgrade lagi.");
        }
    }
    
    /**
     * Calculate new end date for subscription
     *
     * @param Subscription $subscription Subscription instance
     * @return \Carbon\Carbon
     */
    protected function calculateNewEndDate(Subscription $subscription): \Carbon\Carbon
    {
        $currentEndDate = $subscription->end_date ?? now();
        
        return $subscription->duration_unit === 'years' 
            ? $currentEndDate->addYears($subscription->duration)
            : $currentEndDate->addMonths($subscription->duration);
    }
    
    /**
     * Auto-enroll user ke semua kursus premium saat berlangganan premium
     *
     * @param int $userId User ID
     * @return void
     */
    protected function autoEnrollPremiumCourses(int $userId): void
    {
        // Ambil semua kursus premium
        $premiumCourses = Course::where('access_type', 'premium')->get();
        
        foreach ($premiumCourses as $course) {
            // Cek apakah user sudah enrolled
            $alreadyEnrolled = Enrollment::where('user_id', $userId)
                ->where('course_id', $course->id)
                ->exists();
            
            // Jika belum enrolled, buat enrollment baru
            if (!$alreadyEnrolled) {
                Enrollment::create([
                    'user_id'   => $userId,
                    'course_id' => $course->id,
                    'progress'  => 0,
                    'completed' => false,
                ]);
            }
        }
        
        Log::info('Auto-enrolled user to premium courses via subscription upgrade', [
            'user_id' => $userId,
            'total_courses' => $premiumCourses->count(),
        ]);
    }
    
    /**
     * Validate that all course IDs exist in database
     *
     * @param array $courseIds Array of course IDs
     * @return bool
     */
    protected function validateCourseSelection(array $courseIds): bool
    {
        $courseCount = Course::whereIn('id', $courseIds)->count();
        return $courseCount === count($courseIds);
    }

    /**
     * Activate subscription after admin verifies payment
     * This method is called by admin after payment verification
     *
     * @param Subscription $subscription Subscription to activate
     * @return Subscription
     * @throws InvalidArgumentException
     */
    public function activateSubscription(Subscription $subscription): Subscription
    {
        try {
            DB::beginTransaction();

            // Validate subscription can be activated
            if ($subscription->status === 'active') {
                throw new InvalidArgumentException('Subscription is already active');
            }

            if ($subscription->status === 'expired') {
                throw new InvalidArgumentException('Cannot activate expired subscription');
            }

            if ($subscription->status === 'cancelled') {
                throw new InvalidArgumentException('Cannot activate cancelled subscription');
            }

            // Activate subscription
            $subscription->update([
                'status' => 'active',
            ]);

            // Auto-enroll user to courses based on subscription package
            if ($subscription->package_type === 'all_in_one') {
                $this->autoEnrollToAllCourses($subscription->user_id, $subscription->plan);
            } elseif ($subscription->package_type === 'single_course' && !empty($subscription->course_ids)) {
                $this->autoEnrollToSelectedCourses($subscription->user_id, $subscription->course_ids);
            }

            DB::commit();

            Log::info('Subscription activated successfully by admin', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'plan' => $subscription->plan,
                'package_type' => $subscription->package_type,
            ]);

            return $subscription->fresh();
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Subscription activation failed: validation error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription activation failed: unexpected error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to activate subscription. Please try again later.');
        }
    }
}
