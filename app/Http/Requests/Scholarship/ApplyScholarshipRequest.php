<?php

namespace App\Http\Requests\Scholarship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * APPLY SCHOLARSHIP REQUEST (Validasi untuk Daftar Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Memvalidasi dokumen ketika user mendaftar beasiswa.
 * 
 * FIELD YANG DIVALIDASI (semua opsional, berupa file):
 * - motivation_letter   = Surat motivasi (pdf, doc, docx)
 * - cv_path             = CV (pdf)
 * - transcript_path     = Transkrip nilai (pdf)
 * - recommendation_path = Surat rekomendasi (pdf, doc, docx)
 */
class ApplyScholarshipRequest extends FormRequest
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
            'motivation_letter'   => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cv_path'             => 'nullable|file|mimes:pdf|max:2048',
            'transcript_path'     => 'nullable|file|mimes:pdf|max:2048',
            'recommendation_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    /**
     * PESAN ERROR (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'motivation_letter.file'    => 'Surat motivasi harus berupa file',
            'motivation_letter.mimes'   => 'Format surat motivasi harus pdf, doc, atau docx',
            'motivation_letter.max'     => 'Ukuran surat motivasi maksimal 2MB',
            
            'cv_path.file'  => 'CV harus berupa file',
            'cv_path.mimes' => 'Format CV harus pdf',
            'cv_path.max'   => 'Ukuran CV maksimal 2MB',
            
            'transcript_path.file'  => 'Transkrip harus berupa file',
            'transcript_path.mimes' => 'Format transkrip harus pdf',
            'transcript_path.max'   => 'Ukuran transkrip maksimal 2MB',
            
            'recommendation_path.file'  => 'Surat rekomendasi harus berupa file',
            'recommendation_path.mimes' => 'Format surat rekomendasi harus pdf, doc, atau docx',
            'recommendation_path.max'   => 'Ukuran surat rekomendasi maksimal 2MB',
        ];
    }
}
