@extends('layouts.marketplace')

@section('title', 'Produtos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar de Filtros -->
        <div class="md:w-1/4">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold mb-4">Filtros</h3>
                
                <form method="GET">
                    <!-- Busca -->
                    <div class="mb-4">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Buscar Produto
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nome do produto...">
                    </div>

                    <!-- Categoria -->
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoria
                        </label>
                        <select name="category" 
                                id="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas as categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Preço -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Faixa de Preço
                        </label>
                        <div class="flex gap-2">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}"
                                   placeholder="Min"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}"
                                   placeholder="Max"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Filtrar
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="md:w-3/4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
                    <p class="text-gray-600 mt-1">{{ $products->total() }} produto(s) encontrado(s)</p>
                </div>
                
                <!-- Ordenação -->
                <div>
                    <select name="sort" 
                            onchange="this.form.submit()" 
                            form="filter-form"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais Recentes</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Menor Preço</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Maior Preço</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Mais Populares</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Melhor Avaliado</option>
                    </select>
                </div>
            </div>

            @if($products->count() > 0)
                <!-- Grid de Produtos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                            <!-- Imagem -->
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg">
                                @if($product->images->count() > 0)
                                    <img src="{{ Storage::url($product->images->first()->file_path) }}" 
                                         alt="{{ $product->name }}"
                                         class="h-48 w-full object-cover object-center">
                                @else
                                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <!-- Nome -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                
                                <!-- Categoria e Vendedor -->
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                    <span class="bg-gray-100 px-2 py-1 rounded-full">{{ $product->category->name }}</span>
                                    <span>por {{ $product->seller->name }}</span>
                                </div>

                                <!-- Preço -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                            <span class="text-sm text-gray-500 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</span>
                                        @endif
                                        <span class="text-xl font-bold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    @if($product->stock_quantity > 0)
                                        <span class="text-green-600 text-sm font-medium">Em estoque</span>
                                    @else
                                        <span class="text-red-600 text-sm font-medium">Fora de estoque</span>
                                    @endif
                                </div>

                                <!-- Avaliação -->
                                @if($product->rating_average > 0)
                                    <div class="flex items-center mb-3">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $product->rating_average)
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">({{ $product->rating_count }})</span>
                                    </div>
                                @endif

                                <!-- Botões -->
                                <div class="flex gap-2">
                                    <a href="{{ route('products.show', $product) }}" 
                                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Ver Produto
                                    </a>
                                    
                                    @if($product->stock_quantity > 0)
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" 
                                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                + Carrinho
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.287 0-4.33-.919-5.83-2.413" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros de busca.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script para filtro em tempo real -->
<form id="filter-form" method="GET" style="display: none;">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="category" value="{{ request('category') }}">
    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
</form>
@endsection