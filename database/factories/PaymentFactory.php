<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->numberBetween(50000, 500000);

        return [
            'order_id' => Order::factory(),
            'midtrans_order_id' => 'MIDTRANS-' . fake()->unique()->numerify('##########'),
            'midtrans_transaction_id' => null,
            'payment_method' => null,
            'payment_type' => null,
            'amount' => $amount,
            'status' => 'pending',
            'snap_token' => fake()->uuid(),
            'paid_at' => null,
            'expired_at' => now()->addHours(24),
        ];
    }

    /**
     * Indicate that the payment is successful.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'payment_method' => fake()->randomElement(['BCA Virtual Account', 'BNI Virtual Account', 'GoPay', 'OVO']),
            'payment_type' => fake()->randomElement(['bank_transfer', 'gopay', 'shopeepay']),
            'midtrans_transaction_id' => fake()->uuid(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the payment has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the payment has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expired_at' => now()->subHours(1),
        ]);
    }
}
