<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait ApiResponse
 * 
 * FUNGSI: Menyediakan format response JSON yang seragam untuk semua API.
 * 
 * CARA PAKAI: Tambahkan "use ApiResponse;" di dalam controller.
 * 
 * CONTOH RESPONSE SUKSES:
 * {
 *   "sukses": true,
 *   "pesan": "Data berhasil diambil",
 *   "data": {...}
 * }
 * 
 * CONTOH RESPONSE GAGAL:
 * {
 *   "sukses": false,
 *   "pesan": "Data tidak ditemukan",
 *   "data": null
 * }
 */
trait ApiResponse
{
    /**
     * Response SUKSES
     * 
     * Gunakan ini ketika operasi berhasil.
     * Contoh: return $this->responseSukses($user, 'Login berhasil');
     */
    protected function successResponse($data = null, string $message = 'Berhasil', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'sukses' => true,
            'pesan'  => $message,
            'data'   => $data,
        ], $statusCode);
    }

    /**
     * Response GAGAL
     * 
     * Gunakan ini ketika ada error.
     * Contoh: return $this->responseGagal('Email sudah terdaftar');
     */
    protected function errorResponse(string $message = 'Gagal', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'sukses' => false,
            'pesan'  => $message,
            'data'   => null,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Response dengan PAGINATION (halaman)
     * 
     * Gunakan ini untuk data yang banyak dan perlu dibagi per halaman.
     * Contoh: return $this->responsePagination($courses, 'Daftar kursus');
     * 
     * META berisi info halaman:
     * - total: jumlah semua data
     * - per_halaman: jumlah data per halaman
     * - halaman_sekarang: halaman yang sedang ditampilkan
     * - halaman_terakhir: nomor halaman terakhir
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Berhasil'): JsonResponse
    {
        return response()->json([
            'sukses' => true,
            'pesan'  => $message,
            'data'   => $paginator->items(),
            'meta'   => [
                'total'            => $paginator->total(),
                'per_halaman'      => $paginator->perPage(),
                'halaman_sekarang' => $paginator->currentPage(),
                'halaman_terakhir' => $paginator->lastPage(),
                'dari'             => $paginator->firstItem(),
                'sampai'           => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * Response DATA BARU DIBUAT (kode 201)
     * 
     * Gunakan setelah berhasil membuat data baru (insert).
     * Contoh: return $this->responseDibuat($course, 'Kursus berhasil dibuat');
     */
    protected function createdResponse($data, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Response KOSONG (kode 204)
     * 
     * Gunakan ketika tidak perlu mengembalikan data apapun.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Response TIDAK DITEMUKAN (kode 404)
     * 
     * Gunakan ketika data yang dicari tidak ada.
     * Contoh: return $this->responseTidakDitemukan('Kursus tidak ditemukan');
     */
    protected function notFoundResponse(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Response BELUM LOGIN (kode 401)
     * 
     * Gunakan ketika user belum login tapi mencoba akses fitur yang perlu login.
     */
    protected function unauthorizedResponse(string $message = 'Silakan login terlebih dahulu'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Response TIDAK DIIZINKAN (kode 403)
     * 
     * Gunakan ketika user sudah login tapi tidak punya hak akses.
     * Contoh: Student mencoba hapus kursus (hanya admin yang bisa).
     */
    protected function forbiddenResponse(string $message = 'Anda tidak memiliki izin untuk aksi ini'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Response VALIDASI GAGAL (kode 422)
     * 
     * Gunakan ketika input dari user tidak valid.
     * Contoh: Email format salah, password terlalu pendek, dll.
     */
    protected function validationErrorResponse(array $errors, string $message = 'Data tidak valid'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Response SERVER ERROR (kode 500)
     * 
     * Gunakan ketika ada error di server (bug, database error, dll).
     */
    protected function serverErrorResponse(string $message = 'Terjadi kesalahan pada server', $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 500, $errors);
    }
}
