<?php

namespace App\Jobs;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateCertificatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika gagal
     */
    public int $tries = 3;

    /**
     * Timeout dalam detik (PDF generation bisa lama)
     */
    public int $timeout = 120;

    /**
     * Delay antar percobaan (dalam detik)
     */
    public int $backoff = 60;

    /**
     * Data enrollment untuk certificate
     */
    public Enrollment $enrollment;

    /**
     * Buat instance job baru
     */
    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
        $this->onQueue('pdf');
    }

    /**
     * Eksekusi job
     */
    public function handle(): void
    {
        try {
            $enrollment = $this->enrollment->load(['user', 'course']);

            // Data untuk certificate
            $data = [
                'user_name'         => $enrollment->user->name,
                'course_name'       => $enrollment->course->title,
                'completion_date'   => $enrollment->completed_at ?? now(),
                'certificate_id'    => 'CERT-' . strtoupper(uniqid()),
                'enrollment_id'     => $enrollment->id,
            ];

            // Generate PDF
            $pdf = Pdf::loadView('certificates.course', $data);

            // Simpan ke storage
            $filename = 'certificates/course_' . $enrollment->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            // Update enrollment dengan path certificate
            $enrollment->update([
                'certificate_path' => $filename,
            ]);

            Log::info('Certificate PDF generated', [
                'enrollment_id' => $enrollment->id,
                'user_id'       => $enrollment->user_id,
                'filename'      => $filename,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate certificate PDF', [
                'enrollment_id' => $this->enrollment->id,
                'error'         => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Certificate PDF generation permanently failed', [
            'enrollment_id' => $this->enrollment->id,
            'error'         => $exception->getMessage(),
        ]);
    }
}
