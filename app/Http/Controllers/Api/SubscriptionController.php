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
        $user = auth()->user();
        $subscription = Subscription::findOrFail($id);
        
        // Debug: Log untuk troubleshooting
        Log::info('Subscription upgrade attempt', [
            'logged_in_user_id' => $user->id,
            'logged_in_user_role' => $user->role,
            'subscription_id' => $id,
            'subscription_user_id' => $subscription->user_id,
        ]);
        
        // Manual authorization check with clear error message
        if ($user->role !== 'admin' && $user->id !== $subscription->user_id) {
            return $this->forbiddenResponse(
                'You can only upgrade your own subscription. ' .
                'Your ID: ' . $user->id . ', Subscription owner ID: ' . $subscription->user_id
            );
        }

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
     * Upgrade user's active subscription to a new plan (simplified version)
     * Automatically finds user's active subscription without requiring subscription ID
     *
     * @OA\Post(
     *     path="/api/subscription/upgrade",
     *     tags={"Subscriptions"},
     *     summary="Upgrade active subscription (simplified)",
     *     description="Automatically upgrade your currently active subscription to a new plan. Status will be 'pending' until admin/student activates it.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id"},
     *             @OA\Property(property="plan_id", type="integer", example=2, description="ID of new plan to upgrade to")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Upgrade successful, awaiting activation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Upgrade berhasil dibuat. Silakan upload bukti pembayaran dan tunggu konfirmasi admin untuk mengaktifkan paket baru Anda."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="plan_id", type="integer"),
     *                 @OA\Property(property="plan", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="start_date", type="string", format="date"),
     *                 @OA\Property(property="end_date", type="string", format="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No active subscription found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No active subscription found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function upgradeMySubscription(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Find user's active subscription automatically
            $activeSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();
            
            if (!$activeSubscription) {
                return $this->errorResponse(
                    'No active subscription found. Please subscribe to a plan first.',
                    null,
                    404
                );
            }
            
            // Validate plan_id
            $request->validate([
                'plan_id' => 'required|integer|exists:plans,id'
            ]);
            
            Log::info('User upgrading their active subscription', [
                'user_id' => $user->id,
                'active_subscription_id' => $activeSubscription->id,
                'current_plan' => $activeSubscription->plan,
                'new_plan_id' => $request->plan_id
            ]);
            
            // Use existing upgrade service method
            $subscription = $this->subscriptionService->upgradeSubscription(
                $activeSubscription->id,
                $request->plan_id
            );

            return $this->successResponse(
                $subscription, 
                'Upgrade berhasil dibuat. Silakan upload bukti pembayaran dan tunggu konfirmasi admin untuk mengaktifkan paket baru Anda.'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->validationErrorResponse(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Subscription upgrade failed', [
                'user_id' => auth()->id(),
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
        
        // Check for active subscription only (pending means waiting for admin activation)
        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        // Check if there's a pending subscription (waiting for activation)
        $pendingSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$activeSubscription) {
            // If there's a pending subscription, inform user to wait for activation
            if ($pendingSubscription) {
                return $this->successResponse([
                    'has_active_subscription' => false,
                    'has_pending_subscription' => true,
                    'pending_plan' => $pendingSubscription->plan,
                    'current_plan' => null,
                    'can_upgrade' => false,
                    'available_plans' => [],
                    'message' => 'Anda memiliki paket ' . ucfirst($pendingSubscription->plan) . ' yang menunggu verifikasi pembayaran oleh admin. Silakan tunggu aktivasi atau hubungi admin.'
                ], 'Pending subscription awaiting activation');
            }
            
            return $this->successResponse([
                'has_active_subscription' => false,
                'has_pending_subscription' => false,
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
    public function activate($id): JsonResponse
    {
        $user = auth()->user();
        $subscription = Subscription::findOrFail($id);
        
        // Student can activate their own subscription, Admin can activate any
        if ($user->role !== 'admin' && $user->id !== $subscription->user_id) {
            return $this->forbiddenResponse('You can only activate your own subscription');
        }

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
