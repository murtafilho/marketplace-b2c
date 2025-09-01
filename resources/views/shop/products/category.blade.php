@extends('layouts.base')

@section('title', $category->name . ' - Produtos')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm border-b sticky top-16 lg:top-16 z-40">
        <div class="px-4 py-3 sm:px-6 lg:px-8">
            <!-- Mobile Breadcrumb -->
            <nav class="text-xs sm:text-sm text-gray-600 mb-2 overflow-x-auto whitespace-nowrap" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                    Home
                </a>
                <span class="mx-1 sm:mx-2 text-gray-400">/</span>
                <a href="{{ route('products.index') }}" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                    Produtos
                </a>
                <span class="mx-1 sm:mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">{{ $category->name }}</span>
            </nav>
            
            <!-- Category Title and Info -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">
                        {{ $category->name }}
                    </h1>
                    @if($category->description)
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="mt-2 sm:mt-0 sm:ml-4 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ $products->total() }} produto{{ $products->total() != 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if($products->count() > 0)
            <!-- Mobile-First Products Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach($products as $product)
                    <article class="bg-white rounded-lg shadow-sm border hover:shadow-lg transition-all duration-200 group">
                        <!-- Product Image -->
                        <div class="relative aspect-square overflow-hidden rounded-t-lg bg-gray-100">
                            @if($product->images->count() > 0)
                                <img src="{{ Storage::url($product->images->first()->file_path) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-200"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Stock Badge -->
                            @if($product->stock_quantity <= 0)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <span class="bg-red-600 text-white text-xs font-medium px-2 py-1 rounded">
                                        Esgotado
                                    </span>
                                </div>
                            @elseif($product->stock_quantity <= 5)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-yellow-500 text-white text-xs font-medium px-2 py-1 rounded">
                                        Últimas {{ $product->stock_quantity }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-3 sm:p-4">
                            <!-- Product Name -->
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 sm:mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors">
                                <a href="{{ route('products.show', $product) }}" class="hover:underline">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <!-- Seller Info -->
                            <div class="flex items-center justify-between text-xs sm:text-sm text-gray-600 mb-2">
                                <span class="truncate">{{ $product->seller->name }}</span>
                                @if($product->rating_average > 0)
                                    <div class="flex items-center ml-2">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-yellow-400 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="ml-1 text-xs">{{ number_format($product->rating_average, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <div class="text-xs text-gray-500 line-through">
                                        R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                    </div>
                                @endif
                                <div class="text-sm sm:text-lg font-bold text-gray-900">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <a href="{{ route('products.show', $product) }}" 
                                   class="w-full bg-emerald-600 text-white text-center py-2 px-3 rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-xs sm:text-sm font-medium transition-colors touch-manipulation">
                                    Ver Produto
                                </a>
                                
                                @if($product->stock_quantity > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" 
                                                class="w-full bg-gray-100 text-gray-700 py-2 px-3 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-xs sm:text-sm font-medium transition-colors touch-manipulation">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                                                </svg>
                                                Carrinho
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            </div>
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
                    <svg class="mx-auto h-16 w-16 sm:h-20 sm:w-20 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="mt-4 text-lg sm:text-xl font-semibold text-gray-900">
                        Nenhum produto encontrado
                    </h3>
                    <p class="mt-2 text-sm sm:text-base text-gray-500">
                        Não há produtos disponíveis em <span class="font-medium">{{ $category->name }}</span> no momento.
                    </p>
                    <div class="mt-6 space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-center">
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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

    // Add to cart form handling with loading states
    const cartForms = document.querySelectorAll('.add-to-cart-form');
    cartForms.forEach(form => {
        form.addEventListener('submit', function() {
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Adicionando...</span>';
            
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 2000);
        });
    });
});
</script>
@endsection