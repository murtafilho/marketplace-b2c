{{-- Arquivo: resources/views/layouts/base.blade.php --}}
{{-- Descrição: Layout base compartilhado por todos os perfis --}}

<!DOCTYPE html>
<html lang="pt-BR" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @auth
    <meta name="user-authenticated" content="true">
    <meta name="user-role" content="{{ auth()->user()->role ?? 'customer' }}">
    @endauth

    <title>@yield('title', $siteName . ' - Marketplace Comunitário')</title>
    
    <!-- Favicon -->
    @if($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
    @endif
    
    <!-- Meta tags para SEO -->
    <meta name="description" content="@yield('description', $siteName . ' - ' . $siteTagline)">
    <meta name="keywords" content="@yield('keywords', 'marketplace, vale do sol, produtos locais, comunidade, vendedores locais')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', $siteName . ' - Marketplace Comunitário')">
    <meta property="og:description" content="@yield('description', $siteTagline)">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($siteLogo)
        <meta property="og:image" content="{{ $siteLogo }}">
    @endif

    <!-- Preload critical fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased overflow-x-hidden">
    
    {{-- Container Principal --}}
    <div x-data="{}" 
         class="min-h-screen flex flex-col">
        
        {{-- Header Dinâmico --}}
        @include('components.layout.header')
        
        {{-- Content Wrapper --}}
        <div class="flex-1 flex">
            {{-- Sidebar Condicional - Apenas em páginas admin/seller --}}
            @auth
                @if(in_array(auth()->user()->role ?? 'customer', ['admin', 'seller']) && (request()->routeIs('admin.*') || request()->routeIs('seller.*')))
                    @include('components.layout.sidebar')
                @endif
            @endauth
            
            {{-- Main Content --}}
            <main class="flex-1 w-full bg-gradient-to-br from-vale-verde via-vale-verde-light to-sol-dourado bg-cover bg-center bg-no-repeat bg-fixed relative overflow-hidden" style="background-image: url('/images/hero-bg.png')">
                {{-- Background Image Overlay --}}
                <div class="absolute inset-0 bg-black/20"></div>
                
                {{-- Complex Visual Patterns --}}
                {{-- Geometric Grid Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 50px, rgba(255,255,255,0.1) 50px, rgba(255,255,255,0.1) 51px), repeating-linear-gradient(90deg, transparent, transparent 50px, rgba(255,255,255,0.1) 50px, rgba(255,255,255,0.1) 51px);"></div>
                </div>
                
                {{-- Animated Floating Shapes --}}
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    {{-- Large circles --}}
                    <div class="absolute -top-10 -left-10 sm:-top-20 sm:-left-20 w-48 h-48 sm:w-96 sm:h-96 bg-gradient-to-br from-blue-500/10 to-green-500/10 rounded-full blur-2xl sm:blur-3xl animate-pulse"></div>
                    <div class="absolute top-1/3 -right-16 sm:-right-32 w-40 h-40 sm:w-80 sm:h-80 bg-gradient-to-bl from-yellow-500/10 to-orange-500/10 rounded-full blur-2xl sm:blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                    <div class="absolute -bottom-16 sm:-bottom-32 left-1/4 w-48 h-48 sm:w-96 sm:h-96 bg-gradient-to-tr from-purple-500/10 to-pink-500/10 rounded-full blur-2xl sm:blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
                    
                    {{-- Geometric shapes --}}
                    <div class="absolute top-10 sm:top-20 right-1/3 w-16 h-16 sm:w-32 sm:h-32 border-2 sm:border-4 border-white/10 rotate-45 animate-spin" style="animation-duration: 20s;"></div>
                    <div class="absolute bottom-20 sm:bottom-40 left-10 sm:left-20 w-12 h-12 sm:w-24 sm:h-24 border-2 sm:border-4 border-sol-dourado/10 rotate-12 animate-spin" style="animation-duration: 15s; animation-direction: reverse;"></div>
                    
                    {{-- Dots pattern --}}
                    <svg class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="dots" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="2" fill="white"/>
                            </pattern>
                        </defs>
                        <rect x="0" y="0" width="100%" height="100%" fill="url(#dots)"/>
                    </svg>
                </div>
                
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none"></div>
                <div class="relative px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto py-4 sm:py-6">
                    {{-- Breadcrumb --}}
                    @hasSection('breadcrumb')
                        <nav class="mb-4 sm:mb-6" aria-label="Breadcrumb">
                            @yield('breadcrumb')
                        </nav>
                    @endif
                    
                    {{-- Alerts --}}
                    @include('components.layout.alerts')
                    
                    {{-- Content --}}
                    @yield('content')
                </div>
            </main>
        </div>
        
        {{-- Bottom Navigation Mobile --}}
        @include('components.bottom-nav')
        
        {{-- Footer --}}
        @include('components.layout.footer')
    </div>
    
    {{-- Modais Globais --}}
    @include('components.layout.modals')
    
    {{-- Toast Notifications --}}
    @include('components.notification-toast')
    
    @stack('scripts')
</body>
</html>