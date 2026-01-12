<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="CoachingFile",
 *     title="Coaching File",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="session_id", type="integer", example=10),
 *     @OA\Property(property="file_name", type="string", example="react-guide.pdf"),
 *     @OA\Property(property="file_path", type="string", example="uploads/coaching/react-guide.pdf"),
 *     @OA\Property(property="created_at", type="string", format="datetime")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files",
 *     summary="Get Coaching Files",
 *     description="Get all coaching files for a mentoring session",
 *     operationId="getCoachingFiles",
 *     tags={"Coaching Files"},
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
 *         description="List of coaching files",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CoachingFile"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=404, description="Session not found")
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files",
 *     summary="Upload Coaching File",
 *     description="Upload a new coaching file (Mentor Only)",
 *     operationId="uploadCoachingFile",
 *     tags={"Coaching Files"},
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
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"file", "file_name"},
 *                 @OA\Property(property="file", type="string", format="binary", description="File (required)"),
 *                 @OA\Property(property="file_name", type="string", description="Display name for the file", example="Materi Pertemuan 1")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="File uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File uploaded successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/CoachingFile")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Only mentors can upload files"),
 *     @OA\Response(response=422, description="Validation Error")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}",
 *     summary="Get Single Coaching File",
 *     description="Get details of a specific coaching file",
 *     operationId="getCoachingFileById",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Coaching file details",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/CoachingFile")
 *         )
 *     ),
 *     @OA\Response(response=404, description="File not found")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}/download",
 *     summary="Download Coaching File",
 *     description="Download specific coaching file",
 *     operationId="downloadCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File Download Stream",
 *         @OA\MediaType(
 *             mediaType="application/octet-stream",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(response=404, description="File not found")
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{fileId}",
 *     summary="Delete Coaching File",
 *     description="Delete a coaching file (Mentor Only)",
 *     operationId="deleteCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="File deleted successfully")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Only mentors can delete files"),
 *     @OA\Response(response=404, description="File not found")
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files",
 *     summary="Delete All Coaching Files",
 *     description="Delete all coaching files for a mentoring session (Mentor Only)",
 *     operationId="deleteAllCoachingFiles",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="All files deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="All coaching files deleted successfully")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Only mentors can delete files")
 * )
 */
class CoachingFileSwagger {}
