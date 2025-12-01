<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

/**
 * ==========================================================================
 * TRANSACTION POLICY (Aturan Akses untuk Transaksi)
 * ==========================================================================
 * 
 * FUNGSI: Mengatur siapa yang boleh melakukan apa terhadap transaksi.
 * 
 * ATURAN:
 * - User hanya bisa lihat transaksinya sendiri
 * - Admin bisa lihat dan konfirmasi semua transaksi
 * - Hanya pemilik transaksi yang bisa upload bukti bayar dan request refund
 */
class TransactionPolicy
{
    /**
     * Apakah user boleh melihat daftar transaksi?
     * → Semua user yang login boleh (tapi hanya transaksinya sendiri)
     * → Admin bisa lihat semua
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh melihat detail transaksi?
     * → Pemilik atau admin boleh
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id 
            || $user->role === 'admin';
    }

    /**
     * Apakah user boleh membuat transaksi?
     * → Semua user yang login boleh
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Apakah user boleh upload bukti bayar?
     * → Hanya pemilik transaksi dan status pending
     */
    public function uploadProof(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id 
            && $transaction->status === 'pending';
    }

    /**
     * Apakah user boleh konfirmasi pembayaran?
     * → Hanya admin boleh
     */
    public function confirmPayment(User $user, Transaction $transaction): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apakah user boleh request refund?
     * → Pemilik transaksi dengan status paid
     */
    public function requestRefund(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id 
            && $transaction->status === 'paid';
    }

    /**
     * Apakah user boleh melihat statistik transaksi?
     * → Hanya admin boleh
     */
    public function viewStatistics(User $user): bool
    {
        return $user->role === 'admin';
    }
}
