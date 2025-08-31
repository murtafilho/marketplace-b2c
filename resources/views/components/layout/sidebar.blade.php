{{-- Arquivo: resources/views/components/layout/sidebar.blade.php --}}
{{-- Descrição: Sidebar para admin e vendedores --}}

@if(isset($layoutData) && $layoutData['sidebar_visible'])
<aside x-show="$store.ui?.sidebarOpen ?? true" 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white shadow-lg border-r border-gray-200 z-30 lg:translate-x-0 lg:static lg:h-auto overflow-y-auto">
    
    <div class="p-6">
        {{-- User Info --}}
        <div class="flex items-center space-x-3 mb-6 p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-vale-verde rounded-full flex items-center justify-center">
                @if(isset($layoutData['user_avatar']) && $layoutData['user_avatar'])
                    <img src="{{ $layoutData['user_avatar'] }}" alt="Avatar" class="w-10 h-10 rounded-full">
                @else
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $layoutData['user_name'] ?? 'Usuário' }}
                </p>
                <p class="text-xs text-gray-500 capitalize">
                    {{ $layoutData['user_role'] ?? 'customer' }}
                </p>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="space-y-2">
            @if($layoutData['user_role'] === 'admin')
                {{-- Admin Menu --}}
                <div class="mb-4">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Administração</h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('admin.sellers.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.sellers.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Vendedores</span>
                        </a>
                        
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>Categorias</span>
                        </a>
                    </div>
                </div>

                {{-- Admin's Shop Section (if they have a seller profile) --}}
                @if(auth()->user()->sellerProfile)
                    <div class="mb-4">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Minha Loja</h3>
                        <div class="space-y-1">
                            <a href="{{ route('seller.dashboard') }}" 
                               class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('seller.dashboard') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h-2m13-4h2M5 17h2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Dashboard da Loja</span>
                            </a>
                            
                            <a href="{{ route('seller.products.index') }}" 
                               class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('seller.products.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Meus Produtos</span>
                            </a>
                        </div>
                    </div>
                @endif
            @endif

            @if($layoutData['user_role'] === 'seller')
                {{-- Seller Menu --}}
                <div class="mb-4">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Minha Loja</h3>
                    <div class="space-y-1">
                        <a href="{{ route('seller.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('seller.dashboard') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="{{ route('seller.products.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('seller.products.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Produtos</span>
                        </a>
                        
                        <a href="{{ route('seller.orders.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('seller.orders.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Pedidos</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- General Menu --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="space-y-1">
                    <a href="{{ route('home') }}" 
                       class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('home') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Início</span>
                    </a>
                    
                    <a href="{{ route('products.index') }}" 
                       class="flex items-center space-x-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('products.*') ? 'bg-vale-verde text-white hover:bg-vale-verde-dark' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Explorar</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>

{{-- Overlay para mobile --}}
<div x-show="$store.ui?.sidebarOpen && window.innerWidth < 1024" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="$store.ui.sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
@endif