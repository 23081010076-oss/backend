<?php

namespace App\Swagger;

/**
 * @OA\Get(
 * path="/api/reviews",
 * summary="Get all reviews",
 * description="Get list of all reviews (public)",
 * operationId="getReviews",
 * tags={"Reviews"},
 * @OA\Response(
 * response=200,
 * description="Reviews retrieved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="array",
 * @OA\Items(
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="user_id", type="integer", example=101),
 * @OA\Property(property="reviewable_type", type="string", example="App\\Models\\Scholarship"),
 * @OA\Property(property="reviewable_id", type="integer", example=1),
 * @OA\Property(property="rating", type="integer", example=5),
 * @OA\Property(property="comment", type="string", example="Excellent scholarship program!"),
 * @OA\Property(property="created_at", type="string", format="datetime")
 * )
 * )
 * )
 * )
 * )
 *
 * @OA\Post(
 * path="/api/reviews",
 * summary="Create review",
 * description="Submit a new review",
 * operationId="createReview",
 * tags={"Reviews"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 *
 *
 * @OA\JsonContent(
 * required={"reviewable_type", "reviewable_id", "rating", "comment"},
 * @OA\Property(
 * property="reviewable_type",
 * type="string",
 * enum={"App\\Models\\Scholarship", "App\\Models\\Organization", "App\\Models\\Course"},
 * example="App\Models\Scholarship"
 * ),
 * @OA\Property(property="reviewable_id", type="integer", example=1),
 * @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 * @OA\Property(property="comment", type="string", example="Excellent scholarship program!")
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Review created successfully"
 * )
 * )
 *
 * @OA\Get(
 * path="/api/reviews/{id}",
 * summary="Get single review",
 * description="Get specific review details",
 * operationId="getReviewById",
 * tags={"Reviews"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Review ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Review retrieved",
 * @OA\JsonContent(
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="reviewable_type", type="string", example="App\\Models\\Scholarship"),
 * @OA\Property(property="reviewable_id", type="integer", example=1),
 * @OA\Property(property="rating", type="integer", example=5),
 * @OA\Property(property="comment", type="string", example="Excellent scholarship program!")
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="Review not found"
 * )
 * )
 *
 * @OA\Put(
 * path="/api/reviews/{id}",
 * summary="Update review",
 * description="Update existing review",
 * operationId="updateReview",
 * tags={"Reviews"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Review ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * @OA\JsonContent(
 * @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=4),
 * @OA\Property(property="comment", type="string", example="Updated comment content")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Review updated"
 * )
 * )
 *
 * @OA\Delete(
 * path="/api/reviews/{id}",
 * summary="Delete review",
 * description="Delete a review",
 * operationId="deleteReview",
 * tags={"Reviews"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Review ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Review deleted"
 * )
 * )
 */
class ReviewSwagger {}
