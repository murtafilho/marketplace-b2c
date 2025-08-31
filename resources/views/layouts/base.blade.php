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
            <main class="flex-1 w-full bg-gray-100 dark:bg-gray-800">
                <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto py-4 sm:py-6">
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