{{-- Arquivo: resources/views/components/layout/header.blade.php --}}
{{-- Descrição: Header que se adapta ao role do usuário --}}

<header x-data="{ userMenuOpen: false }" 
        @scroll.window="$store.ui && $store.ui.handleScroll()"
        class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-300 dark:border-gray-700 sticky top-0 z-50 transition-all duration-300"
        :class="$store.ui && $store.ui.scrolled ? 'shadow-lg backdrop-blur-md bg-white/95 dark:bg-gray-800/95' : 'shadow-sm'">
    
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            {{-- Logo + Toggle Sidebar --}}
            <div class="flex items-center">
                @auth
                    @if(in_array(auth()->user()->role ?? 'customer', ['admin', 'seller']))
                        {{-- Sidebar toggle apenas para admin/seller --}}
                        <button @click="$store.ui.toggleMobileMenu()"
                                class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden mr-2"
                                aria-label="Toggle sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    @endif
                @endauth
                
                {{-- Logo responsivo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                    @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-vale-verde rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sol-dourado" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                                <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-bold text-primary-700 dark:text-primary-500 leading-none">
                            {{ $siteName }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-gray-500 leading-none hidden sm:block">
                            {{ strtolower($siteDescription) }}
                        </span>
                    </div>
                </a>
            </div>
            
            {{-- Navigation Center (Desktop only) --}}
            <nav class="hidden lg:flex items-center space-x-8">
                @guest
                    {{-- Links públicos --}}
                    <a href="{{ route('products.index') }}" 
                       class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                        Produtos
                    </a>
                    <a href="#categorias" 
                       class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                        Categorias
                    </a>
                    <a href="{{ route('seller.register') }}" 
                       class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                        Vender no valedosol.org
                    </a>
                @endguest
                
                @auth
                    @if(auth()->user()->role === 'customer')
                        {{-- Links do cliente --}}
                        <a href="{{ route('dashboard') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                            Meus Pedidos
                        </a>
                        <a href="#favoritos" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                            Favoritos
                        </a>
                    @elseif(auth()->user()->role === 'seller')
                        {{-- Links do vendedor --}}
                        <a href="{{ route('seller.dashboard') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                            Dashboard
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        {{-- Links do admin --}}
                        <a href="{{ route('admin.dashboard') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors font-medium">
                            Admin
                        </a>
                    @endif
                @endauth
            </nav>
            
            {{-- Actions Right --}}
            <div class="flex items-center space-x-2 sm:space-x-4">
                {{-- Search (sempre visível, mobile collapsible) --}}
                <button @click="$store.ui.toggleSearch()"
                        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Toggle search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                @guest
                    {{-- Botões de Auth simplificados --}}
                    <div class="hidden sm:flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-vale-verde font-medium transition-colors">
                            Entrar
                        </a>
                    </div>
                    <a href="{{ route('register') }}" 
                       class="bg-vale-verde hover:bg-vale-verde-dark text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        Cadastrar
                    </a>
                @endguest
                
                @auth
                    {{-- Carrinho (apenas clientes) --}}
                    @if(auth()->user()->role === 'customer')
                        <button @click="$store.cart && $store.cart.toggleCart()"
                                class="relative p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span x-show="$store.cart && $store.cart.count > 0" 
                                  x-text="$store.cart && $store.cart.count"
                                  class="absolute -top-1 -right-1 bg-comercio-azul text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">
                            </span>
                        </button>
                    @endif
                    
                    {{-- Notifications --}}
                    <button class="relative p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    
                    {{-- User Menu --}}
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-vale-verde transition-colors p-2 rounded-md"
                                aria-expanded="false">
                            <div class="w-8 h-8 bg-vale-verde rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden lg:block font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        {{-- Dropdown Menu --}}
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            
                            <a href="{{ route('dashboard') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                Dashboard
                            </a>
                            
                            @if(auth()->user()->sellerProfile)
                                <a href="{{ route('seller.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Minha Loja
                                </a>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Administração
                                </a>
                            @endif
                            
                            <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
                            
                            <button @click="$store.ui.toggleTheme()" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span x-show="$store.ui.currentTheme === 'light'">🌙 Tema Escuro</span>
                                <span x-show="$store.ui.currentTheme === 'dark'">☀️ Tema Claro</span>
                            </button>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
    
    {{-- Mobile Search Bar --}}
    <div x-show="$store.ui.searchOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="px-4 pb-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="relative">
            <input type="search" 
                   id="mobile-search-input"
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg 
                          focus:ring-2 focus:ring-vale-verde focus:border-vale-verde 
                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                          placeholder-gray-500 dark:placeholder-gray-400"
                   placeholder="Buscar produtos, vendedores...">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>
</header>