<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register new user",
 *     description="Create a new user account",
 *     operationId="register",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation","role"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
 *             @OA\Property(property="role", type="string", enum={"student", "mentor", "corporate"}, example="student"),
 *             @OA\Property(property="phone", type="string", example="08123456789"),
 *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
 *             @OA\Property(property="birth_date", type="string", format="date", example="2000-01-15")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Registration successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Pendaftaran berhasil. Silakan login untuk melanjutkan."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="role", type="string", example="student")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/login",
 *     summary="User login",
 *     description="Authenticate user and return JWT token",
 *     operationId="login",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="admin@learningplatform.com"),
 *             @OA\Property(property="password", type="string", format="password", example="AdminPass123!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login berhasil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin Platform"),
 *                     @OA\Property(property="email", type="string", example="admin@learningplatform.com"),
 *                     @OA\Property(property="role", type="string", example="admin")
 *                 ),
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *                 @OA\Property(property="token_type", type="string", example="Bearer"),
 *                 @OA\Property(property="expires_in", type="integer", example=3600)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Email atau password salah")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/logout",
 *     summary="User logout",
 *     description="Invalidate user token",
 *     operationId="logout",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Logout berhasil")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/auth/refresh",
 *     summary="Refresh token",
 *     description="Get new JWT token using current valid token",
 *     operationId="refreshToken",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Token refreshed",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Token berhasil diperbarui"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *                 @OA\Property(property="token_type", type="string", example="Bearer"),
 *                 @OA\Property(property="expires_in", type="integer", example=3600)
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/me",
 *     summary="Get current user",
 *     description="Get authenticated user data",
 *     operationId="getCurrentUser",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User data retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="role", type="string", example="student"),
 *                 @OA\Property(property="phone", type="string", example="08123456789"),
 *                 @OA\Property(property="gender", type="string", example="male"),
 *                 @OA\Property(property="birth_date", type="string", example="2000-01-15"),
 *                 @OA\Property(property="institution", type="string", example="Universitas Indonesia"),
 *                 @OA\Property(property="major", type="string", example="Teknik Informatika")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/auth/profile",
 *     summary="Get user profile",
 *     description="Get user profile with achievements, experiences, and subscriptions",
 *     operationId="getUserProfile",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Profile retrieved successfully"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/auth/profile",
 *     summary="Update profile",
 *     description="Update authenticated user profile",
 *     operationId="updateProfile",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John Doe Updated"),
 *             @OA\Property(property="phone", type="string", example="08123456789"),
 *             @OA\Property(property="address", type="string", example="Jl. Merdeka No. 123"),
 *             @OA\Property(property="institution", type="string", example="Universitas Indonesia"),
 *             @OA\Property(property="major", type="string", example="Teknik Informatika"),
 *             @OA\Property(property="bio", type="string", example="Software Developer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/auth/change-password",
 *     summary="Change password",
 *     description="Change authenticated user password",
 *     operationId="changePassword",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"current_password","password","password_confirmation"},
 *             @OA\Property(property="current_password", type="string", example="oldpassword123"),
 *             @OA\Property(property="password", type="string", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password changed successfully"
 *     )
 * )
 */
class AuthSwagger
{
}
