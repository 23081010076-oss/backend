<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/need-assessments",
 *     summary="Get need assessment",
 *     description="Get need assessment for a mentoring session",
 *     operationId="getNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="mentoring_session_id", type="integer", example=1),
 *                 @OA\Property(property="form_data", type="object",
 *                     @OA\Property(property="learning_goals", type="array", @OA\Items(type="string")),
 *                     @OA\Property(property="previous_experience", type="string"),
 *                     @OA\Property(property="challenges", type="array", @OA\Items(type="string")),
 *                     @OA\Property(property="expectations", type="string"),
 *                     @OA\Property(property="preferred_schedule", type="string"),
 *                     @OA\Property(property="communication_style", type="string")
 *                 ),
 *                 @OA\Property(property="completed_at", type="string", format="datetime", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Need assessment not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/need-assessments",
 *     summary="Create need assessment",
 *     description="Create need assessment for a mentoring session",
 *     operationId="createNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="learning_goals", type="array",
 *                 @OA\Items(type="string"),
 *                 example={"Master React.js", "Learn backend development", "Build full-stack project"}
 *             ),
 *             @OA\Property(property="previous_experience", type="string", example="Basic knowledge of HTML, CSS, JavaScript"),
 *             @OA\Property(property="challenges", type="array",
 *                 @OA\Items(type="string"),
 *                 example={"Time management", "Understanding async programming"}
 *             ),
 *             @OA\Property(property="expectations", type="string", example="Want to become job-ready full-stack developer"),
 *             @OA\Property(property="preferred_schedule", type="string", enum={"morning", "afternoon", "evening"}, example="evening"),
 *             @OA\Property(property="communication_style", type="string", enum={"formal", "casual", "structured"}, example="casual")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Need assessment created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Assessment already exists"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/need-assessments",
 *     summary="Update need assessment",
 *     description="Update existing need assessment",
 *     operationId="updateNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="learning_goals", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="previous_experience", type="string"),
 *             @OA\Property(property="challenges", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="expectations", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment updated"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/need-assessments/mark-completed",
 *     summary="Mark assessment as completed",
 *     description="Mark need assessment as completed",
 *     operationId="markNeedAssessmentCompleted",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Assessment marked as completed"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/need-assessments",
 *     summary="Delete need assessment",
 *     description="Delete need assessment",
 *     operationId="deleteNeedAssessment",
 *     tags={"Need Assessments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Need assessment deleted"
 *     )
 * )
 */
class NeedAssessmentSwagger {}
