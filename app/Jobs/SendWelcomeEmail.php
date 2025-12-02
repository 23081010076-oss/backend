<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail implements ShouldQueue
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
     * User yang akan dikirimi email
     */
    public User $user;

    /**
     * Buat instance job baru
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('emails');
    }

    /**
     * Eksekusi job
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user->email)->send(new WelcomeEmail($this->user));

            Log::info('Welcome email sent', [
                'user_id' => $this->user->id,
                'email'   => $this->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $this->user->id,
                'error'   => $e->getMessage(),
            ]);

            throw $e; // Re-throw untuk retry
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Welcome email job permanently failed', [
            'user_id' => $this->user->id,
            'email'   => $this->user->email,
            'error'   => $exception->getMessage(),
        ]);
    }
}
