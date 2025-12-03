<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="MentoringSession",
 *     title="Mentoring Session Schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="mentor_id", type="integer", example=7),
 *     @OA\Property(property="member_id", type="integer", example=4),
 *     @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic"),
 *     @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris"),
 *     @OA\Property(property="schedule", type="string", format="date-time", example="2025-12-20 10:00:00"),
 *     @OA\Property(property="status", type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled"}, example="pending"),
 *     @OA\Property(property="meeting_link", type="string", nullable=true, example="https://zoom.us/j/123456789"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions",
 *     summary="Get All Mentoring Sessions",
 *     description="Get all mentoring sessions for authenticated user",
 *     operationId="getAllMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by status",
 *         required=false,
 *         @OA\Schema(type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mentoring sessions retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions",
 *     summary="Create Mentoring Session",
 *     description="Create a new mentoring session booking",
 *     operationId="createMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Mentoring session data",
 *         @OA\JsonContent(
 *             required={"mentor_id", "type", "payment_method", "schedule", "status"},
 *             @OA\Property(property="mentor_id", type="integer", example=7, description="ID of the mentor"),
 *             @OA\Property(property="member_id", type="integer", example=4, description="ID of the member (optional, defaults to authenticated user)"),
 *             @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic", description="Type of mentoring session"),
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris", description="Payment method"),
 *             @OA\Property(property="schedule", type="string", format="date-time", example="2025-12-20 10:00:00", description="Scheduled date and time"),
 *             @OA\Property(property="status", type="string", enum={"pending"}, example="pending", description="Initial status (always pending)"),
 *             @OA\Property(property="note", type="string", example="Saya ingin konsultasi skripsi", description="Additional notes")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Mentoring session created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Mentoring session created successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Get Single Mentoring Session",
 *     description="Get details of a specific mentoring session",
 *     operationId="getSingleMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Mentoring session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mentoring session details retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Mentoring session not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Mentoring session not found")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{id}/schedule",
 *     summary="Schedule Mentoring Session",
 *     description="Endpoint for Mentor to set schedule and meeting link",
 *     operationId="scheduleMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Mentoring session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Schedule and meeting link data",
 *         @OA\JsonContent(
 *             required={"schedule", "meeting_link"},
 *             @OA\Property(
 *                 property="schedule",
 *                 type="string",
 *                 format="date-time",
 *                 example="2025-12-15 14:00:00",
 *                 description="Format: YYYY-MM-DD HH:MM:SS"
 *             ),
 *             @OA\Property(
 *                 property="meeting_link",
 *                 type="string",
 *                 example="https://zoom.us/j/123456789",
 *                 description="Zoom/Google Meet link"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Schedule updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Mentoring session scheduled successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized - User is not mentor or not owner of session",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Only mentors can schedule sessions")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Mentoring session not found"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{id}/status",
 *     summary="Update Session Status",
 *     description="Update mentoring session status (Mentor only)",
 *     operationId="updateSessionStatus",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Mentoring session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Status update data",
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={"pending", "scheduled", "ongoing", "completed", "cancelled"},
 *                 example="completed",
 *                 description="New status for the session"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Session status updated successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Only mentors can update status"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/my-mentoring-sessions",
 *     summary="Get My Mentoring Sessions",
 *     description="Get all mentoring sessions for the authenticated user (as mentor or member)",
 *     operationId="getMyMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="My mentoring sessions retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Delete Mentoring Session",
 *     description="Delete a mentoring session",
 *     operationId="deleteMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Mentoring session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Session deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Mentoring session deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Session not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Not authorized to delete this session"
 *     )
 * )
 */
class MentoringSwagger {}
