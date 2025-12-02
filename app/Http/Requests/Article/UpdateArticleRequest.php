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
 * Database columns: id, author_id, title, content, category, author (string), timestamps
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
            'title'    => 'sometimes|string|max:255',
            'content'  => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'author'   => 'nullable|string|max:255',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'title.max'    => 'Judul maksimal 255 karakter',
            'category.max' => 'Kategori maksimal 100 karakter',
            'author.max'   => 'Nama penulis maksimal 255 karakter',
        ];
    }
}
