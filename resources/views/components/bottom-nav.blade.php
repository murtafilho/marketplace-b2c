{{-- Bottom Navigation Mobile --}}
<nav x-data="{ 
    activeTab: '{{ Route::currentRouteName() }}' 
}"
class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 lg:hidden z-40 safe-area-pb">
    <div class="grid grid-cols-5 h-16">
        {{-- Home --}}
        <a href="{{ route('home') }}" 
           @click="activeTab = 'home'"
           class="flex flex-col items-center justify-center space-y-1 transition-colors py-2 px-1"
           :class="activeTab === 'home' || '{{ Route::currentRouteName() }}' === 'home' ? 'text-comercio-azul' : 'text-gray-600'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs font-medium">Início</span>
        </a>
        
        {{-- Categorias --}}
        <button @click="$store.ui.openCategories()"
                class="flex flex-col items-center justify-center space-y-1 text-gray-600 hover:text-vale-verde transition-colors py-2 px-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="text-xs font-medium">Categorias</span>
        </button>
        
        {{-- Vender (Action Button) --}}
        @auth
            @if(!auth()->user()->sellerProfile)
                <a href="{{ route('seller.register') }}" 
                   class="flex flex-col items-center justify-center relative">
                    <div class="bg-gradient-to-r from-sol-dourado to-energia-laranja rounded-full p-3 -mt-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium mt-1 text-sol-dourado">Criar Loja</span>
                </a>
            @else
                <a href="{{ route('seller.dashboard') }}" 
                   class="flex flex-col items-center justify-center relative">
                    <div class="bg-gradient-to-r from-vale-verde to-vale-verde-light rounded-full p-3 -mt-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium mt-1 text-vale-verde">Minha Loja</span>
                </a>
            @endif
        @else
            <a href="{{ route('seller.register') }}" 
               class="flex flex-col items-center justify-center relative">
                <div class="bg-gradient-to-r from-sol-dourado to-energia-laranja rounded-full p-3 -mt-6 shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1 text-sol-dourado">Vender</span>
            </a>
        @endauth
        
        {{-- Carrinho --}}
        <button @click="$store.cart.toggleCart()"
                class="flex flex-col items-center justify-center space-y-1 text-gray-600 hover:text-comercio-azul transition-colors relative py-2 px-1">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="$store.cart.count > 0" 
                      x-text="$store.cart.count"
                      x-transition:enter="transition ease-out duration-200 transform"
                      x-transition:enter-start="opacity-0 scale-0"
                      x-transition:enter-end="opacity-100 scale-100"
                      class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium border-2 border-white">
                </span>
            </div>
            <span class="text-xs font-medium">Carrinho</span>
        </button>
        
        {{-- Conta --}}
        @auth
            <div x-data="{ menuOpen: false }" class="relative">
                <button @click="menuOpen = !menuOpen"
                        class="flex flex-col items-center justify-center space-y-1 text-gray-600 hover:text-comunidade-roxo transition-colors py-2 px-1 w-full">
                    <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <span class="text-xs font-medium">Conta</span>
                </button>
                
                {{-- User Menu Popup --}}
                <div x-show="menuOpen" 
                     @click.away="menuOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-lg shadow-elevated border border-gray-200 py-2 z-50">
                    
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <a href="{{ route('dashboard') }}" 
                       @click="menuOpen = false"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    
                    @if(auth()->user()->sellerProfile)
                        <a href="{{ route('seller.dashboard') }}" 
                           @click="menuOpen = false"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                            </svg>
                            Minha Loja
                        </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           @click="menuOpen = false"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Administração
                        </a>
                    @endif
                    
                    <div class="border-t border-gray-100 my-1"></div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" 
               class="flex flex-col items-center justify-center space-y-1 text-gray-600 hover:text-comunidade-roxo transition-colors py-2 px-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs font-medium">Entrar</span>
            </a>
        @endauth
    </div>
</nav>

{{-- Safe area padding for iOS devices --}}
<style>
    .safe-area-pb {
        padding-bottom: env(safe-area-inset-bottom);
    }
    
    /* Add bottom padding to body to prevent content overlap */
    @media (max-width: 1023px) {
        body {
            padding-bottom: calc(4rem + env(safe-area-inset-bottom));
        }
    }
</style>