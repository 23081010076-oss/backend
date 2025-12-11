<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ✅ NEW: Job to automatically expire unpaid transactions
 * 
 * This job should be scheduled to run hourly via Laravel Scheduler
 * Add to app/Console/Kernel.php:
 * $schedule->job(new ExpireUnpaidTransactions)->hourly();
 */
class ExpireUnpaidTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredCount = Transaction::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            Log::info('Expired unpaid transactions', [
                'count' => $expiredCount,
                'timestamp' => now(),
            ]);
        }
    }
}
