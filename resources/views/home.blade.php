@extends('layouts.base')

@section('title', 'valedosol.org - Marketplace Comunitário')
@section('description', 'Descubra produtos autênticos da comunidade local. Conecte-se com vendedores verificados e apoie o comércio local no Vale do Sol.')

@section('content')
{{-- Hero Section - Vale do Sol --}}
<div class="relative bg-gradient-to-br from-vale-verde via-vale-verde-light to-sol-dourado rounded-2xl text-white p-6 sm:p-8 lg:p-12 my-4 sm:my-8 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-16 h-16 bg-sol-dourado rounded-full"></div>
        <div class="absolute top-1/2 left-1/3 w-12 h-12 bg-white/50 rounded-full"></div>
    </div>
    
    <div class="relative max-w-4xl">
        {{-- Logo and Tagline --}}
        <div class="flex items-center space-x-3 mb-6">
            @if($siteLogo)
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center p-2">
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="w-full h-full object-contain">
                </div>
            @else
                <div class="w-12 h-12 bg-sol-dourado rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-vale-verde" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                        <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                    </svg>
                </div>
            @endif
            
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-white">
                    {{ $siteName }}
                </h1>
                <p class="text-white text-sm sm:text-base font-medium">{{ $siteDescription }}</p>
            </div>
        </div>
        
        <h2 class="text-xl sm:text-3xl lg:text-4xl font-bold mb-4 sm:mb-6 leading-tight text-white">
            Descubra a autenticidade da <br class="hidden sm:block">
            <span class="text-white drop-shadow-lg">comunidade local</span>
        </h2>
        
        <p class="text-lg sm:text-xl mb-6 sm:mb-8 text-white font-medium drop-shadow-md max-w-2xl">
            Conecte-se com vendedores verificados, apoie o comércio local e descubra produtos únicos que refletem a diversidade e criatividade do Vale do Sol.
        </p>
        
        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            @guest
                <a href="{{ route('products.index') }}" 
                   class="bg-primary-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-primary-900 transition-all transform hover:scale-105 text-center shadow-sm">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Explorar Produtos</span>
                    </span>
                </a>
                
                <a href="{{ route('seller.register') }}" 
                   class="bg-secondary-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-secondary-500 transition-all text-center shadow-sm">
                    Criar Minha Loja
                </a>
            @else
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-primary-900 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-primary-700 transition-all text-center shadow-sm">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Painel Admin</span>
                        </span>
                    </a>
                @endif
                
                @if(!auth()->user()->sellerProfile)
                    <a href="{{ route('seller.register') }}" 
                       class="bg-secondary-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-secondary-500 transition-all text-center shadow-sm">
                        Criar Minha Loja
                    </a>
                @else
                    <a href="{{ route('seller.dashboard') }}" 
                       class="bg-secondary-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-secondary-500 transition-all text-center shadow-sm">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Minha Loja</span>
                        </span>
                    </a>
                @endif
                
                <a href="{{ route('products.index') }}" 
                   class="bg-white text-primary-700 px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-semibold hover:bg-primary-100 transition-all text-center shadow-sm border-2 border-white">
                    Explorar Produtos
                </a>
            @endguest
        </div>
        
        {{-- Trust Indicators --}}
        <div class="flex flex-wrap items-center gap-6 mt-8 text-sm text-white font-medium">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-sol-dourado" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Vendedores Verificados</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-sol-dourado" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Pagamento Seguro</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-sol-dourado" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Apoio Local</span>
            </div>
        </div>
    </div>
</div>

{{-- Menu de Categorias --}}
<div class="my-6 sm:my-8">
    @include('components.category-menu', ['categories' => $mainCategories])
</div>

{{-- Estatísticas da Comunidade --}}
<div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-sm p-6 sm:p-8 my-8 sm:my-12">
    <div class="text-center mb-8">
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Nossa Comunidade em Números</h3>
        <p class="text-gray-600">Juntos, construímos um marketplace próspero e diverso</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Produtos Únicos -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Produtos Únicos</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_products']) }}</p>
                    <p class="text-blue-100 text-xs mt-1">{{ $stats['total_categories'] }} categorias</p>
                </div>
                <div class="p-3 bg-blue-400 bg-opacity-30 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Vendedores Verificados -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Vendedores Verificados</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_sellers']) }}</p>
                    <p class="text-green-100 text-xs mt-1">100% aprovados</p>
                </div>
                <div class="p-3 bg-green-400 bg-opacity-30 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Clientes Ativos -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Clientes Ativos</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_customers']) }}</p>
                    <p class="text-purple-100 text-xs mt-1">+{{ rand(10, 50) }} este mês</p>
                </div>
                <div class="p-3 bg-purple-400 bg-opacity-30 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Seção de Categorias Principais --}}
@if($mainCategories->count() > 0)
<section class="my-12 sm:my-16">
    @include('components.category-grid', ['categories' => $mainCategories])
</section>
@endif

@if($featuredProducts->count() > 0)
{{-- Produtos em Destaque --}}
<section class="my-12 sm:my-16 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-8 space-y-2 sm:space-y-0">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Produtos em <span class="text-secondary-700 dark:text-secondary-500">Destaque</span>
            </h2>
            <p class="text-gray-700 dark:text-gray-300 font-medium">Selecionados especialmente para você</p>
        </div>
        <a href="{{ route('products.index') }}" 
           class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium flex items-center space-x-1 group">
            <span>Ver todos</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredProducts as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
    @else
    <!-- Estado vazio quando não há produtos -->
    <section class="my-16 text-center py-16">
        <div class="max-w-md mx-auto">
            <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nenhum produto em destaque</h3>
            <p class="text-gray-700 font-medium mb-6">Seja o primeiro vendedor a cadastrar produtos no marketplace!</p>
            @auth
                @if(!auth()->user()->sellerProfile)
                    <!-- Usuário logado sem loja -->
                    <a href="{{ route('seller.register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Criar Minha Loja
                    </a>
                    <p class="text-gray-600 font-medium text-sm mt-2">Comece a vender hoje mesmo!</p>
                @else
                    <!-- Usuário com loja -->
                    <a href="{{ route('seller.dashboard') }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600">
                        Administrar Loja
                    </a>
                    <p class="text-gray-600 font-medium text-sm mt-2">Gerencie seus produtos e vendas</p>
                @endif
            @else
                <!-- Usuário não logado -->
                <a href="{{ route('seller.register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Criar Minha Loja
                </a>
                <p class="text-gray-600 font-medium text-sm mt-2">Cadastre-se gratuitamente!</p>
            @endauth
        </div>
    </section>
    @endif

    @if($popularProducts->count() > 0)
    <!-- Produtos Mais Populares -->
    <section class="my-12 sm:my-16 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Produtos <span class="text-success-600 dark:text-success-400">Populares</span></h2>
                <p class="text-gray-700 dark:text-gray-300 font-medium">Os mais visualizados e vendidos</p>
            </div>
            <a href="{{ route('search') }}" class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium flex items-center space-x-1 group">
                <span>Ver todos</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @foreach($popularProducts as $product)
            <a href="{{ route('product.show', $product->id) }}" class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm hover:shadow-md hover:border-primary-500 transition-all duration-300 overflow-hidden group block">
                <div class="aspect-square bg-gray-200 relative overflow-hidden">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ asset($product->images->first()->file_path) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Badge de popularidade -->
                    <div class="absolute top-2 left-2">
                        @if($product->views_count > 50)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">🔥 Hot</span>
                        @elseif($product->sales_count > 10)
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">⭐ Bestseller</span>
                        @else
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">👁️ {{ $product->views_count }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="p-3">
                    <h3 class="font-medium text-gray-800 mb-1 text-sm truncate">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-600 font-medium mb-2">{{ $product->seller->user->name ?? 'Vendedor' }}</p>
                    <p class="text-xs text-blue-600 mb-2">{{ $product->category->name ?? '' }}</p>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <div class="text-xs text-gray-500 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</div>
                            @endif
                            <div class="text-sm font-bold text-blue-600">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </div>
                        </div>
                        
                        <button class="bg-blue-600 text-white p-1.5 rounded-lg hover:bg-blue-700 transition-colors" onclick="event.preventDefault(); event.stopPropagation(); alert('Funcionalidade de carrinho em desenvolvimento');">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l1.5 6m4.5-6h.01M19 13h.01M7 19a2 2 0 11-4 0 2 2 0 014 0zM17 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-600 font-medium">
                        <span>{{ $product->views_count }} visualizações</span>
                        @if($product->sales_count > 0)
                            <span>{{ $product->sales_count }} vendas</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Como Funciona -->
    <section class="my-16 bg-white rounded-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-12">Como Funciona</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900">Escolha os Produtos</h3>
                <p class="text-gray-700 font-medium">Navegue por milhares de produtos de vendedores verificados</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900">Pagamento Seguro</h3>
                <p class="text-gray-700 font-medium">Pague com PIX, cartão ou boleto via Mercado Pago</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-purple-600">3</span>
                </div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900">Receba em Casa</h3>
                <p class="text-gray-700 font-medium">Acompanhe seu pedido e receba com segurança</p>
            </div>
        </div>
    </section>
</div>
@endsection