{{-- Header Responsivo Vale do Sol --}}
<header x-data="{ 
    searchOpen: false 
}" 
@scroll.window="$store.ui && $store.ui.handleScroll()"
class="sticky top-0 z-50 transition-all duration-300 bg-white shadow-sm"
:class="$store.ui && $store.ui.scrolled ? 'shadow-lg backdrop-blur-md bg-white/95' : 'shadow-sm'">
    
    {{-- Mobile Header --}}
    <div class="lg:hidden">
        <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-1.5 sm:space-x-2">
                @if($siteLogo)
                    <img src="{{ $siteLogo }}" 
                         alt="{{ $siteName }}" 
                         class="h-8 sm:h-10 w-auto object-contain">
                @else
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-vale-verde rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-sol-dourado" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                            <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-vale-verde text-base sm:text-lg leading-none">
                            {{ $siteName }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-gray-500 leading-none">{{ $siteTagline }}</span>
                    </div>
                @endif
            </a>
            
            {{-- Mobile Actions --}}
            <div class="flex items-center space-x-1 sm:space-x-2">
                @auth
                    @php
                        $sellerProfile = auth()->user()->sellerProfile;
                        $isAdmin = auth()->user()->role === 'admin';
                        $isSeller = auth()->user()->role === 'seller';
                    @endphp

                    {{-- Admin Button --}}
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-crown text-xs"></i>
                            <span>Admin</span>
                        </a>
                    @endif
                    
                    {{-- Seller Store Management --}}
                    @if($sellerProfile)
                        @if($sellerProfile->status === 'approved')
                            {{-- Approved Store: Administrar Loja --}}
                            <a href="{{ route('seller.dashboard') }}" 
                               class="flex items-center space-x-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-store text-xs"></i>
                                <span>Administrar Loja</span>
                            </a>
                        @elseif($sellerProfile->status === 'pending')
                            {{-- Pending Store: Show Status --}}
                            <a href="{{ route('seller.onboarding') }}" 
                               class="flex items-center space-x-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-clock text-xs"></i>
                                <span>Pendente</span>
                            </a>
                        @else
                            {{-- Rejected/Other: Complete Setup --}}
                            <a href="{{ route('seller.onboarding') }}" 
                               class="flex items-center space-x-1 bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-exclamation-triangle text-xs"></i>
                                <span>Configurar Loja</span>
                            </a>
                        @endif
                    @elseif(!$isSeller)
                        {{-- Non-sellers: Create Store Button --}}
                        <form method="POST" action="{{ route('become-seller') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center space-x-1 bg-vale-verde hover:bg-vale-verde-dark text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-store text-xs"></i>
                                <span>Criar Minha Loja</span>
                            </button>
                        </form>
                    @endif
                    
                    {{-- Logout Button with Icon --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Sair</span>
                        </button>
                    </form>
                @else
                    {{-- Guest: Login and Register Buttons --}}
                    <a href="{{ route('login') }}" 
                       class="text-gray-700 hover:text-vale-verde px-3 py-2 font-medium transition-colors">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-vale-verde hover:bg-vale-verde-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Cadastrar
                    </a>
                @endauth
            </div>
        </div>
        
        {{-- Mobile Search Bar --}}
        <div x-show="$store.ui && $store.ui.searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="px-3 sm:px-4 pb-2.5 sm:pb-3 border-t border-gray-100">
            @include('components.search-bar')
        </div>
    </div>
    
    {{-- Desktop Header --}}
    <div class="hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" 
                             alt="{{ $siteName }}" 
                             class="h-12 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 bg-vale-verde rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-sol-dourado" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                                <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-vale-verde">
                                {{ $siteName }}
                            </span>
                            <p class="text-xs text-gray-600">{{ $siteTagline }}</p>
                        </div>
                    @endif
                </a>
                
                {{-- Navigation Links --}}
                <nav class="hidden xl:flex items-center space-x-8">
                    <a href="{{ route('home') }}" 
                       class="text-gray-700 hover:text-vale-verde transition-colors font-medium
                              {{ Route::currentRouteName() == 'home' ? 'text-vale-verde border-b-2 border-vale-verde pb-1' : '' }}">
                        Início
                    </a>
                    <a href="{{ route('products.index') }}" 
                       class="text-gray-700 hover:text-vale-verde transition-colors font-medium
                              {{ Str::startsWith(Route::currentRouteName(), 'products') ? 'text-vale-verde border-b-2 border-vale-verde pb-1' : '' }}">
                        Produtos
                    </a>
                    <button @click="$store.ui && $store.ui.openCategories()"
                            class="text-gray-700 hover:text-vale-verde transition-colors font-medium">
                        Categorias
                    </button>
                </nav>
                
                {{-- Search Desktop --}}
                <div class="flex-1 max-w-2xl mx-8">
                    @include('components.search-bar')
                </div>
                
                {{-- User Actions --}}
                <div class="flex items-center space-x-4">
                    @auth
                        @php
                            $sellerProfile = auth()->user()->sellerProfile;
                            $isAdmin = auth()->user()->role === 'admin';
                            $isSeller = auth()->user()->role === 'seller';
                        @endphp

                        {{-- User Name --}}
                        <span class="text-gray-700 font-medium hidden xl:inline">
                            Olá, {{ auth()->user()->name }}
                        </span>
                        
                        {{-- Show Store Name if approved seller --}}
                        @if($sellerProfile && $sellerProfile->status === 'approved')
                            <span class="text-sm text-gray-500 hidden xl:inline">
                                ({{ $sellerProfile->company_name }})
                            </span>
                        @endif
                        
                        {{-- Admin Dashboard Button --}}
                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-crown"></i>
                                <span>Administrar Site</span>
                            </a>
                        @endif
                        
                        {{-- Seller Store Management --}}
                        @if($sellerProfile)
                            @if($sellerProfile->status === 'approved')
                                {{-- Approved Store: Administrar Loja --}}
                                <a href="{{ route('seller.dashboard') }}" 
                                   class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-store"></i>
                                    <span>Administrar Minha Loja</span>
                                </a>
                            @elseif($sellerProfile->status === 'pending')
                                {{-- Pending Store: Show Status Badge --}}
                                <a href="{{ route('seller.onboarding') }}" 
                                   class="flex items-center space-x-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-clock"></i>
                                    <span>Aguardando Aprovação</span>
                                </a>
                            @else
                                {{-- Rejected/Other: Complete Setup --}}
                                <a href="{{ route('seller.onboarding') }}" 
                                   class="flex items-center space-x-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Completar Cadastro</span>
                                </a>
                            @endif
                        @elseif(!$isSeller)
                            {{-- Non-sellers: Create Store Button --}}
                            <form method="POST" action="{{ route('become-seller') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center space-x-2 bg-vale-verde hover:bg-vale-verde-dark text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-store"></i>
                                    <span>Criar Minha Loja</span>
                                </button>
                            </form>
                        @elseif($isSeller && !$sellerProfile)
                            {{-- Seller role but no profile yet --}}
                            <a href="{{ route('seller.onboarding') }}" 
                               class="flex items-center space-x-2 bg-vale-verde hover:bg-vale-verde-dark text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-cog"></i>
                                <span>Configurar Loja</span>
                            </a>
                        @endif
                        
                        {{-- Logout Button --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Sair</span>
                            </button>
                        </form>
                    @else
                        {{-- Guest Actions: Login and Register --}}
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 hover:text-vale-verde px-4 py-2 font-medium transition-colors">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-vale-verde hover:bg-vale-verde-dark text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            Cadastrar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>