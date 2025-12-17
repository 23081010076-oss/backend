<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiException
 * 
 * Exception khusus untuk error terkait API.
 * Menyediakan format error yang konsisten dan HTTP status code.
 * 
 * @package App\Exceptions
 */
class ApiException extends Exception
{
    /**
     * Kode status HTTP
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Detail error tambahan
     *
     * @var mixed
     */
    protected $errors;

    /**
     * Membuat instance ApiException baru
     *
     * @param string $message Pesan error
     * @param int $statusCode Kode status HTTP
     * @param mixed $errors Detail error tambahan
     * @param \Throwable|null $previous Exception sebelumnya
     */
    public function __construct(
        string $message = 'Terjadi kesalahan',
        int $statusCode = 400,
        $errors = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * Ambil kode status HTTP
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Ambil detail error tambahan
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Render exception sebagai HTTP response
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
     * Buat exception error validasi
     *
     * @param array $errors Error validasi
     * @param string $message Pesan error
     * @return static
     */
    public static function validationError(array $errors, string $message = 'Validasi gagal'): self
    {
        return new static($message, 422, $errors);
    }

    /**
     * Buat exception unauthorized
     *
     * @param string $message Pesan error
     * @return static
     */
    public static function unauthorized(string $message = 'Tidak terotorisasi'): self
    {
        return new static($message, 401);
    }

    /**
     * Buat exception forbidden
     *
     * @param string $message Pesan error
     * @return static
     */
    public static function forbidden(string $message = 'Akses ditolak'): self
    {
        return new static($message, 403);
    }

    /**
     * Buat exception not found
     *
     * @param string $message Pesan error
     * @return static
     */
    public static function notFound(string $message = 'Resource tidak ditemukan'): self
    {
        return new static($message, 404);
    }

    /**
     * Buat exception conflict
     *
     * @param string $message Pesan error
     * @return static
     */
    public static function conflict(string $message = 'Konflik resource'): self
    {
        return new static($message, 409);
    }

    /**
     * Buat exception server error
     *
     * @param string $message Pesan error
     * @param mixed $errors Detail error tambahan
     * @return static
     */
    public static function serverError(string $message = 'Kesalahan server internal', $errors = null): self
    {
        return new static($message, 500, $errors);
    }
}
