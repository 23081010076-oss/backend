<?php

namespace App\Swagger;

/**
 * @OA\Get(
 * path="/api/scholarships",
 * summary="Get all scholarships",
 * description="Get list of all available scholarships with filtering options",
 * operationId="getScholarships",
 * tags={"Scholarships"},
 * @OA\Parameter(
 * name="status",
 * in="query",
 * description="Filter by scholarship status",
 * required=false,
 * @OA\Schema(type="string", enum={"open", "coming_soon", "closed"})
 * ),
 * @OA\Parameter(
 * name="location",
 * in="query",
 * description="Filter by location",
 * required=false,
 * @OA\Schema(type="string")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarships retrieved successfully",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="array",
 * @OA\Items(
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="LPDP Scholarship Program"),
 * @OA\Property(property="description", type="string", example="Beasiswa penuh untuk program master..."),
 * @OA\Property(property="benefit", type="string", example="Biaya kuliah penuh, biaya hidup..."),
 * @OA\Property(property="location", type="string", example="Indonesia dan Luar Negeri"),
 * @OA\Property(property="status", type="string", example="open"),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-03-31")
 * )
 * )
 * )
 * )
 * )
 *
 * @OA\Get(
 * path="/api/scholarships/{id}",
 * summary="Get scholarship detail",
 * description="Get detailed information about a specific scholarship",
 * operationId="getScholarshipById",
 * tags={"Scholarships"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship detail retrieved"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 *
 * @OA\Post(
 * path="/api/scholarships",
 * summary="Create scholarship",
 * description="Create a new scholarship (Admin only)",
 * operationId="createScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"name","description"},
 * @OA\Property(property="name", type="string", example="New Scholarship Program"),
 * @OA\Property(property="description", type="string", example="Description of the scholarship"),
 * @OA\Property(property="benefit", type="string", example="Full tuition, living expenses"),
 * @OA\Property(property="location", type="string", example="United States"),
 * @OA\Property(property="status", type="string", enum={"open", "coming_soon", "closed"}),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-06-30")
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Scholarship created successfully"
 * ),
 * @OA\Response(
 * response=401,
 * description="Unauthenticated"
 * ),
 * @OA\Response(
 * response=403,
 * description="Unauthorized"
 * )
 * )
 * * @OA\Put(
 * path="/api/scholarships/{id}",
 * summary="Update scholarship",
 * description="Update an existing scholarship (Admin only)",
 * operationId="updateScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * @OA\Property(property="name", type="string", example="Updated Program Name"),
 * @OA\Property(property="description", type="string", example="Updated description"),
 * @OA\Property(property="benefit", type="string", example="Updated benefits"),
 * @OA\Property(property="location", type="string", example="UK"),
 * @OA\Property(property="status", type="string", enum={"open", "coming_soon", "closed"}),
 * @OA\Property(property="deadline", type="string", format="date", example="2024-12-31")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship updated successfully"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 * * @OA\Delete(
 * path="/api/scholarships/{id}",
 * summary="Delete scholarship",
 * description="Remove a scholarship from the system (Admin only)",
 * operationId="deleteScholarship",
 * tags={"Scholarships"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Scholarship ID",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Scholarship deleted successfully"
 * ),
 * @OA\Response(
 * response=404,
 * description="Scholarship not found"
 * )
 * )
 *
 * @OA\Post(
 * path="/api/scholarships/{id}/apply",
 * summary="Apply for scholarship",
 * description="Submit scholarship application with documents for a specific scholarship ID",
 * operationId="applyScholarship",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="ID of the scholarship",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\MediaType(
 * mediaType="multipart/form-data",
 * @OA\Schema(
 * required={"motivation_letter", "cv"},
 * @OA\Property(property="motivation_letter", type="string", format="binary", description="Motivation letter file (PDF/DOC)"),
 * @OA\Property(property="cv", type="string", format="binary", description="CV file (PDF/DOC)"),
 * @OA\Property(property="transcript", type="string", format="binary", description="Transcript file (PDF)"),
 * @OA\Property(property="recommendation", type="string", format="binary", description="Recommendation letter (PDF)")
 * )
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Application submitted successfully"
 * ),
 * @OA\Response(
 * response=422,
 * description="Validation error"
 * )
 * )
 *
 * @OA\Get(
 * path="/api/my-applications",
 * summary="Get my applications",
 * description="Get list of current user's scholarship applications",
 * operationId="getMyApplications",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Response(
 * response=200,
 * description="Applications retrieved successfully"
 * )
 * )
 *
 * @OA\Put(
 * path="/api/scholarship-applications/{id}/status",
 * summary="Update application status",
 * description="Update the status of a scholarship application (Admin/Corporate only)",
 * operationId="updateApplicationStatus",
 * tags={"Scholarship Applications"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="ID of the scholarship application",
 * required=true,
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"status"},
 * @OA\Property(
 * property="status",
 * type="string",
 * example="accepted",
 * enum={"submitted", "review", "accepted", "rejected"},
 * description="The new status for the application"
 * )
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Status updated successfully"
 * ),
 * @OA\Response(
 * response=403,
 * description="Unauthorized action"
 * ),
 * @OA\Response(
 * response=422,
 * description="Invalid status provided"
 * )
 * )
 */
class ScholarshipSwagger {}
