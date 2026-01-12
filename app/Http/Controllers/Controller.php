<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="LMS API Documentation Stuident Rekanesia",
 *     description="API DOC for Learning Management System Stuident Rekanesia",
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://StudentRekanesia.example.com (Comingsoon)",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT token obtained from login endpoint"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints (login, register, logout)"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 * @OA\Tag(
 *     name="Courses",
 *     description="Course management endpoints"
 * )
 * @OA\Tag(
 *     name="Enrollments",
 *     description="Course enrollment endpoints"
 * )
 * @OA\Tag(
 *     name="Scholarships",
 *     description="Scholarship management endpoints"
 * )
 * @OA\Tag(
 *     name="Scholarship Applications",
 *     description="Scholarship application endpoints"
 * )
 * @OA\Tag(
 *     name="Mentoring",
 *     description="Mentoring session endpoints"
 * )
 * @OA\Tag(
 *     name="Articles",
 *     description="Article/Blog management endpoints"
 * )
 * @OA\Tag(
 *     name="Achievements",
 *     description="User achievements/portfolio endpoints"
 * )
 * @OA\Tag(
 *     name="Experiences",
 *     description="User work/education experience endpoints"
 * )
 * @OA\Tag(
 *     name="Organizations",
 *     description="Organization management endpoints"
 * )
 * @OA\Tag(
 *     name="Subscriptions",
 *     description="User subscription endpoints"
 * )
 * @OA\Tag(
 *     name="Transactions",
 *     description="Payment transaction endpoints"
 * )
 * @OA\Tag(
 *     name="Profile",
 *     description="User profile management endpoints"
 * )
 * @OA\Tag(
 *     name="Reviews",
 *     description="Course review endpoints"
 * )
 * @OA\Tag(
 *     name="Corporate Contact",
 *     description="Corporate inquiry endpoints"
 * )
 * @OA\Tag(
 *     name="Webhook",
 *     description="Payment gateway webhook endpoints"
 * )
 * @OA\Tag(
 *     name="Google Auth",
 *     description="Google OAuth authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Need Assessments",
 *     description="Mentoring session need assessment endpoints"
 * )
 * @OA\Tag(
 *     name="Coaching Files",
 *     description="Mentoring session coaching file endpoints"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
