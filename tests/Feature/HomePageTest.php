<?php
/**
 * Arquivo: tests/Feature/HomePageTest.php
 * Descrição: Teste da página inicial do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertSee('valedosol.org');
    }

    public function test_home_page_shows_categories(): void
    {
        // Criar categorias
        Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Category::create([
            'name' => 'Roupas',
            'slug' => 'roupas',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Category::create([
            'name' => 'Inativo',
            'slug' => 'inativo',
            'is_active' => false,
            'sort_order' => 3
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Eletrônicos');
        $response->assertSee('Roupas');
        $response->assertDontSee('Inativo');
    }

    public function test_home_page_shows_empty_state_when_no_products(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Nenhum produto em destaque');
        $response->assertSee('Seja o primeiro vendedor a cadastrar produtos');
    }

    public function test_navigation_links_are_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Entrar');
        $response->assertSee('Cadastrar');
    }

    public function test_authenticated_user_sees_different_navigation(): void
    {
        $user = User::factory()->create(['name' => 'João Silva']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('João Silva');
        $response->assertDontSee('Entrar');
        $response->assertDontSee('Cadastrar');
    }
}
