<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use App\Models\Enrollment;
use App\Http\Requests\StoreProgressReportRequest;
use App\Http\Requests\UpdateProgressReportFrequencyRequest;
use App\Http\Resources\ProgressReportResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressReportController extends Controller
{
    /**
     * List all progress reports for an enrollment or across all enrollments
     */
    public function index(Request $request)
    {
        $query = ProgressReport::query();

        // Filter by enrollment if provided
        if ($request->has('enrollment_id')) {
            $query->where('enrollment_id', $request->input('enrollment_id'));
        }

        // Filter by user if provided
        if ($request->has('user_id')) {
            $query->whereHas('enrollment', function ($q) {
                $q->where('user_id', request()->input('user_id'));
            });
        }

        // Order and paginate
        $reports = $query->orderBy('report_date', 'desc')
            ->with('enrollment')
            ->paginate(15);

        return response()->json([
            'message' => 'Progress reports retrieved successfully',
            'data' => ProgressReportResource::collection($reports),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->per_page(),
                'current_page' => $reports->current_page(),
                'last_page' => $reports->last_page(),
            ]
        ], 200);
    }

    /**
     * Get progress reports for specific enrollment
     */
    public function getByEnrollment($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $reports = $enrollment->progressReports()
            ->orderBy('report_date', 'desc')
            ->get();

        return response()->json([
            'message' => 'Progress reports for enrollment retrieved successfully',
            'enrollment' => [
                'id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
                'current_progress' => $enrollment->progress,
            ],
            'data' => ProgressReportResource::collection($reports),
            'count' => $reports->count()
        ], 200);
    }

    /**
     * Show specific progress report
     */
    public function show($reportId)
    {
        $report = ProgressReport::with('enrollment')->findOrFail($reportId);

        return response()->json([
            'message' => 'Progress report retrieved successfully',
            'data' => new ProgressReportResource($report)
        ], 200);
    }

    /**
     * Create/Submit a progress report
     */
    public function store(StoreProgressReportRequest $request)
    {
        $enrollment = Enrollment::findOrFail($request->validated()['enrollment_id']);

        // Check if report for today already exists
        $todayReport = ProgressReport::where('enrollment_id', $enrollment->id)
            ->whereDate('report_date', today())
            ->first();

        if ($todayReport) {
            return response()->json([
                'message' => 'Progress report already submitted today for this enrollment',
                'data' => new ProgressReportResource($todayReport)
            ], 409);
        }

        $validated = $request->validated();
        $frequency = $validated['frequency'] ?? $enrollment->report_frequency ?? 14;

        // Create report
        $report = ProgressReport::create([
            'enrollment_id' => $enrollment->id,
            'report_date' => $validated['report_date'] ?? today(),
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $validated['notes'],
            'attachment_url' => $validated['attachment_url'] ?? null,
            'frequency' => $frequency,
        ]);

        // Set next report date
        $report->setNextReportDate();

        // Update enrollment with progress info
        $enrollment->update([
            'progress' => $validated['progress_percentage'],
            'last_progress_report_date' => today(),
            'next_progress_report_date' => $report->next_report_date,
            'report_frequency' => $frequency,
        ]);

        return response()->json([
            'message' => 'Progress report submitted successfully',
            'data' => new ProgressReportResource($report)
        ], 201);
    }

    /**
     * Update progress report
     */
    public function update(StoreProgressReportRequest $request, $reportId)
    {
        $report = ProgressReport::findOrFail($reportId);

        $validated = $request->validated();
        $report->update([
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $validated['notes'],
            'attachment_url' => $validated['attachment_url'] ?? null,
        ]);

        // Update enrollment progress
        $report->enrollment->update(['progress' => $validated['progress_percentage']]);

        return response()->json([
            'message' => 'Progress report updated successfully',
            'data' => new ProgressReportResource($report)
        ], 200);
    }

    /**
     * Set report frequency for enrollment
     */
    public function setFrequency(UpdateProgressReportFrequencyRequest $request)
    {
        $enrollment = Enrollment::findOrFail($request->validated()['enrollment_id']);
        $frequency = $request->validated()['frequency'];

        $enrollment->update(['report_frequency' => $frequency]);

        // Update next report date for existing reports
        if ($enrollment->progressReports()->count() > 0) {
            $lastReport = $enrollment->progressReports()->latest('report_date')->first();
            $nextDate = Carbon::parse($lastReport->report_date)->addDays($frequency);
            $lastReport->update(['frequency' => $frequency, 'next_report_date' => $nextDate]);
        }

        return response()->json([
            'message' => 'Report frequency updated successfully',
            'data' => [
                'enrollment_id' => $enrollment->id,
                'frequency' => $frequency,
                'frequency_label' => 'Every ' . $frequency . ' days'
            ]
        ], 200);
    }

    /**
     * Get due reports (reports that need to be generated)
     */
    public function getDueReports()
    {
        $dueReports = ProgressReport::getDueReports();

        return response()->json([
            'message' => 'Due progress reports retrieved successfully',
            'data' => ProgressReportResource::collection($dueReports),
            'count' => $dueReports->count()
        ], 200);
    }

    /**
     * Delete progress report
     */
    public function destroy($reportId)
    {
        $report = ProgressReport::findOrFail($reportId);
        $report->delete();

        return response()->json([
            'message' => 'Progress report deleted successfully'
        ], 200);
    }
}

