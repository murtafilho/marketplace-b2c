<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authenticated" content="true">
    @endauth

    <title>@yield('title', config('app.name') . ' - Marketplace Comunitário')</title>
    
    <!-- Meta tags para SEO -->
    <meta name="description" content="@yield('description', config('app.name') . ' - O marketplace que conecta a comunidade local com produtos autênticos e vendedores verificados.')">
    <meta name="keywords" content="@yield('keywords', 'marketplace, vale do sol, produtos locais, comunidade, vendedores locais')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', config('app.name') . ' - Marketplace Comunitário')">
    <meta property="og:description" content="@yield('description', 'O marketplace que conecta a comunidade local')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Preload critical fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom Layout CSS --}}
    @if(!empty($customLayoutCSS ?? ''))
        <style id="custom-layout-css">
            {!! $customLayoutCSS !!}
        </style>
    @endif
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-bg-light text-text-primary overflow-x-hidden">
    {{-- Header Responsivo --}}
    @include('components.header')

    {{-- Main Content --}}
    <main class="min-h-screen w-full">
        <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto py-4 sm:py-6">
            @yield('content')
        </div>
    </main>

    {{-- Bottom Navigation Mobile --}}
    @include('components.bottom-nav')

    {{-- Global Notification Toast --}}
    @include('components.notification-toast')

    {{-- Mobile Menu Overlay --}}
    <div x-show="$store.ui && $store.ui.mobileMenu" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden"
         @click="$store.ui && $store.ui.closeMobileMenu()"
        
        <div @click.stop
             x-show="$store.ui.mobileMenu"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-80 max-w-sm bg-white h-full shadow-xl">
             
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-vale-verde">Menu</h2>
                <button @click="$store.ui && $store.ui.closeMobileMenu()" 
                        class="p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="py-4">
                <a href="{{ route('home') }}" 
                   @click="$store.ui && $store.ui.closeMobileMenu()"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Início
                </a>
                
                <a href="{{ route('products.index') }}" 
                   @click="$store.ui && $store.ui.closeMobileMenu()"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Produtos
                </a>
                
                @auth
                    <div class="border-t border-gray-200 my-4"></div>
                    <a href="{{ route('dashboard') }}" 
                       @click="$store.ui && $store.ui.closeMobileMenu()"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    
                    @if(auth()->user()->sellerProfile)
                        <a href="{{ route('seller.dashboard') }}" 
                           @click="$store.ui && $store.ui.closeMobileMenu()"
                           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                            </svg>
                            Minha Loja
                        </a>
                    @else
                        <a href="{{ route('seller.register') }}" 
                           @click="$store.ui && $store.ui.closeMobileMenu()"
                           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar Minha Loja
                        </a>
                    @endif
                @else
                    <div class="border-t border-gray-200 my-4"></div>
                    <a href="{{ route('login') }}" 
                       @click="$store.ui && $store.ui.closeMobileMenu()"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" 
                       @click="$store.ui && $store.ui.closeMobileMenu()"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Cadastrar
                    </a>
                @endauth
            </nav>
        </div>
    </div>

    {{-- Global Loading Overlay - REMOVIDO TEMPORARIAMENTE --}}
    {{-- <div x-show="$store.ui && $store.ui.globalLoading" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-white bg-opacity-80 backdrop-blur-sm z-[100] flex items-center justify-center">
        <div class="text-center">
            <svg class="animate-spin h-12 w-12 text-comercio-azul mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600">Carregando...</p>
        </div>
    </div> --}}

    {{-- Footer --}}
    <footer class="bg-vale-verde text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-sol-dourado rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-vale-verde" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                                <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">vale<span class="text-sol-dourado">dosol</span></span>
                    </div>
                    <p class="text-gray-300">O marketplace que conecta a comunidade local com produtos autênticos e vendedores verificados.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Para Compradores</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white">Como Comprar</a></li>
                        <li><a href="#" class="hover:text-white">Segurança</a></li>
                        <li><a href="#" class="hover:text-white">Entrega</a></li>
                        <li><a href="#" class="hover:text-white">Garantias</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Para Vendedores</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('seller.register') }}" class="hover:text-white">Vender no valedosol.org</a></li>
                        <li><a href="#" class="hover:text-white">Como Funciona</a></li>
                        <li><a href="#" class="hover:text-white">Taxas e Comissões</a></li>
                        <li><a href="#" class="hover:text-white">Suporte ao Vendedor</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Suporte</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-white">Contato</a></li>
                        <li><a href="#" class="hover:text-white">WhatsApp</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-vale-verde-light mt-8 pt-8 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }} Marketplace. Conectando comunidades com autenticidade.</p>
                <p class="text-xs mt-2 text-gray-400">{{ config('app.domain') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>