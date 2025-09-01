@extends('layouts.base')

@section('title', 'Marketplace - Início')

@section('content')
<!-- Hero Section - Mobile First -->
<section class="relative overflow-hidden bg-white">
    <div class="mx-auto max-w-7xl">
        <div class="relative z-10 bg-white pb-8 sm:pb-16 md:pb-20 lg:w-full lg:max-w-2xl lg:pb-28 xl:pb-32">
            <!-- Diagonal decoration for larger screens -->
            <svg class="absolute inset-y-0 right-0 hidden h-full w-48 translate-x-1/2 transform text-white lg:block" 
                 fill="currentColor" 
                 viewBox="0 0 100 100" 
                 preserveAspectRatio="none">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <div class="relative px-4 pt-6 sm:px-6 lg:px-8">
                <!-- Hero content -->
                <div class="mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <!-- Mobile-first heading -->
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block">Marketplace</span>
                            <span class="block text-emerald-600">Comunitário</span>
                        </h1>
                        
                        <!-- Mobile-optimized description -->
                        <p class="mt-3 text-base text-gray-500 sm:mx-auto sm:mt-5 sm:max-w-xl sm:text-lg md:mt-5 md:text-xl lg:mx-0">
                            Conecte-se com vendedores locais e descubra produtos únicos da sua região.
                        </p>
                        
                        <!-- CTA Buttons - Stack on mobile, inline on larger screens -->
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('products.index') }}" 
                                   class="flex w-full items-center justify-center rounded-md bg-emerald-600 px-8 py-3 text-base font-medium text-white hover:bg-emerald-700 md:px-10 md:py-4 md:text-lg">
                                    Explorar Produtos
                                </a>
                            </div>
                            <div class="mt-3 sm:ml-3 sm:mt-0">
                                <a href="{{ route('seller.register') }}" 
                                   class="flex w-full items-center justify-center rounded-md border border-transparent bg-emerald-100 px-8 py-3 text-base font-medium text-emerald-700 hover:bg-emerald-200 md:px-10 md:py-4 md:text-lg">
                                    Vender no Marketplace
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hero Image - Hidden on mobile, shown on lg -->
    <div class="hidden lg:absolute lg:inset-y-0 lg:right-0 lg:block lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:h-full lg:w-full" 
             src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
             alt="Marketplace">
    </div>
</section>

<!-- Features Section - Mobile First Grid -->
<section class="py-12 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Por que escolher nosso marketplace?</h2>
            <p class="mt-4 text-lg text-gray-600">Benefícios para compradores e vendedores</p>
        </div>
        
        <!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 3 columns -->
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Feature 1 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Compra Segura</h3>
                <p class="mt-2 text-base text-gray-500">
                    Proteção total em suas compras com garantia de entrega e qualidade.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Vendedores Verificados</h3>
                <p class="mt-2 text-base text-gray-500">
                    Todos os vendedores passam por um processo de verificação rigoroso.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Entrega Rápida</h3>
                <p class="mt-2 text-base text-gray-500">
                    Receba seus produtos rapidamente com nosso sistema de logística otimizado.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section - Horizontal scroll on mobile -->
@if($mainCategories && $mainCategories->count() > 0)
<section class="py-12 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Categorias Populares</h2>
        
        <!-- Mobile: Horizontal scroll, Desktop: Grid -->
        <div class="mt-6 flex gap-4 overflow-x-auto pb-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:overflow-visible sm:pb-0 lg:grid-cols-6">
            @foreach($mainCategories->take(6) as $category)
                <a href="{{ route('products.category', $category) }}" class="flex-none sm:flex-auto">
                    <div class="group relative h-32 w-32 overflow-hidden rounded-lg bg-gray-100 sm:h-auto sm:w-auto">
                        <div class="flex h-full w-full items-center justify-center bg-emerald-100 group-hover:bg-emerald-200">
                            @if($category->icon)
                                <span class="text-2xl mb-2">{{ $category->icon }}</span>
                            @endif
                            <span class="text-sm font-medium text-gray-900 text-center">{{ $category->name }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Products Grid - Responsive -->
<section class="py-12 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Produtos em Destaque</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">
                Ver todos
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
        
        <!-- Mobile: 2 columns, Tablet: 3 columns, Desktop: 4 columns -->
        @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4">
                @foreach($featuredProducts as $product)
                    <div class="group relative">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                            @if($product->images && $product->images->first())
                                <img src="{{ $product->images->first()->url }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover object-center group-hover:opacity-75"
                                     loading="lazy">
                            @else
                                <img src="https://via.placeholder.com/300?text=Sem+Imagem" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover object-center group-hover:opacity-75"
                                     loading="lazy">
                            @endif
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm text-gray-700 line-clamp-2">
                                <a href="{{ route('product.show', $product->id) }}">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $product->category->name ?? 'Categoria' }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback for when no featured products exist -->
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4">
                @for($i = 1; $i <= 8; $i++)
                    <div class="group relative">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                            <img src="https://via.placeholder.com/300?text=Produto+{{ $i }}" 
                                 alt="Produto {{ $i }}" 
                                 class="h-full w-full object-cover object-center group-hover:opacity-75"
                                 loading="lazy">
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm text-gray-700">
                                <a href="#">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    Produto Exemplo {{ $i }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">Categoria</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">R$ {{ number_format($i * 50 + rand(20, 99), 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="bg-emerald-700">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:flex lg:items-center lg:justify-between lg:px-8 lg:py-16">
        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
            <span class="block">Pronto para começar?</span>
            <span class="block text-emerald-200">Crie sua loja hoje mesmo.</span>
        </h2>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0 lg:flex-shrink-0">
            <a href="{{ route('seller.register') }}" 
               class="inline-flex items-center justify-center rounded-md bg-white px-5 py-3 text-base font-medium text-emerald-700 hover:bg-emerald-50">
                Criar Minha Loja
            </a>
            <a href="#" 
               class="inline-flex items-center justify-center rounded-md border border-white bg-emerald-700 px-5 py-3 text-base font-medium text-white hover:bg-emerald-600">
                Saiba Mais
            </a>
        </div>
    </div>
</section>
@endsection