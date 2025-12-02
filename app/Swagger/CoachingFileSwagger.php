<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files",
 *     summary="Get coaching files",
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
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="mentoring_session_id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="React Hooks Guide"),
 *                     @OA\Property(property="description", type="string", example="Comprehensive guide to React Hooks"),
 *                     @OA\Property(property="file_path", type="string", example="coaching-files/react-hooks.pdf"),
 *                     @OA\Property(property="file_name", type="string", example="react-hooks.pdf"),
 *                     @OA\Property(property="file_size", type="integer", example=1024000),
 *                     @OA\Property(property="file_type", type="string", example="application/pdf"),
 *                     @OA\Property(property="uploaded_by", type="integer", example=2),
 *                     @OA\Property(property="created_at", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Mentoring session not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files",
 *     summary="Upload coaching file",
 *     description="Upload a new coaching file for mentoring session",
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
 *                 required={"title", "file"},
 *                 @OA\Property(property="title", type="string", description="File title", example="React Hooks Complete Guide"),
 *                 @OA\Property(property="description", type="string", description="File description", example="Comprehensive guide covering useState, useEffect, useContext, etc."),
 *                 @OA\Property(property="file", type="string", format="binary", description="File to upload (PDF, DOC, DOCX, images, etc.)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="File uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="File uploaded successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="React Hooks Complete Guide"),
 *                 @OA\Property(property="file_name", type="string", example="react-hooks.pdf"),
 *                 @OA\Property(property="file_size", type="integer", example=1024000),
 *                 @OA\Property(property="file_type", type="string", example="application/pdf")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{id}",
 *     summary="Get single coaching file",
 *     description="Get details of a specific coaching file",
 *     operationId="getCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Coaching file details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File not found"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{id}",
 *     summary="Update coaching file",
 *     description="Update coaching file details",
 *     operationId="updateCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Updated Title"),
 *             @OA\Property(property="description", type="string", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{id}",
 *     summary="Delete coaching file",
 *     description="Delete a coaching file",
 *     operationId="deleteCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File not found"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentoringSessionId}/coaching-files/{id}/download",
 *     summary="Download coaching file",
 *     description="Download a coaching file",
 *     operationId="downloadCoachingFile",
 *     tags={"Coaching Files"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="mentoringSessionId",
 *         in="path",
 *         description="Mentoring Session ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Coaching File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File download",
 *         @OA\MediaType(
 *             mediaType="application/octet-stream",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="File not found"
 *     )
 * )
 */
class CoachingFileSwagger {}
