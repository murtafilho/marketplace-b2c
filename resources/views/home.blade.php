@extends('layouts.marketplace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white p-8 my-8">
        <div class="max-w-3xl">
            <h1 class="text-4xl font-bold mb-4">Bem-vindo ao Marketplace B2C</h1>
            <p class="text-xl mb-6">Descubra milhares de produtos de vendedores verificados com pagamento seguro via Mercado Pago</p>
            <div class="flex space-x-4">
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                        Começar a Comprar
                    </a>
                    <a href="#" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:bg-opacity-10">
                        Vender no Marketplace
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 my-12">
        <div class="text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_products'] }}+</div>
            <div class="text-gray-600">Produtos</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_sellers'] }}+</div>
            <div class="text-gray-600">Vendedores</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-purple-600">{{ $stats['total_customers'] }}+</div>
            <div class="text-gray-600">Clientes</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['total_categories'] }}</div>
            <div class="text-gray-600">Categorias</div>
        </div>
    </div>

    @if($mainCategories->count() > 0)
    <!-- Categorias Principais -->
    <section class="my-16">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Categorias Principais</h2>
                <p class="text-gray-600">Explore nossos produtos por categoria</p>
            </div>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Ver todas →</a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
            @foreach($mainCategories as $category)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group cursor-pointer">
                <div class="p-6">
                    @if($category->image_path)
                        <img src="{{ asset($category->image_path) }}" alt="{{ $category->name }}" class="w-20 h-20 mx-auto mb-4 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            @php
                                $icons = [
                                    'Eletrônicos' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                                    'Roupas e Acessórios' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                                    'Casa e Jardim' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
                                    'Esportes e Fitness' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                                    'Beleza e Cuidados' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                                    'Livros e Educação' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                                    'Games e Entretenimento' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>',
                                    'Automotivo' => '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a3 3 0 006 0m-3-3a3 3 0 100 6m-3-3h6m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                ];
                                $icon = $icons[$category->name] ?? '<svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>';
                            @endphp
                            {!! $icon !!}
                        </div>
                    @endif
                    <div class="text-center">
                        <h3 class="font-semibold text-gray-800 mb-2">{{ $category->name }}</h3>
                        <p class="text-sm text-blue-600">{{ $category->products_count }} produtos</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($featuredProducts->count() > 0)
    <!-- Produtos em Destaque -->
    <section class="my-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Produtos em Destaque</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800">Ver todos →</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
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
                        
                        <button class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l1.5 6m4.5-6h.01M19 13h.01M7 19a2 2 0 11-4 0 2 2 0 014 0zM17 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
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
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum produto em destaque</h3>
            <p class="text-gray-500 mb-6">Seja o primeiro vendedor a cadastrar produtos no marketplace!</p>
            <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Começar a Vender
            </a>
        </div>
    </section>
    @endif

    @if($popularProducts->count() > 0)
    <!-- Produtos Mais Populares -->
    <section class="my-16">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Produtos Populares</h2>
                <p class="text-gray-600">Os mais visualizados e vendidos</p>
            </div>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Ver todos →</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @foreach($popularProducts as $product)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
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
                    <p class="text-xs text-gray-500 mb-2">{{ $product->seller->user->name ?? 'Vendedor' }}</p>
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
                        
                        <button class="bg-blue-600 text-white p-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l1.5 6m4.5-6h.01M19 13h.01M7 19a2 2 0 11-4 0 2 2 0 014 0zM17 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                        <span>{{ $product->views_count }} visualizações</span>
                        @if($product->sales_count > 0)
                            <span>{{ $product->sales_count }} vendas</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Como Funciona -->
    <section class="my-16 bg-white rounded-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-12">Como Funciona</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Escolha os Produtos</h3>
                <p class="text-gray-600">Navegue por milhares de produtos de vendedores verificados</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Pagamento Seguro</h3>
                <p class="text-gray-600">Pague com PIX, cartão ou boleto via Mercado Pago</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-purple-600">3</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Receba em Casa</h3>
                <p class="text-gray-600">Acompanhe seu pedido e receba com segurança</p>
            </div>
        </div>
    </section>
</div>
@endsection