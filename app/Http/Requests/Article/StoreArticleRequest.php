<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * STORE ARTICLE REQUEST (Validasi untuk Tambah Artikel)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi data ketika user menambah artikel baru.
 * 
 * Database columns: id, author_id, title, content, category, author (string), timestamps
 * 
 * FIELD YANG DIVALIDASI:
 * - title     = Judul artikel (wajib)
 * - content   = Isi artikel (wajib)
 * - category  = Kategori artikel (opsional)
 * - author    = Nama penulis (opsional, akan diisi otomatis jika kosong)
 */
class StoreArticleRequest extends FormRequest
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
            // FIELD WAJIB
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            
            // FIELD OPSIONAL
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
            'title.required'   => 'Judul artikel wajib diisi',
            'title.max'        => 'Judul maksimal 255 karakter',
            'content.required' => 'Isi artikel wajib diisi',
            'category.max'     => 'Kategori maksimal 100 karakter',
            'author.max'       => 'Nama penulis maksimal 255 karakter',
        ];
    }
}
