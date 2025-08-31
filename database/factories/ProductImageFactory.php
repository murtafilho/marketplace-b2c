<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'file_path' => 'products/' . $this->faker->uuid() . '.jpg',
            'original_name' => $this->faker->words(3, true) . '.jpg',
            'alt_text' => $this->faker->optional()->sentence(),
            'is_primary' => false,
            'sort_order' => $this->faker->numberBetween(1, 10),
            'file_size' => $this->faker->numberBetween(50000, 2000000),
            'mime_type' => 'image/jpeg',
            'width' => $this->faker->numberBetween(400, 1200),
            'height' => $this->faker->numberBetween(400, 1200),
        ];
    }

    /**
     * Indicate that the image is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 1,
        ]);
    }
}
