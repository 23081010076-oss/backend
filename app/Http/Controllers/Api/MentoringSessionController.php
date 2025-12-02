<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentoringSession;
use App\Services\MentoringService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Request Classes
use App\Http\Requests\Mentoring\StoreMentoringSessionRequest;
use App\Http\Requests\Mentoring\UpdateMentoringSessionRequest;
use App\Http\Requests\Mentoring\FeedbackRequest;

/**
 * ==========================================================================
 * MENTORING SESSION CONTROLLER (Controller untuk Sesi Mentoring)
 * ==========================================================================
 *
 * FUNGSI: Mengelola sesi mentoring (booking, jadwal, feedback).
 *
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/MentoringService.php
 * - Policy      : Authorization  → app/Policies/MentoringSessionPolicy.php
 * - Request     : Validation     → app/Http/Requests/Mentoring/
 */
class MentoringSessionController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected MentoringService $mentoringService;

    /**
     * Constructor - Inject service
     */
    public function __construct(MentoringService $mentoringService)
    {
        $this->mentoringService = $mentoringService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar sesi mentoring
     */
    public function index(Request $request): JsonResponse
    {
        $sessions = $this->mentoringService->getSessions(
            Auth::user(),
            $request->all()
        );

        return $this->paginatedResponse($sessions, 'Daftar sesi mentoring berhasil diambil');
    }

    /**
     * Tampilkan detail sesi mentoring
     */
    public function show(int $id): JsonResponse
    {
        $session = MentoringSession::with(['member', 'mentor'])->findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('view', $session);

        return $this->successResponse($session, 'Detail sesi mentoring berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Buat sesi mentoring baru
     *
     * Validasi di: app/Http/Requests/Mentoring/StoreMentoringSessionRequest.php
     */
    public function store(StoreMentoringSessionRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', MentoringSession::class);

        $session = $this->mentoringService->createSession(
            $request->validated(),
            Auth::user()
        );

        return $this->createdResponse($session, 'Sesi mentoring berhasil dibuat');
    }

    /**
     * Update sesi mentoring
     *
     * Validasi di: app/Http/Requests/Mentoring/UpdateMentoringSessionRequest.php
     */
    public function update(UpdateMentoringSessionRequest $request, int $id): JsonResponse
    {
        $session = MentoringSession::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $session);

        $session = $this->mentoringService->updateSession(
            $session,
            $request->validated()
        );

        return $this->successResponse($session, 'Sesi mentoring berhasil diupdate');
    }

    /**
     * Hapus sesi mentoring
     */
    public function destroy(int $id): JsonResponse
    {
        $session = MentoringSession::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $session);

        $session->delete();

        return $this->successResponse(null, 'Sesi mentoring berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | Status & Feedback Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update status sesi
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $session = MentoringSession::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('updateStatus', $session);

        $validated = $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled,refunded',
        ], [
            'status.required' => 'Status harus diisi',
            'status.in'       => 'Status harus salah satu dari: pending, scheduled, completed, cancelled, refunded',
        ]);

        $session = $this->mentoringService->updateStatus($session, $validated['status']);

        return $this->successResponse($session, 'Status sesi berhasil diupdate');
    }

    /**
     * Berikan feedback untuk sesi yang sudah selesai
     *
     * Validasi di: app/Http/Requests/Mentoring/FeedbackRequest.php
     */
    public function feedback(FeedbackRequest $request, int $id): JsonResponse
    {
        $session = MentoringSession::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('giveFeedback', $session);

        if ($session->status !== 'completed') {
            return $this->errorResponse('Feedback hanya bisa diberikan untuk sesi yang sudah selesai', 400);
        }

        $session = $this->mentoringService->giveFeedback($session, $request->validated());

        return $this->successResponse($session, 'Feedback berhasil dikirim');
    }


    public function mySessions(Request $request): JsonResponse
    {
        $sessions = $this->mentoringService->getSessions(
            Auth::user(),
            $request->all() // Tetap bawa filter lain (misal: status=pending)
        );

        return $this->paginatedResponse($sessions, 'Daftar sesi mentoring saya berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Schedule Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Lihat jadwal mentor tertentu
     */
    public function schedule(Request $request, int $mentorId): JsonResponse
    {
        $schedule = $this->mentoringService->getMentorSchedule(
            $mentorId,
            $request->input('from_date'),
            $request->input('to_date')
        );

        return $this->successResponse($schedule, 'Jadwal mentor berhasil diambil');
    }

    /**
     * Tampilkan sesi mentoring milik user yang sedang login
     */
    public function mySessions(Request $request): JsonResponse
    {
        $sessions = $this->mentoringService->getSessions(
            Auth::user(),
            $request->all()
        );

        return $this->paginatedResponse($sessions, 'Daftar sesi mentoring Anda berhasil diambil');
    }
}
