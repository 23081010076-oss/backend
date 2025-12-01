<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * UPDATE ARTICLE REQUEST (Validasi untuk Update Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user mengupdate artikel.
 * 
 * CATATAN: Semua field opsional (pakai 'sometimes')
 */
class UpdateArticleRequest extends FormRequest
{
    /**
     * Apakah user boleh akses endpoint ini?
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ATURAN VALIDASI
     */
    public function rules(): array
    {
        return [
            'title'     => 'sometimes|string|max:255',
            'content'   => 'sometimes|string',
            'excerpt'   => 'nullable|string|max:500',
            'category'  => 'nullable|string|max:100',
            'tags'      => 'nullable|string',
            'status'    => 'nullable|in:draft,published',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.max'     => 'Judul maksimal 255 karakter',
            'excerpt.max'   => 'Ringkasan maksimal 500 karakter',
            'category.max'  => 'Kategori maksimal 100 karakter',
            'status.in'     => 'Status harus draft atau published',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'Format gambar harus jpeg, png, jpg, atau gif',
            'image.max'     => 'Ukuran gambar maksimal 2MB',
            'image_url.url' => 'Format URL gambar tidak valid',
        ];
    }
}
