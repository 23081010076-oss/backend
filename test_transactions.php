<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\User;

echo "=== Testing Transactions ===\n\n";

// Test 1: Total transactions
$total = Transaction::count();
echo "Total transactions: $total\n\n";

// Test 2: Transactions by type
$types = Transaction::selectRaw('type, COUNT(*) as count')
    ->groupBy('type')
    ->get();

echo "Transactions by type:\n";
foreach ($types as $type) {
    echo "  - {$type->type}: {$type->count}\n";
}
echo "\n";

// Test 3: Check user ID 3
$user3 = User::find(3);
if ($user3) {
    echo "User ID 3: {$user3->name} ({$user3->email}) - Role: {$user3->role}\n";
    $user3Transactions = Transaction::where('user_id', 3)->get();
    echo "User 3 transactions: {$user3Transactions->count()}\n";
    foreach ($user3Transactions as $trans) {
        echo "  - {$trans->transaction_code} | {$trans->type} | {$trans->status}\n";
    }
    echo "\n";
}

// Test 4: Check all users with transactions
echo "Users with transactions:\n";
$usersWithTrans = Transaction::selectRaw('user_id, COUNT(*) as count')
    ->groupBy('user_id')
    ->get();

foreach ($usersWithTrans as $item) {
    $user = User::find($item->user_id);
    if ($user) {
        echo "  - User {$item->user_id}: {$user->name} ({$user->email}) - {$item->count} transactions\n";
    }
}
echo "\n";

// Test 5: Mentoring transactions
$mentoringTrans = Transaction::where('type', 'mentoring_session')->get();
echo "Mentoring transactions: {$mentoringTrans->count()}\n";
foreach ($mentoringTrans as $trans) {
    $user = User::find($trans->user_id);
    echo "  - User {$trans->user_id} ({$user->name}): {$trans->transaction_code} | {$trans->status}\n";
}

echo "\n=== Test Complete ===\n";
