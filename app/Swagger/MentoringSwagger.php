<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 * schema="MentoringSession",
 * title="Mentoring Session Schema",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="mentor_id", type="integer", example=4),
 * @OA\Property(property="member_id", type="integer", example=5),
 * @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic"),
 * @OA\Property(property="schedule", type="string", format="date-time", example="2024-02-20 10:00:00"),
 * @OA\Property(property="status", type="string", enum={"pending", "scheduled", "completed", "cancelled"}, example="scheduled"),
 * @OA\Property(property="meeting_link", type="string", example="https://meet.google.com/abc-defg-hij")
 * )
 *
 * @OA\Get(
 * path="/api/mentoring-sessions",
 * summary="List all sessions (Admin/General)",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="status",
 * in="query",
 * description="Filter by status",
 * required=false,
 * @OA\Schema(type="string", enum={"pending", "scheduled", "completed", "cancelled"})
 * ),
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 * )
 * )
 * )
 *
 * @OA\Get(
 * path="/api/my-mentoring-sessions",
 * summary="List MY sessions (Logged in User)",
 * description="Get sessions specific to the authenticated user",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="status",
 * in="query",
 * required=false,
 * @OA\Schema(type="string", enum={"pending", "scheduled", "completed", "cancelled"})
 * ),
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 * )
 * )
 * )
 *
 * @OA\Post(
 * path="/api/mentoring-sessions",
 * summary="Book a new session",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"mentor_id","type","schedule"},
 * @OA\Property(property="mentor_id", type="integer", example=4),
 * @OA\Property(property="type", type="string", enum={"academic", "life_plan"}),
 * @OA\Property(property="schedule", type="string", format="date-time", example="2024-02-20 10:00:00"),
 * @OA\Property(property="note", type="string", example="Saya ingin konsultasi skripsi")
 * )
 * ),
 * @OA\Response(response=201, description="Created")
 * )
 *
 * @OA\Get(
 * path="/api/mentoring-sessions/{id}",
 * summary="Get session detail",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(
 * response=200,
 * description="Detail retrieved",
 * @OA\JsonContent(ref="#/components/schemas/MentoringSession")
 * )
 * )
 *
 * @OA\Put(
 * path="/api/mentoring-sessions/{id}",
 * summary="Update session detail",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * @OA\JsonContent(
 * @OA\Property(property="schedule", type="string", format="date-time"),
 * @OA\Property(property="meeting_link", type="string")
 * )
 * ),
 * @OA\Response(response=200, description="Updated")
 * )
 *
 * @OA\Put(
 * path="/api/mentoring-sessions/{id}/status",
 * summary="Update session status",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"status"},
 * @OA\Property(property="status", type="string", enum={"scheduled", "completed", "cancelled"})
 * )
 * ),
 * @OA\Response(response=200, description="Status updated")
 * )
 *
 * @OA\Post(
 * path="/api/mentoring-sessions/{id}/feedback",
 * summary="Give feedback",
 * description="Can only be done if status is completed",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"rating", "review"},
 * @OA\Property(property="rating", type="integer", example=5, minimum=1, maximum=5),
 * @OA\Property(property="review", type="string", example="Mentor sangat ramah")
 * )
 * ),
 * @OA\Response(response=200, description="Feedback submitted")
 * )
  * @OA\Delete(
 * path="/api/mentoring-sessions/{id}",
 * summary="Delete session",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Deleted")
 * )
 *
 * @OA\Get(
 * path="/api/mentors/{id}/schedule",
 * summary="Check mentor availability",
 * tags={"Mentoring"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, description="ID of the Mentor", @OA\Schema(type="integer")),
 * @OA\Parameter(name="from_date", in="query", @OA\Schema(type="string", format="date")),
 * @OA\Parameter(name="to_date", in="query", @OA\Schema(type="string", format="date")),
 * @OA\Response(
 * response=200,
 * description="Schedule retrieved",
 * @OA\JsonContent(
 * @OA\Property(property="available_slots", type="array", @OA\Items(type="string", format="datetime"))
 * )
 * )
 * )
 */
class MentoringSwagger {}
