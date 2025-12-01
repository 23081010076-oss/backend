<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ==========================================================================
 * SUBSCRIPTION CONTROLLER (Controller untuk Langganan)
 * ==========================================================================
 * 
 * FUNGSI: Menangani operasi CRUD untuk langganan
 * - Lihat daftar langganan user
 * - Tambah langganan baru
 * - Update langganan
 * - Upgrade paket langganan
 * - Hapus langganan
 * 
 * @package App\Http\Controllers\Api
 */
class SubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * Create a new controller instance
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of user's subscriptions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subscription::class);

        $subscriptions = Subscription::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($subscriptions, 'Subscriptions retrieved successfully');
    }

    /**
     * Store a newly created subscription
     *
     * @param StoreSubscriptionRequest $request
     * @return JsonResponse
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $this->authorize('create', Subscription::class);

        try {
            $subscription = $this->subscriptionService->createSubscription(
                $request->validated(),
                $request->user()
            );

            return $this->createdResponse($subscription, 'Subscription created successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->validationErrorResponse(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Subscription creation failed in controller', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to create subscription');
        }
    }

    /**
     * Display the specified subscription
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('view', $subscription);

        return $this->successResponse($subscription, 'Subscription retrieved successfully');
    }

    /**
     * Update the specified subscription
     *
     * @param UpdateSubscriptionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('update', $subscription);

        try {
            $subscription = $this->subscriptionService->updateSubscription(
                $subscription,
                $request->validated()
            );

            return $this->successResponse($subscription, 'Subscription updated successfully');
        } catch (\Exception $e) {
            Log::error('Subscription update failed in controller', [
                'subscription_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to update subscription');
        }
    }

    /**
     * Upgrade subscription to a higher plan
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function upgrade(Request $request, int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('upgrade', $subscription);

        $validated = $request->validate([
            'plan' => 'required|in:regular,premium',
        ]);

        try {
            $subscription = $this->subscriptionService->upgradeSubscription(
                $subscription,
                $validated['plan']
            );

            return $this->successResponse($subscription, 'Subscription upgraded successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->validationErrorResponse(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Subscription upgrade failed in controller', [
                'subscription_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to upgrade subscription');
        }
    }

    /**
     * Remove the specified subscription
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('delete', $subscription);

        try {
            $subscription->delete();
            return $this->successResponse(null, 'Subscription deleted successfully');
        } catch (\Exception $e) {
            Log::error('Subscription deletion failed in controller', [
                'subscription_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to delete subscription');
        }
    }
}
