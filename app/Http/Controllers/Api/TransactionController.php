<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\MentoringSession;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display user's transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'transactionable'])
            ->where('user_id', $request->user()->id);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($transactions);
    }

    /**
     * Create transaction for course enrollment
     */
    public function createCourseTransaction(Request $request, $courseId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ]);

        $course = Course::findOrFail($courseId);
        $user = $request->user();

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'progress' => 0,
                'completed' => false,
            ]);

            // Generate transaction code
            $transactionCode = Transaction::generateTransactionCode();

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => $transactionCode,
                'type' => 'course_enrollment',
                'transactionable_id' => $enrollment->id,
                'transactionable_type' => Enrollment::class,
                'amount' => $course->price,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'expired_at' => now()->addHours(24), // 24 hours to pay
            ]);

            // Generate Midtrans Snap token (skip for manual payment)
            $snapToken = null;
            $redirectUrl = null;

            if ($validated['payment_method'] !== 'manual') {
                $midtransParams = $this->midtransService->buildTransactionParams(
                    $transactionCode,
                    (int) $course->price,
                    [
                        [
                            'id' => $course->id,
                            'price' => (int) $course->price,
                            'quantity' => 1,
                            'name' => $course->title,
                        ]
                    ],
                    [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '08123456789',
                    ]
                );

                $snapResponse = $this->midtransService->createTransaction($midtransParams);

                if ($snapResponse['success']) {
                    $snapToken = $snapResponse['snap_token'];
                    $redirectUrl = $snapResponse['redirect_url'];

                    // Save snap token to payment_details
                    $transaction->payment_details = ['snap_token' => $snapToken];
                    $transaction->save();
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Failed to create payment',
                        'error' => $snapResponse['message']
                    ], 500);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaction created successfully',
                'data' => $transaction->load('transactionable'),
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create transaction for subscription
     */
    public function createSubscriptionTransaction(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:regular,premium',
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ]);

        $user = $request->user();

        // Calculate amount based on plan
        $amounts = [
            'regular' => 99000,
            'premium' => 199000,
        ];

        DB::beginTransaction();
        try {
            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan' => $validated['plan'],
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ]);

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => Transaction::generateTransactionCode(),
                'type' => 'subscription',
                'transactionable_id' => $subscription->id,
                'transactionable_type' => Subscription::class,
                'amount' => $amounts[$validated['plan']],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'expired_at' => now()->addHours(24),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Subscription transaction created successfully',
                'data' => $transaction->load('transactionable')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create transaction for mentoring session
     */
    public function createMentoringTransaction(Request $request, $sessionId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ]);

        $session = MentoringSession::findOrFail($sessionId);
        $user = $request->user();

        // Verify user is the session member
        if ($session->member_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to create transaction for this session'
            ], 403);
        }

        // Calculate amount based on session type
        $amounts = [
            'academic' => 150000,
            'life_plan' => 200000,
        ];

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => Transaction::generateTransactionCode(),
                'type' => 'mentoring_session',
                'transactionable_id' => $session->id,
                'transactionable_type' => MentoringSession::class,
                'amount' => $amounts[$session->type],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'expired_at' => now()->addHours(24),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Mentoring transaction created successfully',
                'data' => $transaction->load('transactionable')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified transaction
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'transactionable'])
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $transaction]);
    }

    /**
     * Upload payment proof for manual payment
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $transaction = Transaction::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $transaction->payment_proof = $path;
            $transaction->save();
        }

        return response()->json([
            'message' => 'Payment proof uploaded successfully',
            'data' => $transaction
        ]);
    }

    /**
     * Confirm payment (Admin only)
     */
    public function confirmPayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Update related model status if needed
        if ($transaction->transactionable_type === MentoringSession::class) {
            $transaction->transactionable->update(['status' => 'scheduled']);
        }

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'data' => $transaction
        ]);
    }

    /**
     * Request refund
     */
    public function requestRefund(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $transaction = Transaction::where('user_id', $request->user()->id)
            ->where('status', 'paid')
            ->findOrFail($id);

        $transaction->update([
            'status' => 'refunded',
            'payment_details' => array_merge(
                $transaction->payment_details ?? [],
                ['refund_reason' => $validated['reason'], 'refunded_at' => now()]
            ),
        ]);

        // Update related model status if needed
        if ($transaction->transactionable_type === MentoringSession::class) {
            $transaction->transactionable->update(['status' => 'refunded']);
        }

        return response()->json([
            'message' => 'Refund requested successfully',
            'data' => $transaction
        ]);
    }

    /**
     * Get transaction statistics (Admin only)
     */
    public function statistics()
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('status', 'paid')->sum('amount'),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'paid_transactions' => Transaction::where('status', 'paid')->count(),
            'failed_transactions' => Transaction::where('status', 'failed')->count(),
            'refunded_transactions' => Transaction::where('status', 'refunded')->count(),
            'by_type' => Transaction::select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->where('status', 'paid')
                ->groupBy('type')
                ->get(),
            'by_payment_method' => Transaction::select('payment_method', DB::raw('count(*) as count'))
                ->where('status', 'paid')
                ->groupBy('payment_method')
                ->get(),
        ];

        return response()->json(['data' => $stats]);
    }
}
