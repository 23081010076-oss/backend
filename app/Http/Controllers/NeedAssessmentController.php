<?php

namespace App\Http\Controllers;

use App\Models\NeedAssessment;
use App\Models\MentoringSession;
use App\Http\Requests\StoreNeedAssessmentRequest;
use App\Http\Resources\NeedAssessmentResource;
use Illuminate\Http\Response;

class NeedAssessmentController extends Controller
{
    /**
     * Get need assessment for a specific mentoring session
     */
    public function show($mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $assessment = $mentoringSession->needAssessment;

        if (!$assessment) {
            return response()->json([
                'message' => 'No assessment found for this mentoring session',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Assessment retrieved successfully',
            'data' => new NeedAssessmentResource($assessment)
        ], 200);
    }

    /**
     * Store/Submit a need assessment for a mentoring session
     */
    public function store(StoreNeedAssessmentRequest $request, $mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);

        // Check if assessment already exists
        if ($mentoringSession->needAssessment) {
            return response()->json([
                'message' => 'Assessment already exists for this mentoring session',
                'data' => new NeedAssessmentResource($mentoringSession->needAssessment)
            ], 409);
        }

        $assessment = NeedAssessment::create([
            'mentoring_session_id' => $mentoringSessionId,
            'form_data' => $request->validated()['form_data'],
        ]);

        // Update mentoring session assessment status
        $mentoringSession->update(['need_assessment_status' => 'completed']);

        return response()->json([
            'message' => 'Assessment submitted successfully',
            'data' => new NeedAssessmentResource($assessment)
        ], 201);
    }

    /**
     * Mark assessment as completed
     */
    public function markCompleted($mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $assessment = $mentoringSession->needAssessment;

        if (!$assessment) {
            return response()->json([
                'message' => 'No assessment found for this mentoring session'
            ], 404);
        }

        $assessment->markCompleted();
        $mentoringSession->update(['need_assessment_status' => 'completed']);

        return response()->json([
            'message' => 'Assessment marked as completed',
            'data' => new NeedAssessmentResource($assessment)
        ], 200);
    }

    /**
     * Delete assessment
     */
    public function destroy($mentoringSessionId)
    {
        $mentoringSession = MentoringSession::findOrFail($mentoringSessionId);
        $assessment = $mentoringSession->needAssessment;

        if (!$assessment) {
            return response()->json([
                'message' => 'No assessment found for this mentoring session'
            ], 404);
        }

        $assessment->delete();
        $mentoringSession->update(['need_assessment_status' => 'pending']);

        return response()->json([
            'message' => 'Assessment deleted successfully'
        ], 200);
    }
}

