<?php
// filepath: app/Swagger/NeedAssessmentSwagger.php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/mentoring-sessions/{sessionId}/need-assessments",
 *     summary="Get need assessment",
 *     description="Get need assessment for a mentoring session",
 *     operationId="getNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="mentoring_session_id", type="integer", example=1),
 *                 @OA\Property(property="form_data", type="object",
 *                     @OA\Property(property="current_situation", type="string", example="3rd year CS student struggling with career direction"),
 *                     @OA\Property(property="goals", type="string", example="Become a software engineer at top tech company"),
 *                     @OA\Property(property="challenges", type="array",
 *                         @OA\Items(type="string"),
 *                         example={"Lack of experience", "Interview preparation"}
 *                     ),
 *                     @OA\Property(property="expectations", type="string", example="Guidance on portfolio and interviews")
 *                 ),
 *                 @OA\Property(property="completed_at", type="string", format="datetime", nullable=true, example="2025-12-03T10:00:00.000000Z"),
 *                 @OA\Property(property="created_at", type="string", format="datetime", example="2025-12-02T10:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-12-03T10:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Need assessment not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Need assessment not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{sessionId}/need-assessments",
 *     summary="Submit need assessment",
 *     description="Create need assessment for a mentoring session (Student only)",
 *     operationId="createNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Need assessment form data",
 *         @OA\JsonContent(
 *             required={"form_data"},
 *             @OA\Property(property="form_data", type="object",
 *                 @OA\Property(property="current_situation", type="string", example="3rd year CS student struggling with career direction"),
 *                 @OA\Property(property="goals", type="string", example="Become a software engineer at top tech company"),
 *                 @OA\Property(property="challenges", type="array",
 *                     @OA\Items(type="string"),
 *                     example={"Lack of experience", "Interview preparation"}
 *                 ),
 *                 @OA\Property(property="expectations", type="string", example="Guidance on portfolio and interviews")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Need assessment created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Need assessment berhasil dibuat"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="mentoring_session_id", type="integer", example=1),
 *                 @OA\Property(property="form_data", type="object"),
 *                 @OA\Property(property="completed_at", type="string", nullable=true, example=null),
 *                 @OA\Property(property="created_at", type="string", format="datetime")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Assessment already exists or bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Assessment sudah ada untuk sesi ini")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Only students can create assessments",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Hanya student yang dapat membuat assessment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="form_data", type="array", @OA\Items(type="string", example="The form data field is required."))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{sessionId}/need-assessments",
 *     summary="Update need assessment",
 *     description="Update existing need assessment (Student only)",
 *     operationId="updateNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Updated assessment data",
 *         @OA\JsonContent(
 *             @OA\Property(property="form_data", type="object",
 *                 @OA\Property(property="current_situation", type="string", example="Updated current situation"),
 *                 @OA\Property(property="goals", type="string", example="Updated goals"),
 *                 @OA\Property(property="challenges", type="array",
 *                     @OA\Items(type="string"),
 *                     example={"New challenge 1", "New challenge 2"}
 *                 ),
 *                 @OA\Property(property="expectations", type="string", example="Updated expectations")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Need assessment berhasil diupdate"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Only students can update assessments",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Hanya student yang dapat mengupdate assessment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Assessment not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Assessment tidak ditemukan")
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{sessionId}/need-assessments/mark-completed",
 *     summary="Mark assessment as completed",
 *     description="Mark need assessment as completed (Mentor only)",
 *     operationId="markNeedAssessmentCompleted",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Assessment marked as completed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Assessment berhasil ditandai sebagai selesai"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="completed_at", type="string", format="datetime", example="2025-12-03T10:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Only mentors can mark as completed",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Hanya mentor yang dapat menandai assessment sebagai selesai")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Assessment not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Assessment tidak ditemukan")
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{sessionId}/need-assessments",
 *     summary="Delete need assessment",
 *     description="Delete need assessment",
 *     operationId="deleteNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Assessment berhasil dihapus")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Assessment not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Assessment tidak ditemukan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Not authorized to delete this assessment",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Anda tidak memiliki akses untuk menghapus assessment ini")
 *         )
 *     )
 * )
 */
class NeedAssessmentSwagger {}
