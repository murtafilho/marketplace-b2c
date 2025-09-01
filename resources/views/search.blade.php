@extends('layouts.base')

@section('title')
    @if($query)
        Busca por "{{ $query }}" - Resultados
    @else
        Todos os Produtos - Busca
    @endif
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm border-b sticky top-16 lg:top-16 z-40">
        <div class="px-4 py-3 sm:px-6 lg:px-8">
            <!-- Search Results Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">
                        @if($query)
                            <span class="block sm:inline">Busca por:</span>
                            <span class="block sm:inline text-emerald-600">"{{ $query }}"</span>
                        @else
                            Todos os produtos
                        @endif
                    </h1>
                </div>
                <div class="mt-2 sm:mt-0 sm:ml-4 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ $products->total() }} encontrado{{ $products->total() != 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            <!-- Search Bar -->
            @if($query)
                <div class="mt-4 max-w-lg">
                    <form action="{{ route('search') }}" method="GET" class="flex">
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="query" 
                                   value="{{ $query }}"
                                   placeholder="Buscar produtos..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <button type="submit" 
                                class="px-4 py-2 bg-emerald-600 text-white rounded-r-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-sm font-medium transition-colors">
                            Buscar
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if($products->count() > 0)
            <!-- Mobile-First Search Results Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach($products as $product)
                    <article class="bg-white rounded-lg shadow-sm border hover:shadow-lg transition-all duration-200 group">
                        <!-- Product Image -->
                        <a href="{{ route('product.show', $product->id) }}" class="block">
                            <div class="relative aspect-square overflow-hidden rounded-t-lg bg-gray-100">
                                @if($product->images && $product->images->count() > 0)
                                    <img src="{{ asset($product->images->first()->file_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-200"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Stock Badge -->
                                @if(isset($product->stock_quantity) && $product->stock_quantity <= 0)
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                        <span class="bg-red-600 text-white text-xs font-medium px-2 py-1 rounded">
                                            Esgotado
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        <!-- Product Info -->
                        <div class="p-3 sm:p-4">
                            <!-- Product Name -->
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors">
                                <a href="{{ route('product.show', $product->id) }}" class="hover:underline">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <!-- Seller Info -->
                            <div class="text-xs sm:text-sm text-gray-600 mb-2 truncate">
                                por {{ $product->seller->user->name ?? 'Vendedor' }}
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <div class="text-xs text-gray-500 line-through">
                                        R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                    </div>
                                @endif
                                <div class="text-sm sm:text-lg font-bold text-emerald-600">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('product.show', $product->id) }}" 
                               class="w-full bg-emerald-600 text-white text-center py-2 px-3 rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-xs sm:text-sm font-medium transition-colors touch-manipulation block">
                                Ver Produto
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Mobile-Optimized Pagination -->
            <div class="mt-8 sm:mt-12">
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 sm:py-20">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-16 w-16 sm:h-20 sm:w-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="mt-4 text-lg sm:text-xl font-semibold text-gray-900">
                        Nenhum produto encontrado
                    </h3>
                    <p class="mt-2 text-sm sm:text-base text-gray-500">
                        @if($query)
                            Não encontramos produtos para "<span class="font-medium text-gray-700">{{ $query }}</span>".
                            <br class="sm:hidden">
                            Tente outros termos ou navegue pelas categorias.
                        @else
                            Nenhum produto disponível no momento.
                        @endif
                    </p>
                    
                    <!-- Suggestions -->
                    @if($query)
                        <div class="mt-6 space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-center">
                            <a href="{{ route('search') }}" 
                               class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Ver Todos os Produtos
                            </a>
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Voltar ao Início
                            </a>
                        </div>
                    @else
                        <div class="mt-6">
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Voltar ao Início
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Touch Optimization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Touch feedback for mobile buttons
    const buttons = document.querySelectorAll('.touch-manipulation');
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        button.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });
    
    // Auto-focus search input on mobile when appropriate
    const searchInput = document.querySelector('input[name="query"]');
    if (searchInput && window.innerWidth > 768) { // Only on tablet/desktop
        searchInput.focus();
    }
});
</script>
@endsection