<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Marketplace'))</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Mobile-First Meta Tags -->
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    <!-- Mobile-First Layout Structure -->
    <div class="min-h-full">
        <!-- Fixed Mobile Navigation Bar -->
        <nav class="bg-white shadow-sm lg:hidden fixed top-0 left-0 right-0 z-50">
            <div class="px-4 sm:px-6">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-gray-900">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <button type="button" 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                            x-data="{ mobileMenuOpen: false }">
                        <span class="sr-only">Abrir menu</span>
                        <!-- Icon when menu is closed -->
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!-- Icon when menu is open -->
                        <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu panel -->
            <div x-show="mobileMenuOpen" 
                 x-cloak
                 x-transition:enter="duration-200 ease-out"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="duration-100 ease-in"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-x-0 top-16 z-50 origin-top-right transform bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="divide-y divide-gray-100">
                    <div class="px-4 pb-3 pt-2">
                        @auth
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <span class="text-white font-medium">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                                    <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                Entrar
                            </a>
                            <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                Cadastrar
                            </a>
                        @endauth
                    </div>
                    
                    <div class="px-4 py-3">
                        <a href="{{ route('products.index') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                            Produtos
                        </a>
                        <a href="{{ route('categories.index') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                            Categorias
                        </a>
                        @auth
                            @if(auth()->user()->role === 'seller')
                                <a href="{{ route('seller.dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                    Painel do Vendedor
                                </a>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                    Painel Admin
                                </a>
                            @endif
                        @endauth
                    </div>
                    
                    @auth
                        <div class="px-4 py-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                    Sair
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Fixed Desktop Navigation -->
        <nav class="hidden lg:block bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex flex-shrink-0 items-center">
                            <a href="/" class="text-xl font-bold text-gray-900">
                                {{ config('app.name') }}
                            </a>
                        </div>
                        
                        <!-- Desktop Navigation Links -->
                        <div class="hidden sm:ml-6 lg:flex lg:space-x-8">
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Produtos
                            </a>
                            <a href="{{ route('categories.index') }}" 
                               class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Categorias
                            </a>
                        </div>
                    </div>
                    
                    <!-- Desktop User Menu -->
                    <div class="flex items-center">
                        @auth
                            <div class="relative ml-3" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        type="button" 
                                        class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                    <span class="sr-only">Abrir menu do usuário</span>
                                    <div class="h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <span class="text-white font-medium">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                                    
                                    @if(auth()->user()->role === 'seller')
                                        <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Painel do Vendedor
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Painel Admin
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Meu Perfil
                                    </a>
                                    
                                    <hr class="my-1">
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                            Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">
                                    Entrar
                                </a>
                                <a href="{{ route('register') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                    Cadastrar
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area with Fixed Header Offset -->
        <main class="pt-16">
            <!-- Mobile-first responsive container -->
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
        
        <!-- Mobile Bottom Navigation (for key actions) -->
        <nav class="fixed inset-x-0 bottom-0 z-50 bg-white border-t border-gray-200 lg:hidden">
            <div class="grid h-16 grid-cols-4 items-center">
                <a href="/" class="flex flex-col items-center justify-center py-2 text-xs text-gray-700 hover:text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="mt-1">Início</span>
                </a>
                
                <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center py-2 text-xs text-gray-700 hover:text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                    </svg>
                    <span class="mt-1">Produtos</span>
                </a>
                
                <button class="flex flex-col items-center justify-center py-2 text-xs text-gray-700 hover:text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <span class="mt-1">Carrinho</span>
                </button>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-2 text-xs text-gray-700 hover:text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="mt-1">Conta</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-2 text-xs text-gray-700 hover:text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="mt-1">Entrar</span>
                    </a>
                @endauth
            </div>
        </nav>
        
        <!-- Spacer for bottom nav on mobile -->
        <div class="h-16 lg:hidden"></div>
    </div>
    
    @stack('scripts')
</body>
</html>