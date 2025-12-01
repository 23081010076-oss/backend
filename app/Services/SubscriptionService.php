<?php

namespace App\Services;

use App\Models\Course;
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
    /**
     * Create a new subscription for a user
     *
     * @param array $data Subscription data
     * @param User $user User who is subscribing
     * @return Subscription
     * @throws InvalidArgumentException
     */
    public function createSubscription(array $data, User $user): Subscription
    {
        try {
            DB::beginTransaction();

            $data['user_id'] = $user->id;
            $data['status'] = 'active'; // New subscriptions always start as active
            
            // Validate course selection for single_course package
            if ($data['package_type'] === 'single_course') {
                $this->validateSingleCoursePackage($data);
            }
            
            $subscription = Subscription::create($data);

            DB::commit();

            Log::info('Subscription created successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan' => $subscription->plan,
                'package_type' => $subscription->package_type,
            ]);

            return $subscription;
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
            
            $subscription->update([
                'plan' => $plan,
                'status' => 'active',
                'end_date' => $newEndDate,
            ]);

            DB::commit();

            Log::info('Subscription upgraded successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'old_plan' => $subscription->getOriginal('plan'),
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
            throw new InvalidArgumentException('Cannot upgrade an expired subscription. Please create a new subscription.');
        }
        
        // Validate upgrade path - no downgrades
        if ($subscription->plan === 'premium' && $newPlan !== 'premium') {
            throw new InvalidArgumentException('Cannot downgrade from premium plan');
        }
        
        if ($subscription->plan === 'regular' && $newPlan === 'free') {
            throw new InvalidArgumentException('Cannot downgrade from regular to free plan');
        }

        // Prevent "upgrading" to the same plan
        if ($subscription->plan === $newPlan) {
            throw new InvalidArgumentException('Subscription is already on the ' . $newPlan . ' plan');
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
}
