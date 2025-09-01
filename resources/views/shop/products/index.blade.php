@extends('layouts.base')

@section('title', 'Produtos - Marketplace')

@section('content')
<!-- Mobile-First Products Page -->
<div class="space-y-6">
    
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Produtos</h1>
            <p class="mt-1 text-sm text-gray-600">
                Mostrando {{ $products->count() }} de {{ $products->total() }} produtos
            </p>
        </div>
        
        <!-- Mobile Search Bar -->
        <div class="w-full sm:w-80">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Buscar produtos..." 
                           class="block w-full rounded-lg border-0 py-3 pl-10 pr-3 ring-1 ring-inset ring-gray-300 
                                  placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-600 
                                  text-sm sm:text-base">
                    <button type="submit" class="sr-only">Buscar</button>
                </div>
                <!-- Preserve other filters -->
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('min_price'))
                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                @endif
                @if(request('max_price'))
                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
            </form>
        </div>
    </div>
    
    <!-- Filters Section - Mobile Collapsible -->
    <div class="border-b border-gray-200 pb-6">
        <!-- Mobile Filter Toggle -->
        <div class="sm:hidden">
            <button type="button" 
                    onclick="toggleMobileFilters()"
                    class="flex w-full items-center justify-between rounded-lg bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700">
                <span>Filtros</span>
                <svg id="filter-chevron" class="h-5 w-5 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </div>
        
        <!-- Filters Content -->
        <div id="mobile-filters" class="hidden space-y-4 pt-4 sm:block sm:pt-0">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4 sm:flex sm:space-y-0 sm:space-x-6">
                <!-- Preserve search term -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                <!-- Category Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Categoria</label>
                    <select name="category" 
                            onchange="this.form.submit()"
                            class="block w-full rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 
                                   focus:ring-2 focus:ring-emerald-600 text-sm
                                   sm:w-48">
                        <option value="">Todas as categorias</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- Price Range -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Preço</label>
                    <div class="flex gap-2">
                        <input type="number" 
                               name="min_price" 
                               value="{{ request('min_price') }}"
                               placeholder="Min"
                               class="block w-1/2 rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 
                                      focus:ring-2 focus:ring-emerald-600 text-sm">
                        <input type="number" 
                               name="max_price" 
                               value="{{ request('max_price') }}"
                               placeholder="Max"
                               class="block w-1/2 rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 
                                      focus:ring-2 focus:ring-emerald-600 text-sm">
                    </div>
                </div>
                
                <!-- Sort -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Ordenar por</label>
                    <select name="sort" 
                            onchange="this.form.submit()"
                            class="block w-full rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 
                                   focus:ring-2 focus:ring-emerald-600 text-sm
                                   sm:w-48">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mais recentes</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Menor preço</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Maior preço</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Mais populares</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Melhor avaliado</option>
                    </select>
                </div>
                
                <!-- Apply Filters Button (Mobile Only) -->
                <div class="sm:hidden pt-2">
                    <button type="submit" 
                            class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white hover:bg-emerald-700">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Summary and View Toggle -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-600">
            @if(request('search'))
                Resultados para "<span class="font-medium">{{ request('search') }}</span>"
            @endif
        </p>
        
        <!-- View Toggle (Grid/List) - Desktop Only -->
        <div class="hidden sm:flex sm:items-center sm:space-x-2">
            <span class="text-sm text-gray-500">Visualização:</span>
            <div class="flex rounded-lg bg-gray-100 p-1">
                <button type="button" 
                        onclick="setView('grid')"
                        id="grid-btn"
                        class="rounded px-3 py-1 text-sm font-medium text-gray-700 bg-white shadow-sm">
                    Grade
                </button>
                <button type="button" 
                        onclick="setView('list')"
                        id="list-btn"
                        class="rounded px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Lista
                </button>
            </div>
        </div>
    </div>
    
    @if($products->count() > 0)
        <!-- Products Grid - Mobile First Responsive -->
        <div class="grid gap-4 sm:gap-6">
            <!-- Mobile: 2 columns, Tablet: 3 columns, Desktop: 4 columns -->
            <div id="products-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4 xl:grid-cols-5">
                @foreach($products as $product)
                    <div class="group relative bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <!-- Product Image -->
                        <div class="aspect-square w-full overflow-hidden rounded-t-lg bg-gray-200">
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ Storage::url($product->images->first()->file_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gray-100">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Mobile CTA Button -->
                            <div class="absolute inset-x-2 bottom-2 sm:hidden">
                                @if($product->stock_quantity > 0)
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                            Adicionar
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="w-full rounded-lg bg-gray-400 py-2 text-sm font-medium text-white">
                                        Indisponível
                                    </button>
                                @endif
                            </div>
                            
                            <!-- Desktop Hover Actions -->
                            <div class="absolute inset-x-2 bottom-2 hidden gap-2 opacity-0 transition-opacity group-hover:opacity-100 sm:flex">
                                <a href="{{ route('products.show', $product) }}" 
                                   class="flex-1 rounded-lg bg-white py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Ver
                                </a>
                                @if($product->stock_quantity > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                            Carrinho
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <!-- Stock Badge -->
                            @if($product->stock_quantity <= 0)
                                <div class="absolute left-2 top-2">
                                    <span class="rounded-full bg-red-600 px-2 py-1 text-xs font-medium text-white">
                                        Esgotado
                                    </span>
                                </div>
                            @elseif($product->stock_quantity <= 5)
                                <div class="absolute left-2 top-2">
                                    <span class="rounded-full bg-yellow-600 px-2 py-1 text-xs font-medium text-white">
                                        Últimas unidades
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Discount Badge -->
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                @php
                                    $discount = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
                                @endphp
                                <div class="absolute right-2 top-2">
                                    <span class="rounded-full bg-red-600 px-2 py-1 text-xs font-medium text-white">
                                        -{{ $discount }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-3 space-y-2">
                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
                                <a href="{{ route('products.show', $product) }}" class="hover:text-emerald-600">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Categoria' }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <span class="text-xs text-gray-500 line-through">
                                            R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                        </span>
                                    @endif
                                    <span class="text-sm font-bold text-gray-900">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </span>
                                </div>
                                
                                <!-- Rating -->
                                @if(isset($product->rating_average) && $product->rating_average > 0)
                                    <div class="flex items-center">
                                        @for($star = 1; $star <= 5; $star++)
                                            <svg class="h-3 w-3 {{ $star <= $product->rating_average ? 'text-yellow-400' : 'text-gray-300' }}" 
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
                            
                            <!-- Seller Info (Desktop Only) -->
                            <div class="hidden sm:block">
                                <p class="text-xs text-gray-500">por {{ $product->seller->name ?? 'Vendedor' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros de busca.</p>
            <div class="mt-4">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Ver todos os produtos
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Mobile-Optimized JavaScript -->
<script>
function toggleMobileFilters() {
    const filters = document.getElementById('mobile-filters');
    const chevron = document.getElementById('filter-chevron');
    
    filters.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}

function setView(view) {
    const grid = document.getElementById('products-grid');
    const gridBtn = document.getElementById('grid-btn');
    const listBtn = document.getElementById('list-btn');
    
    if (view === 'list') {
        grid.className = 'space-y-4';
        gridBtn.className = 'rounded px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700';
        listBtn.className = 'rounded px-3 py-1 text-sm font-medium text-gray-700 bg-white shadow-sm';
        
        // Transform to list view
        Array.from(grid.children).forEach(item => {
            item.className = 'flex gap-4 bg-white p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow';
        });
    } else {
        // Reset to grid view
        grid.className = 'grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4 xl:grid-cols-5';
        gridBtn.className = 'rounded px-3 py-1 text-sm font-medium text-gray-700 bg-white shadow-sm';
        listBtn.className = 'rounded px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700';
        
        Array.from(grid.children).forEach(item => {
            item.className = 'group relative bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow';
        });
    }
}

// Touch optimization
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback for buttons
    const buttons = document.querySelectorAll('button, a[class*="bg-"]');
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        button.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });
});
</script>
@endsection