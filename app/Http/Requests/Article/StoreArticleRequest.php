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
 * FIELD YANG DIVALIDASI:
 * - title     = Judul artikel (wajib)
 * - content   = Isi artikel (wajib)
 * - excerpt   = Ringkasan (opsional, max 500 karakter)
 * - category  = Kategori artikel (opsional)
 * - tags      = Tag/label artikel (opsional)
 * - status    = Status: draft/published (opsional, default: draft)
 * - image     = File gambar (opsional)
 * - image_url = URL gambar (opsional)
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
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            
            // FIELD OPSIONAL
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
            'title.required'   => 'Judul artikel wajib diisi',
            'title.max'        => 'Judul maksimal 255 karakter',
            'content.required' => 'Isi artikel wajib diisi',
            'excerpt.max'      => 'Ringkasan maksimal 500 karakter',
            'category.max'     => 'Kategori maksimal 100 karakter',
            'status.in'        => 'Status harus draft atau published',
            'image.image'      => 'File harus berupa gambar',
            'image.mimes'      => 'Format gambar harus jpeg, png, jpg, atau gif',
            'image.max'        => 'Ukuran gambar maksimal 2MB',
            'image_url.url'    => 'Format URL gambar tidak valid',
        ];
    }
}
