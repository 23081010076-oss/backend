<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/experiences",
 *     summary="Get my experiences",
 *     description="Get list of user's work/education experiences",
 *     operationId="getMyExperiences",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Experiences retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Software Developer"),
 *                     @OA\Property(property="description", type="string", example="Developed web applications..."),
 *                     @OA\Property(property="type", type="string", example="work"),
 *                     @OA\Property(property="level", type="string", example="Junior"),
 *                     @OA\Property(property="company", type="string", example="PT Technology Indonesia"),
 *                     @OA\Property(property="start_date", type="string", format="date", example="2023-01-15"),
 *                     @OA\Property(property="end_date", type="string", format="date", nullable=true),
 *                     @OA\Property(property="certificate_url", type="string", nullable=true)
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/experiences",
 *     summary="Create experience",
 *     description="Add new work/education experience",
 *     operationId="createExperience",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","type","company","start_date"},
 *             @OA\Property(property="title", type="string", example="Frontend Developer"),
 *             @OA\Property(property="description", type="string", example="Developing user interfaces..."),
 *             @OA\Property(property="type", type="string", enum={"work", "internship", "volunteer"}, example="work"),
 *             @OA\Property(property="level", type="string", enum={"Entry", "Junior", "Mid-level", "Senior"}, example="Junior"),
 *             @OA\Property(property="company", type="string", example="PT ABC Technology"),
 *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
 *             @OA\Property(property="end_date", type="string", format="date", nullable=true, example="2023-12-31"),
 *             @OA\Property(property="certificate_url", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Experience created successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/experiences/{id}",
 *     summary="Get experience detail",
 *     description="Get specific experience detail",
 *     operationId="getExperienceById",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Experience retrieved"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/experiences/{id}",
 *     summary="Update experience",
 *     description="Update existing experience",
 *     operationId="updateExperience",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="end_date", type="string", format="date")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Experience updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/experiences/{id}",
 *     summary="Delete experience",
 *     description="Delete an experience",
 *     operationId="deleteExperience",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Experience deleted"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/achievements",
 *     summary="Get my achievements",
 *     description="Get list of user's achievements",
 *     operationId="getMyAchievements",
 *     tags={"Achievements"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Achievements retrieved"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/achievements",
 *     summary="Create achievement",
 *     description="Add new achievement",
 *     operationId="createAchievement",
 *     tags={"Achievements"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="Best Student Award"),
 *             @OA\Property(property="description", type="string", example="Awarded for academic excellence"),
 *             @OA\Property(property="organization", type="string", example="University of Indonesia"),
 *             @OA\Property(property="year", type="integer", example=2023)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Achievement created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/achievements/{id}",
 *     summary="Update achievement",
 *     description="Update existing achievement",
 *     operationId="updateAchievement",
 *     tags={"Achievements"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Achievement updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/achievements/{id}",
 *     summary="Delete achievement",
 *     description="Delete an achievement",
 *     operationId="deleteAchievement",
 *     tags={"Achievements"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Achievement deleted"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/organizations",
 *     summary="Get my organizations",
 *     description="Get list of user's organizations",
 *     operationId="getMyOrganizations",
 *     tags={"Organizations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Organizations retrieved"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/organizations",
 *     summary="Create organization",
 *     description="Add new organization",
 *     operationId="createOrganization",
 *     tags={"Organizations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Google Developer Student Club"),
 *             @OA\Property(property="role", type="string", example="President"),
 *             @OA\Property(property="description", type="string", example="Leading tech community..."),
 *             @OA\Property(property="location", type="string", example="Jakarta, Indonesia"),
 *             @OA\Property(property="start_date", type="string", format="date"),
 *             @OA\Property(property="end_date", type="string", format="date", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Organization created"
 *     )
 * )
 */
class PortfolioSwagger {}
