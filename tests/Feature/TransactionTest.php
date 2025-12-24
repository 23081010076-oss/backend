<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a course transaction
     */
    public function test_can_create_course_transaction()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $course = Course::factory()->create([
            'price' => 100000,
        ]);

        $response = $this->postJson("/api/transactions/courses/{$course->id}", [
            'payment_method' => 'manual',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    'transaction' => [
                        'id',
                        'transaction_code',
                        'type',
                        'amount',
                        'payment_method',
                        'status',
                    ],
                    'instructions' => [
                        'bank_name',
                        'account_number',
                        'account_holder',
                        'instructions',
                    ],
                ],
            ])
            ->assertJson([
                'sukses' => true,
            ]);
    }

    public function test_can_list_transactions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/transactions', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test upload payment proof with valid image
     */
    public function test_can_upload_payment_proof_with_image()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        // Create a pending transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'manual',
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('payment_proof.jpg', 600, 400)->size(1024);

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
                'pesan' => 'Bukti pembayaran berhasil diupload',
            ]);

        // Verify file was stored
        Storage::disk('public')->assertExists('payment-proofs/' . basename($response->json('data.payment_proof')));
        
        // Verify transaction has payment_proof
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending', // Status tetap pending sampai admin konfirmasi
        ]);
        $transaction->refresh();
        $this->assertNotNull($transaction->payment_proof);
    }

    /**
     * Test upload payment proof with valid PDF
     */
    public function test_can_upload_payment_proof_with_pdf()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('payment_proof.pdf', 1024, 'application/pdf');

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'sukses' => true,
            ]);
    }

    /**
     * Test upload payment proof fails for non-manual payment
     */
    public function test_cannot_upload_payment_proof_for_non_manual_payment()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'virtual_account',
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('payment_proof.jpg');

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'sukses' => false,
            ]);
    }

    /**
     * Test upload payment proof fails for paid transaction
     */
    public function test_cannot_upload_payment_proof_for_paid_transaction()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'manual',
            'status' => 'paid',
        ]);

        $file = UploadedFile::fake()->image('payment_proof.jpg');

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Expect 403 karena Policy memblock transaksi yang bukan status pending
        $response->assertStatus(403);
    }

    /**
     * Test upload payment proof validation for file size
     */
    public function test_upload_payment_proof_fails_for_large_file()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'manual',
            'status' => 'pending',
        ]);

        // Create file larger than 5MB
        $file = UploadedFile::fake()->create('payment_proof.jpg', 6000);

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_proof']);
    }

    /**
     * Test old payment proof is deleted when uploading new one
     */
    public function test_old_payment_proof_deleted_on_new_upload()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        // Upload first proof
        $oldFile = UploadedFile::fake()->image('old_proof.jpg');
        $oldPath = $oldFile->store('payment-proofs', 'public');
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'manual',
            'status' => 'pending',
            'payment_proof' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // Upload new proof
        $newFile = UploadedFile::fake()->image('new_proof.jpg');

        $response = $this->postJson("/api/transactions/{$transaction->id}/payment-proof", [
            'payment_proof' => $newFile,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);
    }

    /**
     * Test admin can view all transactions
     */
    public function test_admin_can_view_all_transactions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $token = JWTAuth::fromUser($admin);
        
        // Create transactions for different users
        Transaction::factory()->create(['user_id' => $user1->id]);
        Transaction::factory()->create(['user_id' => $user2->id]);

        $response = $this->getJson('/api/transactions/admin/all', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sukses',
                'pesan',
                'data' => [
                    '*' => [
                        'id',
                        'transaction_code',
                        'status',
                        'user',
                    ]
                ],
                'meta'
            ]);
    }

    /**
     * Test admin can view pending verification transactions
     */
    public function test_admin_can_view_pending_verification_transactions()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($admin);
        
        // Create pending transaction with payment proof
        $file = UploadedFile::fake()->image('proof.jpg');
        $path = $file->store('payment-proofs', 'public');
        
        Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_proof' => $path,
        ]);
        
        // Create pending transaction without payment proof
        Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_proof' => null,
        ]);

        $response = $this->getJson('/api/transactions/admin/pending-verification', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        
        // Should only return 1 transaction (with payment proof)
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test non-admin cannot access admin endpoints
     */
    public function test_non_admin_cannot_view_admin_transactions()
    {
        $user = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/transactions/admin/all', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403);
    }
}
