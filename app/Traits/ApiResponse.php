<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait ApiResponse
 * 
 * Provides standardized JSON response methods for API controllers.
 * Ensures consistent response format across the entire application.
 * Frontend-friendly: 'data' key is always present for easy access.
 * 
 * @package App\Traits
 */
trait ApiResponse
{
    /**
     * Return a success JSON response
     * 
     * Response structure:
     * {
     *   "success": true,
     *   "message": "Success message",
     *   "data": {...} // Always present, null if no data
     * }
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data, // Always include data key for consistency
        ], $statusCode);
    }

    /**
     * Return an error JSON response
     * 
     * Response structure:
     * {
     *   "success": false,
     *   "message": "Error message",
     *   "data": null,
     *   "errors": {...} // Optional, only if provided
     * }
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param mixed $errors Additional error details
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null, // Always include data key for consistency
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated JSON response
     * 
     * Response structure:
     * {
     *   "success": true,
     *   "message": "Success message",
     *   "data": [...], // Array of items
     *   "meta": {
     *     "total": 100,
     *     "per_page": 15,
     *     "current_page": 1,
     *     "last_page": 7,
     *     "from": 1,
     *     "to": 15
     *   }
     * }
     *
     * @param LengthAwarePaginator $paginator Paginator instance
     * @param string $message Success message
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * Return a created resource response (201)
     *
     * @param mixed $data Created resource data
     * @param string $message Success message
     * @return JsonResponse
     */
    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return a no content response (204)
     * Note: 204 responses should not have a body
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Return a not found response (404)
     *
     * @param string $message Error message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return an unauthorized response (401)
     *
     * @param string $message Error message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a forbidden response (403)
     *
     * @param string $message Error message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a validation error response (422)
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Return a server error response (500)
     *
     * @param string $message Error message
     * @param mixed $errors Additional error details
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Internal server error', $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 500, $errors);
    }
}
