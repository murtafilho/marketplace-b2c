<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Marketplace'))</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('description', 'valedosol.org - A melhor experiência de compra online')">
    <meta name="keywords" content="@yield('keywords', 'marketplace, compras, produtos, vendas')">
    <meta name="author" content="{{ config('app.name') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('description', 'valedosol.org')">
    <meta property="og:image" content="@yield('image', asset('images/og-default.jpg'))">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="@yield('title', config('app.name'))">
    <meta property="twitter:description" content="@yield('description', 'valedosol.org')">
    <meta property="twitter:image" content="@yield('image', asset('images/og-default.jpg'))">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#6366f1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    
    <!-- Preload Critical Resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom Layout CSS --}}
    @if(!empty($customLayoutCSS ?? ''))
        <style id="custom-layout-css">
            {!! $customLayoutCSS !!}
        </style>
    @endif
    
    {{-- Critical CSS Inline --}}
    <style>
        /* Critical CSS - Above the fold styles */
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            line-height: 1.6;
        }
        
        /* Loading state */
        .loading {
            @apply animate-pulse bg-gray-200;
        }
        
        /* Mobile-first responsive utilities */
        .container-mobile {
            @apply px-4 mx-auto max-w-full;
        }
        
        @media (min-width: 640px) {
            .container-mobile {
                @apply px-6 max-w-full;
            }
        }
        
        @media (min-width: 1024px) {
            .container-mobile {
                @apply px-8 max-w-7xl;
            }
        }
        
        /* Safe area handling for iOS */
        @supports(padding: max(0px)) {
            .safe-top {
                padding-top: max(1rem, env(safe-area-inset-top));
            }
            .safe-bottom {
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
            }
        }
        
        /* Touch-friendly tap targets */
        .tap-target {
            @apply min-h-11 min-w-11 flex items-center justify-center;
        }
        
        /* Smooth scrolling for mobile */
        html {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Hide scrollbars but keep functionality */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        
        /* Focus styles for accessibility */
        .focus-visible\:ring-custom:focus-visible {
            @apply outline-none ring-2 ring-offset-2 ring-indigo-500;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ 
    mobileMenuOpen: false, 
    sidebarOpen: false,
    loading: true 
}" x-init="setTimeout(() => loading = false, 100)">
    
    <!-- Loading Overlay -->
    <div x-show="loading" x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-white z-[100] flex items-center justify-center">
        <div class="flex flex-col items-center space-y-4">
            <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            <p class="text-sm text-gray-600">Carregando...</p>
        </div>
    </div>
    
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-600 text-white px-3 py-2 rounded-md text-sm z-50">
        Pular para conteúdo principal
    </a>
    
    <!-- Mobile Header -->
    @include('components.layout.mobile-header')
    
    <!-- Desktop Header -->
    <header class="hidden lg:block bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="container-mobile">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors">
                        {{ config('app.name', 'Marketplace') }}
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden lg:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Início
                    </a>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Produtos
                    </a>
                </nav>
                
                <!-- Search -->
                <div class="hidden lg:flex flex-1 max-w-lg mx-8">
                    <div class="relative w-full">
                        <input type="text" placeholder="Buscar produtos..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'customer')
                            <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-indigo-600 relative tap-target">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h6m-6 0a1 1 0 102 0m4 0a1 1 0 102 0"></path>
                                </svg>
                                @if(isset($cart_count) && $cart_count > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $cart_count > 99 ? '99+' : $cart_count }}
                                    </span>
                                @endif
                            </a>
                        @endif
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 tap-target">
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100" 
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                                    @if(auth()->user()->role === 'seller')
                                        <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    @elseif(auth()->user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors">
                            Cadastrar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
        @yield('content')
    </main>
    
    <!-- Bottom Navigation (Mobile) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 safe-bottom z-30">
        <div class="grid grid-cols-4 h-16">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center space-y-1 text-xs {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Início</span>
            </a>
            
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center space-y-1 text-xs {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-gray-600' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Produtos</span>
            </a>
            
            @auth
                @if(auth()->user()->role === 'customer')
                    <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center space-y-1 text-xs {{ request()->routeIs('cart.*') ? 'text-indigo-600' : 'text-gray-600' }} relative">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h6m-6 0a1 1 0 102 0m4 0a1 1 0 102 0"></path>
                        </svg>
                        @if(isset($cart_count) && $cart_count > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                {{ $cart_count > 9 ? '9+' : $cart_count }}
                            </span>
                        @endif
                        <span>Carrinho</span>
                    </a>
                @else
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('seller.dashboard') }}" 
                       class="flex flex-col items-center justify-center space-y-1 text-xs text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endif
                
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center space-y-1 text-xs {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-gray-600' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Perfil</span>
                </a>
            @else
                <a href="{{ route('register') }}" class="flex flex-col items-center justify-center space-y-1 text-xs text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span>Cadastrar</span>
                </a>
                
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center space-y-1 text-xs text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Entrar</span>
                </a>
            @endauth
        </div>
    </nav>
    
    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed bottom-20 lg:bottom-4 right-4 z-50 space-y-2"></div>
    
    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('SW registered: ', registration);
                })
                .catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
        
        // Toast notification system
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `max-w-sm bg-white border-l-4 ${type === 'success' ? 'border-green-400' : type === 'error' ? 'border-red-400' : 'border-blue-400'} rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;
            toast.innerHTML = `
                <div class="p-4 flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 ${type === 'success' ? 'text-green-400' : type === 'error' ? 'text-red-400' : 'text-blue-400'}" fill="currentColor" viewBox="0 0 20 20">
                            ${type === 'success' ? 
                                '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                                '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>'
                            }
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <button onclick="this.closest('.max-w-sm').remove()" class="ml-auto flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        };
        
        // Global error handler for AJAX requests
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG') {
                e.target.src = '/images/placeholder.svg';
            }
        });
        
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('loading');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
</body>
</html>