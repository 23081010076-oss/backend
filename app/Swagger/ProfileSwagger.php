<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/auth/profile/photo",
 *     summary="Upload profile photo",
 *     description="Upload user profile photo",
 *     operationId="uploadProfilePhoto",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"photo"},
 *                 @OA\Property(property="photo", type="string", format="binary", description="Profile photo (JPEG, PNG, max 2MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Photo uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Profile photo uploaded successfully"),
 *             @OA\Property(property="photo_url", type="string", example="http://localhost/storage/profile-photos/abc123.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid file format or size"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/auth/profile/cv",
 *     summary="Upload CV",
 *     description="Upload user CV document",
 *     operationId="uploadCv",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"cv"},
 *                 @OA\Property(property="cv", type="string", format="binary", description="CV document (PDF, DOC, DOCX, max 5MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="CV uploaded successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/recommendations",
 *     summary="Get recommendations",
 *     description="Get personalized recommendations for courses, scholarships, etc.",
 *     operationId="getRecommendations",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Personalized recommendations",
 *         @OA\JsonContent(
 *             @OA\Property(property="courses", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="scholarships", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="mentors", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/portfolio",
 *     summary="Get user portfolio",
 *     description="Get user portfolio including achievements, experiences, and organizations",
 *     operationId="getPortfolioAuth",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User portfolio",
 *         @OA\JsonContent(
 *             @OA\Property(property="achievements", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="experiences", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="organizations", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/activity-history",
 *     summary="Get activity history",
 *     description="Get user activity history",
 *     operationId="getActivityHistory",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User activity history",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="type", type="string", example="enrollment"),
 *                     @OA\Property(property="description", type="string", example="Enrolled in React Course"),
 *                     @OA\Property(property="timestamp", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class ProfileSwagger {}
