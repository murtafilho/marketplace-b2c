<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard - valedosol.org' }}</title>

    <!-- Font Awesome Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios (local fallback) -->
    <script>
        // Simple axios fallback for basic functionality
        if (typeof axios === 'undefined') {
            window.axios = {
                post: function(url, data) {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    }).then(response => response.json());
                }
            };
        }
    </script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 border-b border-gray-800">
                <h1 class="text-xl font-bold text-red-400">Admin Panel</h1>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrador</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4">
                <div class="px-4 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-red-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-chart-pie mr-3 text-sm"></i>
                        Dashboard
                    </a>

                    <!-- Sellers -->
                    <a href="{{ route('admin.sellers.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.sellers.*') ? 'bg-red-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-user-tie mr-3 text-sm"></i>
                        Vendedores
                    </a>

                    <!-- Products -->
                    <div x-data="{ open: false }" class="space-y-1">
                        <button @click="open = !open"
                               class="group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-cube mr-3 text-sm"></i>
                                Produtos
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="ml-6 space-y-1">
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-th-large mr-2 text-xs"></i>Todos os Produtos
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-hourglass-half mr-2 text-xs"></i>Aguardando Moderação
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>Produtos Reportados
                            </a>
                        </div>
                    </div>

                    <!-- Orders -->
                    <div x-data="{ open: false }" class="space-y-1">
                        <button @click="open = !open"
                               class="group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-receipt mr-3 text-sm"></i>
                                Pedidos
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="ml-6 space-y-1">
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-clipboard-list mr-2 text-xs"></i>Todos os Pedidos
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-shipping-fast mr-2 text-xs"></i>Em Andamento
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-times-circle mr-2 text-xs"></i>Problemas
                            </a>
                        </div>
                    </div>

                    <!-- Categories -->
                    <a href="{{ route('admin.categories.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-red-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-sitemap mr-3 text-sm"></i>
                        Categorias
                    </a>

                    <!-- Users -->
                    <div x-data="{ open: false }" class="space-y-1">
                        <button @click="open = !open"
                               class="group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-user-friends mr-3 text-sm"></i>
                                Usuários
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="ml-6 space-y-1">
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-users mr-2 text-xs"></i>Todos os Usuários
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-crown mr-2 text-xs"></i>Administradores
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-user-slash mr-2 text-xs"></i>Usuários Bloqueados
                            </a>
                        </div>
                    </div>

                    <!-- Finance -->
                    <div x-data="{ open: false }" class="space-y-1">
                        <button @click="open = !open"
                               class="group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-coins mr-3 text-sm"></i>
                                Financeiro
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" class="ml-6 space-y-1">
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-chart-line mr-2 text-xs"></i>Receitas
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-hand-holding-usd mr-2 text-xs"></i>Comissões
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-money-check-alt mr-2 text-xs"></i>Pagamentos
                            </a>
                            <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                <i class="fas fa-file-invoice-dollar mr-2 text-xs"></i>Relatórios
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="px-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Moderação</h3>
                    </div>
                    <div class="px-4 mt-3 space-y-1">
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-flag mr-3 text-sm"></i>
                            Reportes
                            <span class="ml-auto bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">3</span>
                        </a>
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-history mr-3 text-sm"></i>
                            Log de Ações
                        </a>
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-lock mr-3 text-sm"></i>
                            Conteúdo Bloqueado
                        </a>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="px-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistema</h3>
                    </div>
                    <div class="px-4 mt-3 space-y-1">
                        <!-- Settings Submenu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open"
                                   class="group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                                <div class="flex items-center">
                                    <i class="fas fa-sliders-h mr-3 text-sm"></i>
                                    Configurações
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" class="ml-6 space-y-1">
                                {{-- Gestão de Layout removida --}}
                                <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                    <i class="fas fa-shopping-bag mr-2 text-xs"></i>Marketplace
                                </a>
                                <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                    <i class="fas fa-at mr-2 text-xs"></i>E-mails
                                </a>
                                <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                    <i class="fas fa-wallet mr-2 text-xs"></i>Pagamentos
                                </a>
                                <a href="#" class="group flex items-center px-2 py-1 text-sm text-gray-400 hover:text-gray-300">
                                    <i class="fas fa-truck mr-2 text-xs"></i>Frete
                                </a>
                            </div>
                        </div>

                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-chart-area mr-3 text-sm"></i>
                            Analytics
                        </a>
                        
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-hdd mr-3 text-sm"></i>
                            Backup
                        </a>
                        
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-wrench mr-3 text-sm"></i>
                            Manutenção
                        </a>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="px-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Links Rápidos</h3>
                    </div>
                    <div class="px-4 mt-3 space-y-1">
                        <a href="{{ route('home') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-globe mr-3 text-sm"></i>
                            Ver Marketplace
                        </a>
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-book mr-3 text-sm"></i>
                            Documentação
                        </a>
                        <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-headset mr-3 text-sm"></i>
                            Suporte
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                        @if(isset($breadcrumbs))
                            <nav class="text-sm text-gray-500 mt-1">
                                @foreach($breadcrumbs as $breadcrumb)
                                    @if($loop->last)
                                        <span>{{ $breadcrumb['title'] }}</span>
                                    @else
                                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-700">{{ $breadcrumb['title'] }}</a>
                                        <span class="mx-2">></span>
                                    @endif
                                @endforeach
                            </nav>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Quick Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.sellers.index') }}" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700">
                                <i class="fas fa-user-check mr-1"></i>Aprovar Sellers
                            </a>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-600 relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900">
                                <span class="mr-2">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Perfil
                                </a>
                                <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-home mr-2"></i>Ver Marketplace
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-auto">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="m-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="m-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="m-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>