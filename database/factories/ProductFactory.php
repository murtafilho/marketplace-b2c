<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        
        return [
            'seller_id' => SellerProfile::factory(),
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => $this->faker->paragraphs(3, true),
            'short_description' => $this->faker->sentence(20),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'compare_at_price' => null,
            'cost' => $this->faker->randomFloat(2, 5, 500),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'barcode' => $this->faker->optional()->ean13(),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'stock_status' => $this->faker->randomElement(['in_stock', 'out_of_stock', 'backorder']),
            'weight' => $this->faker->optional()->randomFloat(3, 0.1, 50),
            'length' => $this->faker->optional()->randomFloat(2, 1, 100),
            'width' => $this->faker->optional()->randomFloat(2, 1, 100),
            'height' => $this->faker->optional()->randomFloat(2, 1, 100),
            'status' => $this->faker->randomElement(['draft', 'active', 'inactive']),
            'featured' => $this->faker->boolean(20), // 20% chance
            'digital' => $this->faker->boolean(10), // 10% chance
            'downloadable_files' => null,
            'meta_title' => $this->faker->optional()->sentence(6),
            'meta_description' => $this->faker->optional()->sentence(15),
            'meta_keywords' => $this->faker->optional()->words(10, true),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year'),
            'brand' => $this->faker->optional()->company(),
            'model' => $this->faker->optional()->bothify('Model-###-??'),
            'warranty_months' => $this->faker->optional()->numberBetween(0, 24),
            'tags' => $this->faker->optional()->passthrough(json_encode($this->faker->words(rand(1, 5)))),
            'attributes' => $this->faker->optional()->passthrough(json_encode([
                'color' => $this->faker->safeColorName(),
                'material' => $this->faker->randomElement(['Metal', 'Plastic', 'Wood', 'Glass', 'Fabric'])
            ])),
            'dimensions' => $this->faker->optional()->passthrough(json_encode([
                'length' => $this->faker->randomFloat(2, 10, 200),
                'width' => $this->faker->randomFloat(2, 10, 150),
                'height' => $this->faker->randomFloat(2, 5, 100)
            ])),
            'shipping_class' => $this->faker->optional()->randomElement(['standard', 'express', 'free', 'heavy']),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'sales_count' => $this->faker->numberBetween(0, 100),
            'rating_average' => $this->faker->optional(0.7)->randomFloat(2, 1, 5),
            'rating_count' => $this->faker->numberBetween(0, 50),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'digital' => true,
            'weight' => null,
            'length' => null,
            'width' => null,
            'height' => null,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'stock_status' => 'in_stock',
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'stock_status' => 'out_of_stock',
        ]);
    }
}