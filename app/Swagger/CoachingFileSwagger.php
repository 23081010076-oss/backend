<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 * schema="CoachingFile",
 * title="Coaching File",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="session_id", type="integer", example=10),
 * @OA\Property(property="file_name", type="string", example="react-guide.pdf"),
 * @OA\Property(property="file_path", type="string", example="uploads/coaching/react-guide.pdf"),
 * @OA\Property(property="created_at", type="string", format="datetime")
 * )
 */
class CoachingFileSwagger
{
    /**
     * @OA\Get(
     * path="/mentoring-sessions/{sessionId}/coaching-files",
     * summary="Get Coaching Files",
     * description="Get all coaching files for a mentoring session",
     * operationId="getCoachingFiles",
     * tags={"Coaching Files"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="sessionId",
     * in="path",
     * description="Mentoring Session ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="List of coaching files",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CoachingFile"))
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Session not found")
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     * path="/mentoring-sessions/{sessionId}/coaching-files",
     * summary="Upload Coaching File",
     * description="Upload a new coaching file (Mentor Only)",
     * operationId="uploadCoachingFile",
     * tags={"Coaching Files"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="sessionId",
     * in="path",
     * description="Mentoring Session ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"file", "file_name"},
     * @OA\Property(property="file", type="string", format="binary", description="File (required)"),
     * @OA\Property(property="file_name", type="string", description="String (required)", example="Materi Pertemuan 1")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="File uploaded successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="File uploaded successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/CoachingFile")
     * )
     * ),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     * path="/mentoring-sessions/{sessionId}/coaching-files/{fileId}/download",
     * summary="Download Coaching File",
     * description="Download specific coaching file",
     * operationId="downloadCoachingFile",
     * tags={"Coaching Files"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="sessionId",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="fileId",
     * in="path",
     * description="Coaching File ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="File Download Stream",
     * @OA\MediaType(
     * mediaType="application/octet-stream",
     * @OA\Schema(type="string", format="binary")
     * )
     * ),
     * @OA\Response(response=404, description="File not found")
     * )
     */
    public function download() {}

    /**
     * @OA\Delete(
     * path="/mentoring-sessions/{sessionId}/coaching-files/{fileId}",
     * summary="Delete Coaching File",
     * description="Delete a coaching file (Mentor Only)",
     * operationId="deleteCoachingFile",
     * tags={"Coaching Files"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="sessionId",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="fileId",
     * in="path",
     * description="Coaching File ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="File deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="File deleted successfully")
     * )
     * ),
     * @OA\Response(response=404, description="File not found")
     * )
     */
    public function destroy() {}
}
