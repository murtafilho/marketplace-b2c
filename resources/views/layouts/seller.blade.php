<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Seller Dashboard - valedosol.org' }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen">
        <!-- Fixed Mobile Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 lg:hidden fixed top-0 left-0 right-0 z-50">
            <div class="flex items-center justify-between px-4 py-3">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('storage/assets/logo_coruja.svg') }}" 
                         alt="Vale do Sol Logo" 
                         class="w-6 h-6">
                    <h1 class="text-lg font-semibold text-emerald-600">Seller Dashboard</h1>
                </div>
                <div class="w-8"></div> <!-- Spacer -->
            </div>
        </header>

        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto"
                 :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 lg:justify-center lg:px-0">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('storage/assets/logo_coruja.svg') }}" 
                             alt="Vale do Sol Logo" 
                             class="w-8 h-8">
                        <h1 class="text-lg lg:text-xl font-bold text-emerald-600">Seller</h1>
                    </div>
                    <button @click="sidebarOpen = false" class="p-2 text-gray-400 hover:text-gray-600 lg:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- User Info -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="ml-3 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">
                                @if(auth()->user()->sellerProfile)
                                    {{ ucfirst(auth()->user()->sellerProfile->status) }}
                                @else
                                    Seller
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 mt-4 overflow-y-auto">
                    <div class="px-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('seller.dashboard') }}" 
                           @click="sidebarOpen = false"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i class="fas fa-tachometer-alt mr-3 text-sm"></i>
                            Dashboard
                        </a>

                        <!-- Products -->
                        <a href="{{ route('seller.products.index') }}" 
                           @click="sidebarOpen = false"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('seller.products.*') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i class="fas fa-box mr-3 text-sm"></i>
                            Produtos
                        </a>

                        <!-- Orders -->
                        <div class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-400 cursor-not-allowed">
                            <i class="fas fa-shopping-cart mr-3 text-sm"></i>
                            <span class="flex-1">Pedidos</span>
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Em breve</span>
                        </div>

                        <!-- Profile -->
                        <a href="#" 
                           @click="sidebarOpen = false"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                            <i class="fas fa-store mr-3 text-sm"></i>
                            Perfil da Loja
                        </a>

                        <!-- Settings -->
                        <a href="{{ route('profile.edit') }}" 
                           @click="sidebarOpen = false"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                            <i class="fas fa-cog mr-3 text-sm"></i>
                            Configurações
                        </a>
                    </div>

                    <!-- Support Section -->
                    <div class="mt-8 pb-4">
                        <div class="px-4 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Suporte</h3>
                        </div>
                        <div class="px-4 space-y-1">
                            <a href="#" 
                               @click="sidebarOpen = false"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                <i class="fas fa-question-circle mr-3 text-sm"></i>
                                Ajuda
                            </a>
                            <a href="{{ route('home') }}" 
                               @click="sidebarOpen = false"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                <i class="fas fa-external-link-alt mr-3 text-sm"></i>
                                Ver Loja
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
                <!-- Fixed Desktop Header -->
                <header class="hidden lg:block bg-white shadow-sm border-b border-gray-200 fixed top-0 left-64 right-0 z-50">
                    <div class="flex justify-between items-center px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl font-semibold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
                            @if(isset($breadcrumbs))
                                <nav class="text-sm text-gray-500 mt-1 flex items-center space-x-1 overflow-x-auto">
                                    @foreach($breadcrumbs as $breadcrumb)
                                        @if($loop->last)
                                            <span class="whitespace-nowrap">{{ $breadcrumb['title'] }}</span>
                                        @else
                                            <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-700 whitespace-nowrap">{{ $breadcrumb['title'] }}</a>
                                            <span class="text-gray-400">></span>
                                        @endif
                                    @endforeach
                                </nav>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-3 ml-4">
                            <!-- Notifications -->
                            <button class="p-2 text-gray-400 hover:text-gray-600 relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900 p-1 rounded-md transition-colors">
                                    <span class="mr-2 hidden sm:block">{{ auth()->user()->name }}</span>
                                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-xs"></i>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-user mr-2"></i>Perfil
                                    </a>
                                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-home mr-2"></i>Ver Marketplace
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content with Fixed Header Offset -->
                <main class="flex-1 overflow-auto pt-16 lg:pt-16">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="m-3 lg:m-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start" role="alert">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                            <span class="text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="m-3 lg:m-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start" role="alert">
                            <i class="fas fa-exclamation-circle mr-2 mt-0.5 text-red-500"></i>
                            <span class="text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="m-3 lg:m-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-start" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2 mt-0.5 text-yellow-500"></i>
                            <span class="text-sm">{{ session('warning') }}</span>
                        </div>
                    @endif

                    <!-- Page Content -->
                    <div class="p-3 lg:p-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>