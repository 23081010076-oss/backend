<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiException
 * 
 * Custom exception for API-related errors.
 * Provides consistent error formatting and HTTP status codes.
 * 
 * @package App\Exceptions
 */
class ApiException extends Exception
{
    /**
     * HTTP status code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Additional error details
     *
     * @var mixed
     */
    protected $errors;

    /**
     * Create a new ApiException instance
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param mixed $errors Additional error details
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 400,
        $errors = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * Get the HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get additional error details
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Render the exception as an HTTP response
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $this->statusCode);
    }

    /**
     * Create a validation error exception
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return static
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): self
    {
        return new static($message, 422, $errors);
    }

    /**
     * Create an unauthorized exception
     *
     * @param string $message Error message
     * @return static
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new static($message, 401);
    }

    /**
     * Create a forbidden exception
     *
     * @param string $message Error message
     * @return static
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new static($message, 403);
    }

    /**
     * Create a not found exception
     *
     * @param string $message Error message
     * @return static
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return new static($message, 404);
    }

    /**
     * Create a conflict exception
     *
     * @param string $message Error message
     * @return static
     */
    public static function conflict(string $message = 'Resource conflict'): self
    {
        return new static($message, 409);
    }

    /**
     * Create a server error exception
     *
     * @param string $message Error message
     * @param mixed $errors Additional error details
     * @return static
     */
    public static function serverError(string $message = 'Internal server error', $errors = null): self
    {
        return new static($message, 500, $errors);
    }
}
