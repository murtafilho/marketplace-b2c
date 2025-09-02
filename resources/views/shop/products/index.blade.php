@extends('layouts.base')

@section('title', 'Produtos - valedosol.org')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header com Busca -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-verde-mata mb-4">
            @if(request('search'))
                Resultados para: "{{ request('search') }}"
            @elseif(request('category'))
                {{ $categories->find(request('category'))->name ?? 'Produtos' }}
            @else
                Todos os Produtos
            @endif
        </h1>
        
        <!-- Barra de Busca e Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                <!-- Campo de Busca -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Buscar produtos..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-suave pr-12">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-verde-suave hover:text-verde-mata">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Categoria -->
                    <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-suave">
                        <option value="">Todas as Categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Preço Mínimo -->
                    <input type="number" 
                           name="min_price" 
                           value="{{ request('min_price') }}"
                           placeholder="Preço mínimo" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-suave">
                    
                    <!-- Preço Máximo -->
                    <input type="number" 
                           name="max_price" 
                           value="{{ request('max_price') }}"
                           placeholder="Preço máximo" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-suave">
                    
                    <!-- Ordenação -->
                    <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-suave">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais recentes</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Menor preço</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Maior preço</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Mais populares</option>
                    </select>
                </div>
                
                <!-- Botões -->
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-verde-suave text-white rounded-lg hover:bg-verde-mata transition duration-300">
                        Aplicar Filtros
                    </button>
                    <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300">
                        Limpar Filtros
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Contador de Resultados -->
        <p class="text-gray-600">
            Encontramos <span class="font-semibold text-verde-mata">{{ $products->total() }}</span> produto(s)
        </p>
    </div>
    
    <!-- Grid de Produtos -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 overflow-hidden group">
                    <a href="{{ route('products.show', $product) }}">
                        <!-- Imagem do Produto -->
                        <div class="relative aspect-square bg-gray-100">
                            @if($product->hasImages())
                                <img src="{{ $product->primary_image_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @elseif($product->images && $product->images->count() > 0)
                                <img src="{{ Storage::url($product->images->first()->file_path ?? $product->images->first()->path) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                @php
                                    $discount = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
                                @endphp
                                <span class="absolute top-2 left-2 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    -{{ $discount }}%
                                </span>
                            @endif
                            
                            @if($product->stock_quantity <= 0)
                                <span class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Esgotado
                                </span>
                            @elseif($product->stock_quantity <= 5)
                                <span class="absolute top-2 right-2 bg-yellow-600 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    Últimas unidades
                                </span>
                            @endif
                        </div>
                        
                        <!-- Informações do Produto -->
                        <div class="p-4">
                            <!-- Categoria -->
                            <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Categoria' }}</p>
                            
                            <!-- Nome -->
                            <h3 class="font-semibold text-verde-mata mb-2 line-clamp-2">{{ $product->name }}</h3>
                            
                            <!-- Vendedor -->
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="text-xs">por</span> {{ $product->seller->store_name ?? $product->seller->name ?? 'Vendedor' }}
                            </p>
                            
                            <!-- Preço -->
                            <div class="flex items-baseline gap-2">
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span class="text-sm text-gray-400 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</span>
                                @endif
                                <span class="text-xl font-bold text-orange-500">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            
                            <!-- Rating -->
                            @if(isset($product->rating_average) && $product->rating_average > 0)
                                <div class="flex items-center mt-2">
                                    @for($star = 1; $star <= 5; $star++)
                                        <svg class="h-4 w-4 {{ $star <= $product->rating_average ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    @if(isset($product->rating_count) && $product->rating_count > 0)
                                        <span class="ml-1 text-xs text-gray-500">({{ $product->rating_count }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <!-- Paginação -->
        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- Mensagem quando não há produtos -->
        <div class="text-center py-16">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum produto encontrado</h3>
            <p class="text-gray-500 mb-6">Tente ajustar seus filtros ou fazer uma nova busca</p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-verde-suave text-white rounded-lg hover:bg-verde-mata transition duration-300">
                Ver todos os produtos
            </a>
        </div>
    @endif
</div>
@endsection