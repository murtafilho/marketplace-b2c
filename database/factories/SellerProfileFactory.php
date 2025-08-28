<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerProfile>
 */
class SellerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentType = $this->faker->randomElement(['CPF', 'CNPJ']);
        
        return [
            'user_id' => User::factory(),
            'document_type' => $documentType,
            'document_number' => $documentType === 'CPF' 
                ? $this->faker->numerify('###.###.###-##')
                : $this->faker->numerify('##.###.###/####-##'),
            'company_name' => $this->faker->company(),
            'address_proof_path' => null,
            'identity_proof_path' => null,
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'bank_name' => $this->faker->company() . ' Bank',
            'bank_account' => $this->faker->numerify('####-######-#'),
            'status' => $this->faker->randomElement(['pending', 'pending_approval', 'approved', 'rejected', 'suspended']),
            'rejection_reason' => null,
            'commission_rate' => $this->faker->randomFloat(2, 5, 20),
            'product_limit' => $this->faker->numberBetween(50, 500),
            'mp_access_token' => null,
            'mp_user_id' => null,
            'mp_connected' => false,
            'approved_at' => null,
            'submitted_at' => null,
        ];
    }

    /**
     * Indicate that the seller is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the seller is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_approval',
        ]);
    }

    /**
     * Indicate that the seller is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the seller is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}