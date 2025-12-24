<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomElement([50000, 100000, 150000, 200000, 500000]);
        
        return [
            'user_id' => User::factory(),
            'transaction_code' => 'TRX-' . strtoupper($this->faker->unique()->bothify('??###??###')),
            'type' => $this->faker->randomElement(['course_enrollment', 'subscription', 'mentoring_session']),
            'transactionable_type' => Course::class,
            'transactionable_id' => Course::factory(),
            'amount' => $amount,
            'payment_method' => $this->faker->randomElement(['manual', 'bank_transfer', 'virtual_account', 'credit_card', 'qris']),
            'status' => $this->faker->randomElement(['pending', 'paid', 'expired', 'refunded']),
            'payment_proof' => null,
            'payment_details' => null,
            'paid_at' => null,
            'expired_at' => now()->addDays(1),
        ];
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the transaction is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the transaction uses manual payment.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'manual',
        ]);
    }

    /**
     * Indicate that the transaction uses bank transfer.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
        ]);
    }
}
