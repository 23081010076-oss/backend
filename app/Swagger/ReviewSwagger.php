<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/reviews",
 *     summary="Get all reviews",
 *     description="Get list of all reviews (public)",
 *     operationId="getReviews",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="organization_id",
 *         in="query",
 *         description="Filter by organization ID",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reviews retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=1),
 *                     @OA\Property(property="organization_id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="John Doe"),
 *                     @OA\Property(property="description", type="string", example="Great platform..."),
 *                     @OA\Property(property="focus_area", type="integer", example=5),
 *                     @OA\Property(property="created_at", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/reviews",
 *     summary="Create review",
 *     description="Submit a new review",
 *     operationId="createReview",
 *     tags={"Reviews"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"description"},
 *             @OA\Property(property="organization_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="description", type="string", example="Great learning platform with excellent mentors"),
 *             @OA\Property(property="focus_area", type="integer", minimum=1, maximum=5, example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Review created successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/reviews/{id}",
 *     summary="Get review detail",
 *     description="Get specific review details",
 *     operationId="getReviewById",
 *     tags={"Reviews"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Review ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review retrieved"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/reviews/{id}",
 *     summary="Update review",
 *     description="Update existing review (owner only)",
 *     operationId="updateReview",
 *     tags={"Reviews"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Review ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="focus_area", type="integer", minimum=1, maximum=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/reviews/{id}",
 *     summary="Delete review",
 *     description="Delete a review (owner only)",
 *     operationId="deleteReview",
 *     tags={"Reviews"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Review ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review deleted"
 *     )
 * )
 */
class ReviewSwagger {}
