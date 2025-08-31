<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        $defaultImages = [
            'eletronicos' => 'categories/eletronicos.jpg',
            'roupas-e-acessorios' => 'categories/roupas-acessorios.jpg',
            'moda-e-vestuario' => 'categories/moda-vestuario.jpg',
            'casa-e-jardim' => 'categories/casa-jardim.jpg',
            'casa-e-decoracao' => 'categories/casa-decoracao.jpg',
            'esportes-e-fitness' => 'categories/esportes-fitness.jpg',
            'esportes-e-lazer' => 'categories/esportes-lazer.jpg',
            'beleza-e-cuidados' => 'categories/beleza-cuidados.jpg',
            'automotivo' => 'categories/automotivo.jpg',
            'livros-e-educacao' => 'categories/livros-educacao.jpg',
            'livros-e-papelaria' => 'categories/livros-papelaria.jpg',
            'games-e-entretenimento' => 'categories/games-entretenimento.jpg',
            'alimentos-e-bebidas' => 'categories/alimentos-bebidas.jpg',
            'pet-shop' => 'categories/pet-shop.jpg',
        ];

        foreach ($defaultImages as $slug => $imagePath) {
            Category::where('slug', $slug)->update([
                'image_path' => $imagePath
            ]);
        }
    }

    public function down(): void
    {
        Category::whereNotNull('image_path')->update([
            'image_path' => null
        ]);
    }
};