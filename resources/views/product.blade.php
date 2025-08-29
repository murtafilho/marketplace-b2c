@extends('layouts.marketplace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="md:flex">
            <!-- Imagem do produto -->
            <div class="md:w-1/2">
                @if($product->images && $product->images->count() > 0)
                    <img src="{{ asset($product->images->first()->file_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-96 md:h-full object-cover">
                @else
                    <div class="w-full h-96 md:h-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Informações do produto -->
            <div class="md:w-1/2 p-8">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <p class="text-gray-600">Por {{ $product->seller->user->name }}</p>
                </div>
                
                <div class="mb-6">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="text-lg text-gray-500 line-through mb-1">
                            De R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                        </div>
                    @endif
                    <div class="text-3xl font-bold text-blue-600">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </div>
                </div>
                
                @if($product->short_description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Descrição</h3>
                        <p class="text-gray-700">{{ $product->short_description }}</p>
                    </div>
                @endif
                
                <div class="mb-6">
                    <p class="text-sm text-gray-500">
                        <span class="font-medium">Categoria:</span> {{ $product->category->name ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        <span class="font-medium">Estoque:</span> {{ $product->stock_quantity }} unidades
                    </p>
                </div>
                
                <div class="flex space-x-4">
                    <button class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium"
                            onclick="alert('Funcionalidade de carrinho em desenvolvimento')">
                        Adicionar ao Carrinho
                    </button>
                    <button class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50"
                            onclick="alert('Funcionalidade de favoritos em desenvolvimento')">
                        ♡
                    </button>
                </div>
            </div>
        </div>
        
        @if($product->description)
            <div class="border-t p-8">
                <h3 class="text-xl font-semibold mb-4">Descrição Completa</h3>
                <div class="text-gray-700 whitespace-pre-wrap">{{ $product->description }}</div>
            </div>
        @endif
    </div>
    
    @if($relatedProducts->count() > 0)
        <!-- Produtos Relacionados -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Produtos Relacionados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <a href="{{ route('product.show', $related->id) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden block">
                    <div class="aspect-square bg-gray-200 relative">
                        @if($related->images && $related->images->count() > 0)
                            <img src="{{ asset($related->images->first()->file_path) }}" 
                                 alt="{{ $related->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-medium text-gray-800 mb-2 truncate">{{ $related->name }}</h3>
                        <div class="text-lg font-bold text-blue-600">
                            R$ {{ number_format($related->price, 2, ',', '.') }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection