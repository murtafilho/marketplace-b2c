<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 200);
        $quantity = $this->faker->numberBetween(1, 5);
        
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'product_variation_id' => null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'product_snapshot' => json_encode([
                'name' => $this->faker->words(3, true),
                'description' => $this->faker->sentence(),
                'price' => $unitPrice,
                'images' => [$this->faker->imageUrl()],
            ]),
            'variation_snapshot' => null,
        ];
    }

    /**
     * Create cart item for specific cart
     */
    public function forCart(Cart $cart): static
    {
        return $this->state(fn (array $attributes) => [
            'cart_id' => $cart->id,
        ]);
    }

    /**
     * Create cart item for specific product
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
            'unit_price' => $product->price,
            'total_price' => $product->price * $attributes['quantity'],
            'product_snapshot' => json_encode([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'images' => $product->images->pluck('path')->toArray(),
            ]),
        ]);
    }

    /**
     * Create cart item with variation
     */
    public function withVariation($variationId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'product_variation_id' => $variationId ?? $this->faker->numberBetween(1, 100),
            'variation_snapshot' => json_encode([
                'size' => $this->faker->randomElement(['P', 'M', 'G', 'GG']),
                'color' => $this->faker->colorName(),
                'additional_price' => $this->faker->randomFloat(2, 0, 20),
            ]),
        ]);
    }

    /**
     * Create cart item with multiple quantity
     */
    public function withQuantity(int $quantity): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $quantity,
            'total_price' => $attributes['unit_price'] * $quantity,
        ]);
    }
}