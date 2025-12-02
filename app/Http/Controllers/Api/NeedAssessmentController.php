<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NeedAssessment;
use App\Models\MentoringSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * ==========================================================================
 * NEED ASSESSMENT CONTROLLER (Controller untuk Penilaian Kebutuhan)
 * ==========================================================================
 * 
 * FUNGSI: Menangani operasi untuk need assessment dalam sesi mentoring
 * - Lihat assessment
 * - Submit assessment
 * - Update assessment
 * - Tandai selesai
 * - Hapus assessment
 * 
 * NOTE: Semua endpoint menggunakan nested route:
 *       /mentoring-sessions/{mentoringSessionId}/need-assessments
 * 
 * FLOW:
 * 1. Student membuat sesi mentoring
 * 2. Student mengisi need assessment (form_data)
 * 3. Mentor review assessment
 * 4. Assessment ditandai completed setelah sesi selesai
 */
class NeedAssessmentController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * SHOW - Lihat Need Assessment
     * --------------------------------------------------------------------------
     * GET /mentoring-sessions/{mentoringSessionId}/need-assessments
     * 
     * Menampilkan need assessment untuk sesi mentoring tertentu.
     * User harus terlibat dalam sesi (sebagai student atau mentor).
     */
    public function show(int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya member, mentor terkait, atau admin
        $user = Auth::user();
        if ($user->role !== 'admin' && 
            $mentoringSession->member_id !== $user->id && 
            $mentoringSession->mentor_id !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Anda tidak memiliki akses ke sesi ini',
                'data'   => null
            ], 403);
        }

        $assessment = NeedAssessment::where('mentoring_session_id', $mentoringSessionId)->first();

        if (!$assessment) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Need assessment belum diisi untuk sesi ini',
                'data'   => null
            ], 404);
        }

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Need assessment berhasil diambil',
            'data'   => [
                'id'                   => $assessment->id,
                'mentoring_session_id' => $assessment->mentoring_session_id,
                'form_data'            => $assessment->form_data,
                'is_completed'         => $assessment->isCompleted(),
                'completed_at'         => $assessment->completed_at,
                'created_at'           => $assessment->created_at,
                'updated_at'           => $assessment->updated_at,
            ]
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * STORE - Submit Need Assessment
     * --------------------------------------------------------------------------
     * POST /mentoring-sessions/{mentoringSessionId}/need-assessments
     * 
     * Submit need assessment baru untuk sesi mentoring.
     * Hanya student yang terlibat dalam sesi yang bisa submit.
     * 
     * @bodyParam form_data object required Data assessment dalam format JSON
     * 
     * Contoh form_data:
     * {
     *   "goals": "Ingin belajar public speaking",
     *   "current_level": "beginner",
     *   "challenges": ["Gugup di depan umum", "Sulit mengorganisir ide"],
     *   "expectations": "Bisa presentasi dengan percaya diri",
     *   "preferred_schedule": "weekend",
     *   "additional_notes": "Fokus ke presentasi bisnis"
     * }
     */
    public function store(Request $request, int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya member terkait atau admin
        $user = Auth::user();
        if ($user->role !== 'admin' && $mentoringSession->member_id !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Hanya member yang terlibat yang bisa mengisi assessment',
                'data'   => null
            ], 403);
        }

        // Cek apakah sudah ada assessment
        $existingAssessment = NeedAssessment::where('mentoring_session_id', $mentoringSessionId)->first();
        if ($existingAssessment) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment sudah ada untuk sesi ini. Gunakan endpoint update.',
                'data'   => [
                    'id'                   => $existingAssessment->id,
                    'mentoring_session_id' => $existingAssessment->mentoring_session_id,
                    'form_data'            => $existingAssessment->form_data,
                ]
            ], 409);
        }

        // Validasi
        $validated = $request->validate([
            'form_data' => 'required|array',
        ], [
            'form_data.required' => 'Data assessment wajib diisi',
            'form_data.array'    => 'Format data assessment tidak valid',
        ]);

        // Create assessment
        $assessment = NeedAssessment::create([
            'mentoring_session_id' => $mentoringSessionId,
            'form_data'            => $validated['form_data'],
        ]);

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Need assessment berhasil disimpan',
            'data'   => [
                'id'                   => $assessment->id,
                'mentoring_session_id' => $assessment->mentoring_session_id,
                'form_data'            => $assessment->form_data,
                'is_completed'         => $assessment->isCompleted(),
                'completed_at'         => $assessment->completed_at,
                'created_at'           => $assessment->created_at,
            ]
        ], 201);
    }

    /**
     * --------------------------------------------------------------------------
     * UPDATE - Update Need Assessment
     * --------------------------------------------------------------------------
     * PUT /mentoring-sessions/{mentoringSessionId}/need-assessments
     * 
     * Update need assessment yang sudah ada.
     * Tidak bisa update jika sudah completed.
     */
    public function update(Request $request, int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya member terkait atau admin
        $user = Auth::user();
        if ($user->role !== 'admin' && $mentoringSession->member_id !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Hanya member yang terlibat yang bisa mengupdate assessment',
                'data'   => null
            ], 403);
        }

        $assessment = NeedAssessment::where('mentoring_session_id', $mentoringSessionId)->first();
        
        if (!$assessment) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment tidak ditemukan',
                'data'   => null
            ], 404);
        }

        // Cek apakah sudah completed
        if ($assessment->isCompleted()) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment yang sudah completed tidak bisa diubah',
                'data'   => null
            ], 422);
        }

        // Validasi
        $validated = $request->validate([
            'form_data' => 'required|array',
        ]);

        // Update
        $assessment->update([
            'form_data' => $validated['form_data'],
        ]);

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Need assessment berhasil diupdate',
            'data'   => [
                'id'                   => $assessment->id,
                'mentoring_session_id' => $assessment->mentoring_session_id,
                'form_data'            => $assessment->form_data,
                'is_completed'         => $assessment->isCompleted(),
                'updated_at'           => $assessment->updated_at,
            ]
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * MARK COMPLETED - Tandai Assessment Selesai
     * --------------------------------------------------------------------------
     * PUT /mentoring-sessions/{mentoringSessionId}/need-assessments/mark-completed
     * 
     * Tandai need assessment sebagai completed.
     * Hanya mentor atau admin yang bisa menandai.
     */
    public function markCompleted(int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya mentor terkait atau admin
        $user = Auth::user();
        if ($user->role !== 'admin' && $mentoringSession->mentor_id !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Hanya mentor yang bisa menandai assessment selesai',
                'data'   => null
            ], 403);
        }

        $assessment = NeedAssessment::where('mentoring_session_id', $mentoringSessionId)->first();
        
        if (!$assessment) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment tidak ditemukan',
                'data'   => null
            ], 404);
        }

        if ($assessment->isCompleted()) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment sudah ditandai completed sebelumnya',
                'data'   => null
            ], 422);
        }

        // Mark as completed
        $assessment->markCompleted();

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Assessment berhasil ditandai completed',
            'data'   => [
                'id'                   => $assessment->id,
                'mentoring_session_id' => $assessment->mentoring_session_id,
                'is_completed'         => true,
                'completed_at'         => $assessment->completed_at,
            ]
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * DESTROY - Hapus Need Assessment
     * --------------------------------------------------------------------------
     * DELETE /mentoring-sessions/{mentoringSessionId}/need-assessments
     * 
     * Hapus need assessment.
     * Hanya admin yang bisa menghapus, atau student jika belum completed.
     */
    public function destroy(int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        $assessment = NeedAssessment::where('mentoring_session_id', $mentoringSessionId)->first();
        
        if (!$assessment) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Assessment tidak ditemukan',
                'data'   => null
            ], 404);
        }

        $user = Auth::user();
        
        // Admin bisa hapus kapan saja
        // Member hanya bisa hapus jika belum completed
        if ($user->role !== 'admin') {
            if ($mentoringSession->member_id !== $user->id) {
                return response()->json([
                    'sukses' => false,
                    'pesan'  => 'Anda tidak memiliki akses untuk menghapus assessment ini',
                    'data'   => null
                ], 403);
            }

            if ($assessment->isCompleted()) {
                return response()->json([
                    'sukses' => false,
                    'pesan'  => 'Assessment yang sudah completed tidak bisa dihapus',
                    'data'   => null
                ], 422);
            }
        }

        $assessment->delete();

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Assessment berhasil dihapus',
            'data'   => null
        ], 200);
    }
}
