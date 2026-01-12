<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;

class DiagnoseTransactions extends Command
{
    protected $signature = 'transactions:diagnose {--user_id= : Specific user ID to check}';
    protected $description = 'Diagnose transaction data and filters';

    public function handle()
    {
        $this->info('=== TRANSACTION DIAGNOSTICS ===');
        $this->newLine();

        // Total transactions
        $total = Transaction::count();
        $this->info("Total transactions in database: {$total}");
        $this->newLine();

        // Transactions by status
        $this->info('Transactions by status:');
        $statuses = Transaction::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        foreach ($statuses as $status) {
            $this->line("  - {$status->status}: {$status->count}");
        }
        $this->newLine();

        // Transactions by type
        $this->info('Transactions by type:');
        $types = Transaction::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();
        foreach ($types as $type) {
            $this->line("  - {$type->type}: {$type->count}");
        }
        $this->newLine();

        // Transactions by user
        $this->info('Transactions by user:');
        $users = Transaction::selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->get();
        foreach ($users as $user) {
            $userName = User::find($user->user_id)->name ?? 'Unknown';
            $this->line("  - User #{$user->user_id} ({$userName}): {$user->count} transactions");
        }
        $this->newLine();

        // Specific filter: pending + subscription
        $pendingSubscriptions = Transaction::where('status', 'pending')
            ->where('type', 'subscription')
            ->count();
        $this->info("Transactions with status='pending' AND type='subscription': {$pendingSubscriptions}");
        $this->newLine();

        // If user_id specified, show their transactions
        if ($userId = $this->option('user_id')) {
            $this->info("=== TRANSACTIONS FOR USER #{$userId} ===");
            $userTransactions = Transaction::where('user_id', $userId)->get();
            
            if ($userTransactions->isEmpty()) {
                $this->warn("User #{$userId} has NO transactions!");
            } else {
                $this->info("User #{$userId} has {$userTransactions->count()} transaction(s):");
                foreach ($userTransactions as $t) {
                    $this->line("  - ID: {$t->id}, Type: {$t->type}, Status: {$t->status}, Amount: {$t->amount}");
                }
            }
            $this->newLine();

            // Check specific filters for this user
            $userPendingSubscriptions = Transaction::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('type', 'subscription')
                ->count();
            $this->info("User #{$userId} - pending subscription transactions: {$userPendingSubscriptions}");
        }

        $this->newLine();
        $this->info('=== DIAGNOSTIC COMPLETE ===');
        
        return 0;
    }
}
