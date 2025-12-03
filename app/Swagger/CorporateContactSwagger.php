<?php

namespace App\Swagger;

/**
  * @OA\Post(
 *     path="/api/corporate-contact",
 *     summary="Submit corporate inquiry",
 *     description="Submit corporate partnership/inquiry form (public)",
 *     operationId="submitCorporateContact",
 *     tags={"Corporate Contact"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","message"},
 *             @OA\Property(property="org_id", type="integer", nullable=true, example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="message", type="string", example="We are interested in a corporate partnership.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Corporate contact submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Corporate contact submitted successfully. We will get back to you soon."),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/corporate-contacts",
 *     summary="Get all corporate contacts (Admin)",
 *     description="Get list of all corporate contact inquiries - Admin only",
 *     operationId="getCorporateContacts",
 *     tags={"Corporate Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by status",
 *         required=false,
 *         @OA\Schema(type="string", enum={"pending", "contacted", "in_progress", "closed"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Corporate contacts retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="company_name", type="string", example="PT ABC Technology"),
 *                     @OA\Property(property="contact_name", type="string", example="John Doe"),
 *                     @OA\Property(property="email", type="string", example="john@abc-tech.com"),
 *                     @OA\Property(property="phone", type="string", example="08123456789"),
 *                     @OA\Property(property="message", type="string"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="created_at", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin only"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/corporate-contacts/{id}",
 *     summary="Get corporate contact detail (Admin)",
 *     description="Get specific corporate contact details - Admin only",
 *     operationId="getCorporateContactById",
 *     tags={"Corporate Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Corporate Contact ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Corporate contact retrieved"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not found"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/corporate-contacts/{id}/status",
 *     summary="Update contact status (Admin)",
 *     description="Update corporate contact status - Admin only",
 *     operationId="updateCorporateContactStatus",
 *     tags={"Corporate Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Corporate Contact ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"pending", "contacted", "in_progress", "closed"}, example="contacted"),
 *             @OA\Property(property="notes", type="string", example="Contacted via email on 2024-01-15")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/corporate-contacts/{id}",
 *     summary="Delete corporate contact (Admin)",
 *     description="Delete a corporate contact - Admin only",
 *     operationId="deleteCorporateContact",
 *     tags={"Corporate Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Corporate Contact ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Corporate contact deleted"
 *     )
 * )
 */
class CorporateContactSwagger {}
