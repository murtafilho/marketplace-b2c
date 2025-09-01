@extends('layouts.base')

@section('title', 'Produtos - Marketplace')

@section('content')
<!-- Mobile-First Products Page -->
<div class="space-y-6">
    
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Produtos</h1>
            <p class="mt-1 text-sm text-gray-600">Descubra produtos incríveis dos nossos vendedores</p>
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
                        @foreach(['Eletrônicos', 'Moda', 'Casa', 'Esportes'] as $cat)
                            <option value="{{ strtolower($cat) }}" {{ request('category') === strtolower($cat) ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Price Range -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Faixa de Preço</label>
                    <select name="price_range" 
                            onchange="this.form.submit()"
                            class="block w-full rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 
                                   focus:ring-2 focus:ring-emerald-600 text-sm
                                   sm:w-48">
                        <option value="">Qualquer preço</option>
                        <option value="0-50" {{ request('price_range') === '0-50' ? 'selected' : '' }}>Até R$ 50</option>
                        <option value="50-100" {{ request('price_range') === '50-100' ? 'selected' : '' }}>R$ 50 - R$ 100</option>
                        <option value="100-500" {{ request('price_range') === '100-500' ? 'selected' : '' }}>R$ 100 - R$ 500</option>
                        <option value="500+" {{ request('price_range') === '500+' ? 'selected' : '' }}>Acima de R$ 500</option>
                    </select>
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
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Summary -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-600">
            Mostrando <span class="font-medium">1-20</span> de <span class="font-medium">{{ $totalProducts ?? '150' }}</span> resultados
            @if(request('search'))
                para "<span class="font-medium">{{ request('search') }}</span>"
            @endif
        </p>
        
        <!-- View Toggle (List/Grid) - Desktop Only -->
        <div class="hidden sm:flex sm:items-center sm:space-x-2">
            <span class="text-sm text-gray-500">Visualização:</span>
            <div class="flex rounded-lg bg-gray-100 p-1">
                <button type="button" 
                        onclick="setView('grid')"
                        class="rounded px-3 py-1 text-sm font-medium text-gray-700 bg-white shadow-sm">
                    Grade
                </button>
                <button type="button" 
                        onclick="setView('list')"
                        class="rounded px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Lista
                </button>
            </div>
        </div>
    </div>
    
    <!-- Products Grid - Mobile First Responsive -->
    <div class="grid gap-4 sm:gap-6">
        <!-- Mobile: 2 columns, Tablet: 3 columns, Desktop: 4 columns -->
        <div id="products-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4 xl:grid-cols-5">
            @for($i = 1; $i <= 20; $i++)
                <div class="group relative bg-white">
                    <!-- Product Image -->
                    <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                        <img src="https://picsum.photos/300/300?random={{ $i }}" 
                             alt="Produto {{ $i }}" 
                             class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy">
                        
                        <!-- Mobile CTA Button -->
                        <div class="absolute inset-x-2 bottom-2 sm:hidden">
                            <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                Adicionar
                            </button>
                        </div>
                        
                        <!-- Desktop Hover Actions -->
                        <div class="absolute inset-x-2 bottom-2 hidden gap-2 opacity-0 transition-opacity group-hover:opacity-100 sm:flex">
                            <button class="flex-1 rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                Carrinho
                            </button>
                            <button class="rounded-lg bg-white p-2 text-gray-700 hover:text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Badges -->
                        @if($i % 3 === 0)
                            <div class="absolute left-2 top-2">
                                <span class="rounded-full bg-red-600 px-2 py-1 text-xs font-medium text-white">
                                    -20%
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="mt-3 space-y-1">
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
                            <a href="#" class="hover:text-emerald-600">
                                Produto Exemplo {{ $i }} - Nome Longo para Teste
                            </a>
                        </h3>
                        <p class="text-xs text-gray-500">Categoria Exemplo</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-gray-900">
                                    R$ {{ number_format(rand(20, 500), 2, ',', '.') }}
                                </span>
                                @if($i % 3 === 0)
                                    <span class="ml-1 text-xs text-gray-500 line-through">
                                        R$ {{ number_format(rand(520, 600), 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <!-- Rating -->
                            <div class="flex items-center">
                                @for($star = 1; $star <= 5; $star++)
                                    <svg class="h-3 w-3 {{ $star <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
        
        <!-- Load More Button - Mobile Friendly -->
        <div class="mt-8 flex justify-center">
            <button type="button" 
                    class="rounded-lg bg-gray-100 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-200 
                           sm:px-8 sm:py-4 sm:text-base">
                Carregar Mais Produtos
            </button>
        </div>
    </div>
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
    
    if (view === 'list') {
        grid.className = 'space-y-4';
        // Transform to list view
        Array.from(grid.children).forEach(item => {
            item.className = 'flex gap-4 bg-white p-4 rounded-lg border';
        });
    } else {
        // Reset to grid view
        grid.className = 'grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4 xl:grid-cols-5';
        Array.from(grid.children).forEach(item => {
            item.className = 'group relative bg-white';
        });
    }
}

// Touch optimization
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback for buttons
    const buttons = document.querySelectorAll('button');
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