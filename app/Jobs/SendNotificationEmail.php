<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika gagal
     */
    public int $tries = 3;

    /**
     * Timeout dalam detik
     */
    public int $timeout = 60;

    /**
     * Delay antar percobaan (dalam detik)
     */
    public int $backoff = 30;

    /**
     * Data notifikasi
     */
    public User $user;
    public string $subject;
    public string $message;
    public string $type;

    /**
     * Buat instance job baru
     */
    public function __construct(User $user, string $subject, string $message, string $type = 'info')
    {
        $this->user    = $user;
        $this->subject = $subject;
        $this->message = $message;
        $this->type    = $type;

        $this->onQueue('notifications');
    }

    /**
     * Eksekusi job
     */
    public function handle(): void
    {
        try {
            // Kirim email notifikasi (bisa menggunakan Mailable khusus)
            Mail::raw($this->message, function ($mail) {
                $mail->to($this->user->email)
                     ->subject($this->subject);
            });

            Log::info('Notification email sent', [
                'user_id' => $this->user->id,
                'type'    => $this->type,
                'subject' => $this->subject,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification email', [
                'user_id' => $this->user->id,
                'type'    => $this->type,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Notification email job permanently failed', [
            'user_id' => $this->user->id,
            'type'    => $this->type,
            'error'   => $exception->getMessage(),
        ]);
    }
}
