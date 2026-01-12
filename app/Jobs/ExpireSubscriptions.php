<?php

namespace App\Jobs;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ✅ NEW: Job to automatically expire subscriptions
 * 
 * This job should be scheduled to run daily via Laravel Scheduler
 * Add to app/Console/Kernel.php:
 * $schedule->job(new ExpireSubscriptions)->daily();
 */
class ExpireSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredCount = Subscription::where('status', 'active')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            Log::info('Expired subscriptions', [
                'count' => $expiredCount,
                'timestamp' => now(),
            ]);
        }
    }
}
