<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Apartemen']),
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'province' => fake()->randomElement(['DKI Jakarta', 'Jawa Barat', 'Jawa Timur', 'Banten']),
            'city' => fake()->city(),
            'district' => fake()->streetName(),
            'postal_code' => fake()->numerify('#####'),
            'full_address' => fake()->address(),
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the address is the default address.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
