<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->bothify('???#####')),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => $this->faker->randomFloat(2, 10, 4500),
            'shipping_total' => $this->faker->randomFloat(2, 0, 50),
            'tax_total' => $this->faker->randomFloat(2, 0, 500),
            'discount_total' => $this->faker->randomFloat(2, 0, 200),
            'total' => $this->faker->randomFloat(2, 10, 5000),
            'currency' => 'BRL',
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'payment_method' => $this->faker->randomElement(['pix', 'credit_card', 'boleto']),
            'billing_address' => json_encode([
                'name' => $this->faker->name(),
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'state' => $this->faker->stateAbbr(),
                'postal_code' => $this->faker->postcode(),
            ]),
            'shipping_address' => json_encode([
                'name' => $this->faker->name(),
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'state' => $this->faker->stateAbbr(),
                'postal_code' => $this->faker->postcode(),
            ]),
            'customer_notes' => $this->faker->optional()->passthrough(json_encode(['note' => $this->faker->sentence()])),
            'mp_payment_id' => $this->faker->optional()->numerify('##########'),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
    }
}
