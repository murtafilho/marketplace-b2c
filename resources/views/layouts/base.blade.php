<!DOCTYPE html>
<html lang="pt-BR" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'valedosol.org - Marketplace da Comunidade')</title>
    
    <!-- Fonts will be loaded locally via Vite -->
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-branco-fresco font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-verde-suave/20 sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Main Header -->
            <div class="flex items-center justify-between py-3">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('storage/assets/logo_coruja_laranja.svg') }}" alt="Vale do Sol Logo" class="w-14 h-14">
                    <div>
                        <h1 class="text-2xl font-display font-semibold text-verde-mata">
                            <a href="{{ route('home') }}">valedosol.org</a>
                        </h1>
                        <p class="text-sm text-cinza-pedra flex items-center">
                            <span class="mr-1.5 text-verde-suave">🛍️</span>
                            Marketplace da Comunidade
                        </p>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8 hidden md:block">
                    <div class="relative">
                        <form action="{{ route('search') }}" method="GET">
                            <input type="text" 
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Buscar produtos locais, artesãos, serviços..." 
                                   class="w-full px-6 py-4 border-2 border-verde-suave/20 rounded-full focus:outline-none focus:border-verde-suave bg-white shadow-sm">
                            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-verde-suave text-white p-2 rounded-full hover:bg-verde-mata transition duration-300">
                                <x-icon name="search" size="5" />
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-6">
                    @auth
                        @php
                            $sellerProfile = auth()->user()->sellerProfile;
                            $isAdmin = auth()->user()->role === 'admin';
                            $isSeller = auth()->user()->role === 'seller';
                        @endphp

                        <!-- Admin Button -->
                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <span>👑</span>
                                <span class="hidden lg:inline">Administrar Site</span>
                            </a>
                        @endif
                        
                        <!-- Seller Store Management -->
                        @if($sellerProfile)
                            @if($sellerProfile->status === 'approved')
                                {{-- Approved Store: Administrar Loja --}}
                                <a href="{{ route('seller.dashboard') }}" 
                                   class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <span>🏪</span>
                                    <span class="hidden lg:inline">Administrar Loja</span>
                                </a>
                            @elseif($sellerProfile->status === 'pending')
                                {{-- Pending Store: Show Status --}}
                                <a href="{{ route('seller.onboarding.index') }}" 
                                   class="flex items-center space-x-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <span>⏰</span>
                                    <span class="hidden lg:inline">Pendente</span>
                                </a>
                            @else
                                {{-- Rejected/Other: Complete Setup --}}
                                <a href="{{ route('seller.onboarding.index') }}" 
                                   class="flex items-center space-x-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <span>⚠️</span>
                                    <span class="hidden lg:inline">Configurar Loja</span>
                                </a>
                            @endif
                        @elseif(!$isSeller)
                            {{-- Non-sellers: Create Store Button --}}
                            <form method="POST" action="{{ route('become-seller') }}">
                                @csrf
                                <button type="submit" 
                                        class="bg-verde-suave hover:bg-verde-mata text-white px-6 py-2 rounded-full font-medium transition duration-300 flex items-center space-x-2">
                                    <span>🏪</span>
                                    <span>Criar Minha Loja</span>
                                </button>
                            </form>
                        @elseif($isSeller && !$sellerProfile)
                            {{-- Seller role but no profile yet --}}
                            <a href="{{ route('seller.onboarding.index') }}" 
                               class="flex items-center space-x-2 bg-verde-suave hover:bg-verde-mata text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <span>⚙️</span>
                                <span class="hidden lg:inline">Configurar Loja</span>
                            </a>
                        @endif
                        
                        <!-- Show Store Name if approved seller -->
                        @if($sellerProfile && $sellerProfile->status === 'approved')
                            <span class="text-sm text-gray-600 hidden xl:inline">
                                ({{ $sellerProfile->company_name }})
                            </span>
                        @endif

                        <!-- Authenticated User -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex flex-col items-center text-verde-mata hover:text-verde-suave transition duration-300">
                                <x-icon name="user" size="8" />
                                <span class="text-sm mt-1 hidden md:block font-medium">{{ auth()->user()->name }}</span>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-verde-suave/10 py-2 z-50">
                                
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-verde-mata hover:bg-verde-suave/10">
                                        <span class="mr-2">⚙️</span>Painel Admin
                                    </a>
                                @endif
                                
                                @if(auth()->user()->role === 'seller')
                                    <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-sm text-verde-mata hover:bg-verde-suave/10">
                                        <span class="mr-2">🏪</span>Minha Loja
                                    </a>
                                @else
                                    @if(!auth()->user()->sellerProfile)
                                        <form method="POST" action="{{ route('become-seller') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-verde-mata hover:bg-verde-suave/10 font-medium">
                                                <span class="mr-2">🏪</span>Criar Minha Loja
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-verde-mata hover:bg-verde-suave/10">
                                    <span class="mr-2">👤</span>Meu Perfil
                                </a>
                                
                                <div class="border-t border-verde-suave/10 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-verde-mata hover:bg-verde-suave/10">
                                        <span class="mr-2">🚪</span>Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest User Actions -->
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-verde-suave px-4 py-2 font-medium transition-colors">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="bg-verde-suave hover:bg-verde-mata text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            Cadastrar
                        </a>
                        
                        <!-- Create Store Button for guests -->
                        <form method="POST" action="{{ route('become-seller') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <span>🏪</span>
                                <span class="hidden lg:inline">Criar Minha Loja</span>
                            </button>
                        </form>
                    @endauth
                    
                    <a href="#" class="flex flex-col items-center text-verde-mata hover:text-orange-400 transition duration-300">
                        <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span class="text-sm mt-1 hidden md:block font-medium">Favoritos</span>
                    </a>
                    
                    <a href="#" class="flex flex-col items-center text-verde-mata hover:text-verde-suave transition duration-300 relative">
                        <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.21 9l-4.38-6.56c-.19-.28-.51-.42-.83-.42-.32 0-.64.14-.83.43L6.79 9H2c-.55 0-1 .45-1 1 0 .09.01.18.04.27l2.54 9.27c.23.84 1 1.46 1.92 1.46h13c.92 0 1.69-.62 1.93-1.46l2.54-9.27L23 10c0-.55-.45-1-1-1h-4.79zM9 9l3-4.4L15 9H9zm3 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                        </svg>
                        <span class="text-sm mt-1 hidden md:block font-medium">Cesta</span>
                        <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-verde-mata hover:text-verde-suave transition duration-300">
                        <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Search Bar -->
            <div class="md:hidden pb-3">
                <div class="relative">
                    <form action="{{ route('search') }}" method="GET">
                        <input type="text" 
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Buscar produtos..." 
                               class="w-full px-4 py-3 border-2 border-verde-suave/20 rounded-full focus:outline-none focus:border-verde-suave bg-white shadow-sm text-sm">
                        <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-verde-suave text-white p-1.5 rounded-full hover:bg-verde-mata transition duration-300">
                            <x-icon name="search" size="4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-full"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-full"
             class="md:hidden bg-white border-t border-verde-suave/20">
            <div class="container mx-auto px-4 py-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Início</a>
                <a href="{{ route('products.index') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Produtos</a>
                <a href="{{ route('categories.index') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Categorias</a>
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Painel Admin</a>
                    @endif
                    @if(auth()->user()->role === 'seller')
                        <a href="{{ route('seller.dashboard') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Minha Loja</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Entrar</a>
                    <a href="{{ route('register') }}" class="block py-2 text-verde-mata hover:text-verde-suave">Cadastrar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-verde-mata text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="{{ asset('storage/assets/logo_coruja_laranja.svg') }}" alt="Vale do Sol Logo" class="w-14 h-14">
                        <div>
                            <h4 class="text-2xl font-display font-semibold">valedosol.org</h4>
                            <p class="text-sm opacity-75">Marketplace da Comunidade</p>
                        </div>
                    </div>
                    <p class="text-verde-suave/80 mb-6 leading-relaxed">
                        Mais que um marketplace, somos uma comunidade que acredita no poder das conexões locais. 
                        Aqui, cada compra fortalece laços e constrói um futuro mais sustentável para todos nós.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-verde-suave/20 rounded-full flex items-center justify-center hover:bg-dourado transition duration-300">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1 1 12.324 0 6.162 6.162 0 0 1-12.324 0zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm4.965-10.405a1.44 1.44 0 1 1 2.881.001 1.44 1.44 0 0 1-2.881-.001z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-verde-suave/20 rounded-full flex items-center justify-center hover:bg-dourado transition duration-300">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-verde-suave/20 rounded-full flex items-center justify-center hover:bg-dourado transition duration-300">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4 text-dourado">Para Compradores</h5>
                    <ul class="space-y-3 text-verde-suave/80">
                        <li><a href="#" class="hover:text-white transition duration-300">Como Comprar</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Entregas</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Trocas e Devoluções</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Programa de Fidelidade</a></li>
                    </ul>
                </div>
                
                <div>
                    <h5 class="font-semibold mb-4 text-dourado">Para Vendedores</h5>
                    <ul class="space-y-3 text-verde-suave/80">
                        <li><a href="{{ route('seller.register') }}" class="hover:text-white transition duration-300">Venda Conosco</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Guia do Vendedor</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Taxas e Comissões</a></li>
                        <li><a href="#" class="hover:text-white transition duration-300">Suporte ao Vendedor</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-verde-suave/20 pt-8 text-center">
                <p class="text-verde-suave/60">
                    &copy; 2024 valedosol.org. Feito com ❤️ para nossa comunidade.
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>