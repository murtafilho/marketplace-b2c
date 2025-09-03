<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => $this->faker->uuid(),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'total_items' => $this->faker->numberBetween(1, 10),
            'shipping_data' => json_encode([
                'method' => $this->faker->randomElement(['correios', 'transportadora']),
                'cost' => $this->faker->randomFloat(2, 10, 50),
                'days' => $this->faker->numberBetween(1, 10)
            ]),
            'coupon_data' => $this->faker->optional()->passthrough(json_encode([
                'code' => strtoupper($this->faker->lexify('???###')),
                'discount' => $this->faker->randomFloat(2, 5, 50),
                'type' => $this->faker->randomElement(['fixed', 'percentage'])
            ])),
            'expires_at' => $this->faker->dateTimeBetween('now', '+30 days'),
        ];
    }

    /**
     * Create a cart with specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a cart for guest (session-based)
     */
    public function forGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Create an expired cart
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}