<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $quantity = $this->faker->numberBetween(1, 5);
        $totalPrice = $unitPrice * $quantity;
        $commissionRate = $this->faker->randomFloat(2, 5, 15);
        $commissionAmount = $totalPrice * ($commissionRate / 100);
        $sellerAmount = $totalPrice - $commissionAmount;
        
        return [
            'order_id' => Order::factory(),
            'sub_order_id' => function (array $attributes) {
                return \App\Models\SubOrder::factory()->create([
                    'order_id' => $attributes['order_id']
                ])->id;
            },
            'product_id' => Product::factory(),
            'product_variation_id' => null,
            'product_name' => $this->faker->words(3, true),
            'product_sku' => $this->faker->optional()->bothify('SKU-########'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'product_snapshot' => json_encode([
                'name' => $this->faker->words(3, true),
                'description' => $this->faker->paragraph(),
                'sku' => $this->faker->bothify('SKU-########'),
                'brand' => $this->faker->optional()->company(),
                'weight' => $this->faker->optional()->randomFloat(2, 0.1, 10),
                'dimensions' => $this->faker->optional()->words(3, true),
            ]),
            'variation_snapshot' => null,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'seller_amount' => $sellerAmount,
        ];
    }
}
