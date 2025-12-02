<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachingFile;
use App\Models\MentoringSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

/**
 * ==========================================================================
 * COACHING FILE CONTROLLER (Controller untuk File Coaching/Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Menangani operasi untuk file coaching dalam sesi mentoring
 * - Lihat daftar file dalam sesi mentoring
 * - Upload file baru
 * - Download file
 * - Hapus file
 * 
 * NOTE: Semua endpoint menggunakan nested route:
 *       /mentoring-sessions/{mentoringSessionId}/coaching-files
 */
class CoachingFileController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * INDEX - Daftar File Coaching
     * --------------------------------------------------------------------------
     * GET /mentoring-sessions/{mentoringSessionId}/coaching-files
     * 
     * Menampilkan semua file dalam sesi mentoring tertentu.
     * User harus terlibat dalam sesi (sebagai student atau mentor).
     */
    public function index(int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya member atau mentor yang terlibat
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

        $files = CoachingFile::where('mentoring_session_id', $mentoringSessionId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Daftar file coaching berhasil diambil',
            'data'   => [
                'files' => $files->map(fn($file) => [
                    'id'           => $file->id,
                    'file_name'    => $file->file_name,
                    'file_path'    => $file->file_path,
                    'file_type'    => $file->file_type,
                    'file_url'     => $file->file_url,
                    'uploaded_by'  => $file->uploaded_by,
                    'created_at'   => $file->created_at,
                ]),
                'total' => $files->count()
            ]
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * STORE - Upload File Coaching
     * --------------------------------------------------------------------------
     * POST /mentoring-sessions/{mentoringSessionId}/coaching-files
     * 
     * Upload file baru ke sesi mentoring.
     * Hanya user yang terlibat dalam sesi yang bisa upload.
     * 
     * @bodyParam file file required File yang akan diupload (max 10MB)
     * @bodyParam file_name string optional Nama file custom
     */
    public function store(Request $request, int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses
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

        // Validasi
        $validated = $request->validate([
            'file' => [
                'required',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'zip'])
                    ->max(10 * 1024), // 10MB
            ],
            'file_name' => 'nullable|string|max:255',
        ], [
            'file.required' => 'File wajib diupload',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension    = $uploadedFile->getClientOriginalExtension();
        $fileName     = $validated['file_name'] ?? pathinfo($originalName, PATHINFO_FILENAME);
        
        // Generate unique filename
        $storedName = time() . '_' . uniqid() . '.' . $extension;
        
        // Store file
        $filePath = $uploadedFile->storeAs(
            'coaching-files/' . $mentoringSessionId,
            $storedName,
            'public'
        );

        // Create record
        $coachingFile = CoachingFile::create([
            'mentoring_session_id' => $mentoringSessionId,
            'file_name'            => $fileName,
            'file_path'            => $filePath,
            'file_type'            => $extension,
            'uploaded_by'          => $user->id,
        ]);

        return response()->json([
            'sukses' => true,
            'pesan'  => 'File berhasil diupload',
            'data'   => [
                'id'           => $coachingFile->id,
                'file_name'    => $coachingFile->file_name,
                'file_path'    => $coachingFile->file_path,
                'file_type'    => $coachingFile->file_type,
                'file_url'     => $coachingFile->file_url,
                'uploaded_by'  => $coachingFile->uploaded_by,
                'created_at'   => $coachingFile->created_at,
            ]
        ], 201);
    }

    /**
     * --------------------------------------------------------------------------
     * SHOW - Detail File Coaching
     * --------------------------------------------------------------------------
     * GET /mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}
     * 
     * Menampilkan detail file tertentu.
     */
    public function show(int $mentoringSessionId, int $fileId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses
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

        $file = CoachingFile::where('mentoring_session_id', $mentoringSessionId)
            ->where('id', $fileId)
            ->firstOrFail();

        return response()->json([
            'sukses' => true,
            'pesan'  => 'Detail file berhasil diambil',
            'data'   => [
                'id'           => $file->id,
                'file_name'    => $file->file_name,
                'file_path'    => $file->file_path,
                'file_type'    => $file->file_type,
                'file_url'     => $file->file_url,
                'uploaded_by'  => $file->uploaded_by,
                'download_name'=> $file->download_name,
                'created_at'   => $file->created_at,
                'updated_at'   => $file->updated_at,
            ]
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * DOWNLOAD - Download File Coaching
     * --------------------------------------------------------------------------
     * GET /mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}/download
     * 
     * Download file coaching.
     */
    public function download(int $mentoringSessionId, int $fileId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses
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

        $file = CoachingFile::where('mentoring_session_id', $mentoringSessionId)
            ->where('id', $fileId)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'File tidak ditemukan di storage',
                'data'   => null
            ], 404);
        }

        return Storage::disk('public')->download(
            $file->file_path,
            $file->download_name
        );
    }

    /**
     * --------------------------------------------------------------------------
     * DESTROY - Hapus File Coaching
     * --------------------------------------------------------------------------
     * DELETE /mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}
     * 
     * Hapus file coaching. Hanya uploader atau admin yang bisa hapus.
     */
    public function destroy(int $mentoringSessionId, int $fileId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        $file = CoachingFile::where('mentoring_session_id', $mentoringSessionId)
            ->where('id', $fileId)
            ->firstOrFail();
        
        // Cek akses - hanya uploader atau admin
        $user = Auth::user();
        if ($user->role !== 'admin' && $file->uploaded_by !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Anda tidak memiliki akses untuk menghapus file ini',
                'data'   => null
            ], 403);
        }

        // Hapus dari storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Hapus record
        $file->delete();

        return response()->json([
            'sukses' => true,
            'pesan'  => 'File berhasil dihapus',
            'data'   => null
        ], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * DESTROY ALL - Hapus Semua File Coaching
     * --------------------------------------------------------------------------
     * DELETE /mentoring-sessions/{mentoringSessionId}/coaching-files
     * 
     * Hapus semua file dalam sesi mentoring. Hanya admin atau mentor yang bisa.
     */
    public function destroyAll(int $mentoringSessionId): JsonResponse
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        
        // Cek akses - hanya admin atau mentor sesi ini
        $user = Auth::user();
        if ($user->role !== 'admin' && $mentoringSession->mentor_id !== $user->id) {
            return response()->json([
                'sukses' => false,
                'pesan'  => 'Anda tidak memiliki akses untuk menghapus semua file',
                'data'   => null
            ], 403);
        }

        $files = CoachingFile::where('mentoring_session_id', $mentoringSessionId)->get();
        $deletedCount = $files->count();

        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        return response()->json([
            'sukses' => true,
            'pesan'  => "Berhasil menghapus {$deletedCount} file",
            'data'   => [
                'deleted_count' => $deletedCount
            ]
        ], 200);
    }
}
