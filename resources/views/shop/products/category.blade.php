@extends('layouts.marketplace')

@section('title', $category->name . ' - Produtos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header da Categoria -->
    <div class="mb-8">
        <nav class="text-sm breadcrumbs mb-4">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Produtos</a>
            <span class="mx-2">/</span>
            <span class="text-gray-600">{{ $category->name }}</span>
        </nav>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-600">{{ $category->description }}</p>
        @endif
        <p class="text-sm text-gray-500 mt-2">{{ $products->total() }} produto(s) encontrado(s)</p>
    </div>

    @if($products->count() > 0)
        <!-- Grid de Produtos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                        
                        <!-- Vendedor -->
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                            <span>por {{ $product->seller->name }}</span>
                            @if($product->rating_average > 0)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1">{{ number_format($product->rating_average, 1) }}</span>
                                </div>
                            @endif
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

                        <!-- Botões -->
                        <div class="flex gap-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                                Ver Produto
                            </a>
                            
                            @if($product->stock_quantity > 0)
                                <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-sm">
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado nesta categoria</h3>
            <p class="mt-1 text-sm text-gray-500">
                Não há produtos disponíveis em <strong>{{ $category->name }}</strong> no momento.
            </p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ver Todos os Produtos
                </a>
            </div>
        </div>
    @endif
</div>
@endsection