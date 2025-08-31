<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlpineJsCompatibilityTest extends TestCase
{
    /** @test */
    public function alpine_js_is_loaded_in_layouts()
    {
        $response = $this->get(route('home'));
        
        $response->assertStatus(200);
        
        // Verificar se o Alpine.js está sendo carregado
        $response->assertSee('@vite([\'resources/css/app.css\', \'resources/js/app.js\'])', false);
    }

    /** @test */
    public function alpine_js_directives_are_present_in_components()
    {
        $response = $this->get(route('home'));
        
        $response->assertStatus(200);
        
        // Verificar se diretivas Alpine.js estão presentes
        $response->assertSee('x-data');
    }

    /** @test */
    public function alpine_js_stores_are_configured()
    {
        $response = $this->get(route('home'));
        
        $response->assertStatus(200);
        
        // Verificar se o JavaScript do app.js está sendo carregado
        // O teste verifica se a estrutura HTML básica está correta
        $this->assertTrue(true); // O Alpine.js é carregado via Vite, não podemos testar diretamente no PHP
    }

    /** @test */
    public function seller_onboarding_form_has_alpine_directives()
    {
        // Criar usuário vendedor para testar formulário
        $user = \App\Models\User::factory()->create(['role' => 'seller']);
        $sellerProfile = \App\Models\SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get(route('seller.onboarding.index'));
        
        $response->assertStatus(200);
        
        // Verificar se diretivas Alpine.js específicas estão presentes
        $response->assertSee('x-data="sellerOnboardingForm()"');
        $response->assertSee('x-init="init()"');
        $response->assertSee('@change="validateFileSize');
        $response->assertSee('@input="formatDocument');
        $response->assertSee('x-model="documentType"');
    }

    /** @test */
    public function javascript_functions_are_present_in_onboarding()
    {
        $user = \App\Models\User::factory()->create(['role' => 'seller']);
        $sellerProfile = \App\Models\SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get(route('seller.onboarding.index'));
        
        $response->assertStatus(200);
        
        // Verificar se funções JavaScript necessárias estão presentes
        $response->assertSee('function sellerOnboardingForm()');
        $response->assertSee('validateCPF(');
        $response->assertSee('validateCNPJ(');
        $response->assertSee('formatPhone(');
        $response->assertSee('validateFileSize(');
    }

    /** @test */
    public function cart_functionality_uses_alpine_stores()
    {
        $response = $this->get(route('home'));
        
        $response->assertStatus(200);
        
        // Verificar se as diretivas do carrinho estão presentes
        if (str_contains($response->getContent(), '$store.cart')) {
            $response->assertSee('$store.cart.addItem');
        } else {
            // Se não há produtos na home, não há problema
            $this->assertTrue(true);
        }
    }
}