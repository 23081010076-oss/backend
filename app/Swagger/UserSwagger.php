<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/admin/users",
 *     summary="Get all users (Admin)",
 *     description="Get list of all users - Admin only",
 *     operationId="adminGetUsers",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="role",
 *         in="query",
 *         description="Filter by role",
 *         required=false,
 *         @OA\Schema(type="string", enum={"student", "mentor", "admin", "corporate"})
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search by name or email",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Items per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Users retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Data pengguna berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Ahmad Rizki"),
 *                     @OA\Property(property="email", type="string", example="ahmad.rizki@student.com"),
 *                     @OA\Property(property="role", type="string", example="student"),
 *                     @OA\Property(property="gender", type="string", example="male"),
 *                     @OA\Property(property="birth_date", type="string", example="2001-05-15"),
 *                     @OA\Property(property="phone", type="string", example="08123456001"),
 *                     @OA\Property(property="address", type="string", example="Jl. Merdeka No. 123, Jakarta Pusat"),
 *                     @OA\Property(property="institution", type="string", example="Universitas Indonesia"),
 *                     @OA\Property(property="major", type="string", example="Teknik Informatika"),
 *                     @OA\Property(property="education_level", type="string", example="S1"),
 *                     @OA\Property(property="bio", type="string", example="Mahasiswa semester 6 yang passionate dengan web development dan machine learning.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin only"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/admin/users",
 *     summary="Create user (Admin)",
 *     description="Create a new user - Admin only",
 *     operationId="adminCreateUser",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","role"},
 *             @OA\Property(property="name", type="string", example="New User"),
 *             @OA\Property(property="email", type="string", format="email", example="newuser@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="role", type="string", enum={"student", "mentor", "admin", "corporate"}, example="student"),
 *             @OA\Property(property="phone", type="string", example="08123456789"),
 *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}),
 *             @OA\Property(property="birth_date", type="string", format="date"),
 *             @OA\Property(property="institution", type="string"),
 *             @OA\Property(property="major", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin only"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/admin/users/{id}",
 *     summary="Get user detail (Admin)",
 *     description="Get specific user details - Admin only",
 *     operationId="adminGetUserById",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/admin/users/{id}",
 *     summary="Update user (Admin)",
 *     description="Update user data - Admin only",
 *     operationId="adminUpdateUser",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="role", type="string", enum={"student", "mentor", "admin", "corporate"}),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="institution", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/admin/users/{id}",
 *     summary="Delete user (Admin)",
 *     description="Delete a user - Admin only",
 *     operationId="adminDeleteUser",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
class UserSwagger {}
