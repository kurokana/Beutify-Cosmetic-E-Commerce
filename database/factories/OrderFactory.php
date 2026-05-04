<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(50000, 500000);
        $shippingCost = fake()->numberBetween(10000, 50000);
        $discountAmount = 0;
        $totalAmount = $subtotal + $shippingCost - $discountAmount;

        return [
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'courier_name' => fake()->randomElement(['jne', 'jnt', 'sicepat']),
            'courier_service' => fake()->randomElement(['REG', 'YES', 'OKE']),
            'shipping_cost' => $shippingCost,
            'shipping_tracking_number' => null,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'voucher_id' => null,
            'status' => 'pending_payment',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'payment_confirmed',
        ]);
    }

    /**
     * Indicate that the order is shipped.
     */
    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
            'shipping_tracking_number' => fake()->numerify('JNE##########'),
        ]);
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'shipping_tracking_number' => fake()->numerify('JNE##########'),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
