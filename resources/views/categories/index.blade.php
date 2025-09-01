@extends('layouts.base')

@section('title', 'Categorias - Marketplace')

@section('content')
<!-- Categories Page - Mobile First -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">Todas as Categorias</h1>
        <p class="mt-2 text-lg text-gray-600">Explore produtos por categoria</p>
    </div>
    
    <!-- Categories Grid - Mobile First -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:gap-6 lg:grid-cols-4 xl:grid-cols-6">
        @foreach(['Eletrônicos', 'Moda', 'Casa', 'Esportes', 'Livros', 'Beleza', 'Automóveis', 'Saúde', 'Brinquedos', 'Música', 'Arte', 'Jardim'] as $category)
            <a href="{{ route('products.index', ['category' => strtolower($category)]) }}" 
               class="group relative overflow-hidden rounded-lg bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                
                <!-- Category Icon Placeholder -->
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-lg bg-emerald-100 group-hover:bg-emerald-200 transition-colors
                           sm:h-20 sm:w-20">
                    <span class="text-2xl sm:text-3xl">
                        @if($category === 'Eletrônicos') 📱
                        @elseif($category === 'Moda') 👕
                        @elseif($category === 'Casa') 🏠
                        @elseif($category === 'Esportes') ⚽
                        @elseif($category === 'Livros') 📚
                        @elseif($category === 'Beleza') 💄
                        @elseif($category === 'Automóveis') 🚗
                        @elseif($category === 'Saúde') 🏥
                        @elseif($category === 'Brinquedos') 🧸
                        @elseif($category === 'Música') 🎵
                        @elseif($category === 'Arte') 🎨
                        @elseif($category === 'Jardim') 🌱
                        @endif
                    </span>
                </div>
                
                <!-- Category Name -->
                <h3 class="text-sm font-medium text-gray-900 group-hover:text-emerald-600 transition-colors
                           sm:text-base">
                    {{ $category }}
                </h3>
                
                <!-- Product Count -->
                <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                    {{ rand(5, 50) }} produtos
                </p>
            </a>
        @endforeach
    </div>
    
    <!-- Popular Categories Section -->
    <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Categorias Populares</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach(['Eletrônicos', 'Moda', 'Casa'] as $popular)
                <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 p-6 text-white">
                    <h3 class="text-lg font-medium">{{ $popular }}</h3>
                    <p class="mt-2 text-sm opacity-90">Produtos em alta</p>
                    <div class="mt-4">
                        <a href="{{ route('products.index', ['category' => strtolower($popular)]) }}" 
                           class="inline-flex items-center rounded-md bg-white/20 px-3 py-2 text-sm font-medium text-white hover:bg-white/30 transition-colors">
                            Ver produtos
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection