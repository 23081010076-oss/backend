<?php

namespace App\Http\Controllers;

use App\Models\CoachingFile;
use App\Models\MentoringSession;
use App\Http\Requests\StoreCoachingFileRequest;
use App\Http\Resources\CoachingFileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoachingFileController extends Controller
{
    /**
     * List all coaching files for a mentoring session
     */
    public function index($mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $files = $mentoringSession->coachingFiles()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Coaching files retrieved successfully',
            'data' => CoachingFileResource::collection($files),
            'count' => $files->count()
        ], 200);
    }

    /**
     * Upload/Store a coaching file
     */
    public function store(StoreCoachingFileRequest $request, $mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);

        // Validate file upload
        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file provided',
                'errors' => ['file' => ['File is required']]
            ], 422);
        }

        $file = $request->file('file');
        $fileName = $request->input('file_name');
        $fileType = $request->input('file_type');

        // Store file to storage/coaching-files
        $filePath = $file->storeAs('coaching-files', $fileName, 'public');

        // Create coaching file record
        $coachingFile = CoachingFile::create([
            'mentoring_session_id' => $mentoringSessionId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'uploaded_by' => $request->input('uploaded_by'),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => new CoachingFileResource($coachingFile)
        ], 201);
    }

    /**
     * Get specific coaching file details
     */
    public function show($mentoringSessionId, $fileId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $file = $mentoringSession->coachingFiles()->findOrFail($fileId);

        return response()->json([
            'message' => 'File retrieved successfully',
            'data' => new CoachingFileResource($file)
        ], 200);
    }

    /**
     * Download coaching file
     */
    public function download($mentoringSessionId, $fileId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $file = $mentoringSession->coachingFiles()->findOrFail($fileId);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json([
                'message' => 'File not found on storage'
            ], 404);
        }

        return response()->file(storage_path('app/public/' . $file->file_path));
    }

    /**
     * Delete coaching file
     */
    public function destroy($mentoringSessionId, $fileId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $file = $mentoringSession->coachingFiles()->findOrFail($fileId);

        // Delete from storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Delete database record
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully'
        ], 200);
    }

    /**
     * Delete all coaching files for a mentoring session
     */
    public function destroyAll($mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $files = $mentoringSession->coachingFiles;

        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        return response()->json([
            'message' => 'All files deleted successfully',
            'deleted_count' => $files->count()
        ], 200);
    }
}

