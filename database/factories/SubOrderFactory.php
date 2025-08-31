<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\SubOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubOrder>
 */
class SubOrderFactory extends Factory
{
    protected $model = SubOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'seller_id' => SellerProfile::factory(),
            'sub_order_number' => 'SUB-' . strtoupper($this->faker->bothify('???#####')),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => $this->faker->randomFloat(2, 10, 2000),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
            'total' => $this->faker->randomFloat(2, 10, 2050),
            'commission_rate' => $this->faker->randomFloat(2, 5, 15),
            'commission_amount' => $this->faker->randomFloat(2, 5, 200),
            'seller_amount' => $this->faker->randomFloat(2, 100, 1800),
            'tracking_code' => $this->faker->optional()->bothify('??########'),
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'delivered_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
