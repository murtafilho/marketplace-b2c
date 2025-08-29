@extends('layouts.marketplace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-600 mt-2">{{ $category->description }}</p>
        @endif
        <p class="text-gray-500 mt-1">{{ $products->total() }} produtos disponíveis</p>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
            <a href="{{ route('product.show', $product->id) }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden block">
                <div class="aspect-square bg-gray-200 relative">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ asset($product->images->first()->file_path) }}" 
                             alt="{{ $product->name }}" 
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
                    <h3 class="font-medium text-gray-800 mb-2 truncate">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $product->seller->user->name ?? 'Vendedor' }}</p>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <div class="text-sm text-gray-500 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</div>
                            @endif
                            <div class="text-lg font-bold text-blue-600">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @else
        <!-- Estado vazio -->
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum produto nesta categoria</h3>
            <p class="text-gray-500 mb-6">Esta categoria ainda não possui produtos cadastrados.</p>
            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Voltar ao início
            </a>
        </div>
    @endif
</div>
@endsection