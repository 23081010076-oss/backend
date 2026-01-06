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
     * Display a listing of subscriptions
     * - Admin: Can see all subscriptions
     * - User: Can only see their own subscriptions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subscription::class);

        $user = $request->user();
        
        // Admin can see all subscriptions with user details
        if ($user->role === 'admin') {
            $subscriptions = Subscription::with('user:id,name,email')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            return $this->paginatedResponse($subscriptions, 'All subscriptions retrieved successfully');
        }
        
        // Regular users see only their own subscriptions
        $subscriptions = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($subscriptions, 'Subscriptions retrieved successfully');
    }

    /**
     * Get authenticated user's subscriptions
     *
     * @return JsonResponse
     */
    public function mySubscriptions(): JsonResponse
    {
        $user = auth()->user();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($subscriptions, 'Langganan Anda berhasil diambil');
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
            $result = $this->subscriptionService->createSubscription(
                $request->validated(),
                $request->user()
            );

            return $this->createdResponse($result, 'Langganan berhasil dibuat. Silakan upload bukti pembayaran dan tunggu konfirmasi admin untuk mengaktifkan langganan dan akses kursus.');
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

            return $this->successResponse($subscription, 'Upgrade berhasil dibuat. Silakan upload bukti pembayaran dan tunggu konfirmasi admin untuk mengaktifkan paket baru Anda.');
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
     * Check user's current subscription status
     * Returns active subscription info and available upgrade options
     *
     * @return JsonResponse
     */
    public function checkStatus(): JsonResponse
    {
        $user = auth()->user();
        
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        if (!$activeSubscription) {
            return $this->successResponse([
                'has_active_subscription' => false,
                'current_plan' => null,
                'can_upgrade' => true,
                'available_plans' => ['regular', 'premium'],
                'message' => 'Tidak ada paket aktif. Silakan pilih paket langganan.'
            ], 'No active subscription found');
        }

        // Determine available upgrades based on current plan
        $canUpgrade = false;
        $availablePlans = [];
        $message = '';

        if ($activeSubscription->plan === 'regular') {
            $canUpgrade = true;
            $availablePlans = ['premium'];
            $message = 'Anda dapat upgrade ke paket Premium untuk akses lebih banyak kursus.';
        } elseif ($activeSubscription->plan === 'premium') {
            $canUpgrade = false;
            $availablePlans = [];
            $message = 'Anda sudah berlangganan paket Premium (paket tertinggi).';
        }

        return $this->successResponse([
            'has_active_subscription' => true,
            'current_plan' => $activeSubscription->plan,
            'subscription' => [
                'id' => $activeSubscription->id,
                'plan' => $activeSubscription->plan,
                'status' => $activeSubscription->status,
                'start_date' => $activeSubscription->start_date,
                'end_date' => $activeSubscription->end_date,
                'package_type' => $activeSubscription->package_type,
            ],
            'can_upgrade' => $canUpgrade,
            'available_plans' => $availablePlans,
            'message' => $message
        ], 'Subscription status retrieved successfully');
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

    /**
     * Activate subscription after payment verification (Admin only)
     * 
     * @param int $id Subscription ID
     * @return JsonResponse
     */
    public function activate(int $id): JsonResponse
    {
        // Only admin can activate subscriptions
        $this->authorize('update', Subscription::findOrFail($id));

        $subscription = Subscription::findOrFail($id);

        try {
            $subscription = $this->subscriptionService->activateSubscription($subscription);

            return $this->successResponse(
                $subscription,
                'Subscription berhasil diaktifkan. User sekarang dapat mengakses kursus sesuai paket langganan.'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->validationErrorResponse(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Subscription activation failed in controller', [
                'subscription_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to activate subscription');
        }
    }
}
