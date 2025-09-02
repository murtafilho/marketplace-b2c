@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
    <!-- Quick Stats - Mobile-first responsive -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Users - Azul (Primário) -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-emerald-100 text-xs sm:text-sm font-medium truncate">Total de Usuários</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ number_format($stats['users_total']) }}</p>
                    <p class="text-emerald-100 text-xs mt-1">+{{ $stats['users_new_this_month'] ?? 0 }} este mês</p>
                </div>
                <div class="p-2 sm:p-3 bg-emerald-400 bg-opacity-30 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-users text-lg sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Sellers - Sucesso -->
        <div class="bg-gradient-to-r from-success-600 to-green-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-green-100 text-xs sm:text-sm font-medium truncate">Sellers Aprovados</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ number_format($stats['sellers_approved']) }}</p>
                    <p class="text-green-100 text-xs mt-1">{{ $stats['sellers_approved_rate'] ?? '0' }}% taxa aprovação</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-400 bg-opacity-30 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-store text-lg sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Sellers - Laranja (Informativo) -->
        <div class="bg-gradient-to-r from-info-500 to-info-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-info-100 text-xs sm:text-sm font-medium truncate">Aguardando Aprovação</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ number_format($stats['sellers_pending']) }}</p>
                    <p class="text-info-100 text-xs mt-1">
                        @if($stats['sellers_pending'] > 0)
                            <i class="fas fa-exclamation-triangle mr-1"></i>Requer atenção
                        @else
                            Tudo em dia
                        @endif
                    </p>
                </div>
                <div class="p-2 sm:p-3 bg-info-400 bg-opacity-30 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-clock text-lg sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Products - Warning -->
        <div class="bg-gradient-to-r from-warning-600 to-yellow-600 rounded-xl shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-yellow-100 text-xs sm:text-sm font-medium truncate">Produtos Ativos</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ number_format($stats['products_active']) }}</p>
                    <p class="text-yellow-100 text-xs mt-1">{{ $stats['categories_count'] ?? 0 }} categorias</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-400 bg-opacity-30 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-box text-lg sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin's Shop Card -->
    @if(auth()->user()->sellerProfile)
        <div class="mb-6 sm:mb-8">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl shadow-lg p-4 sm:p-6 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex-1">
                        <div class="flex items-start sm:items-center space-x-3 mb-4">
                            <div class="p-2 sm:p-3 bg-white bg-opacity-20 rounded-full flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 7a1 1 0 100-2 1 1 0 000 2zm6-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg sm:text-xl font-bold truncate">{{ auth()->user()->sellerProfile->company_name }}</h3>
                                <p class="text-emerald-100 text-xs sm:text-sm">Sua loja no marketplace</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-emerald-200 text-xs">Status</p>
                                <p class="font-semibold capitalize text-sm">
                                    @if(auth()->user()->sellerProfile->status === 'approved')
                                        ✅ Aprovada
                                    @elseif(auth()->user()->sellerProfile->status === 'pending')
                                        ⏳ Pendente
                                    @else
                                        ❌ {{ auth()->user()->sellerProfile->status }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-emerald-200 text-xs">Produtos</p>
                                <p class="font-semibold text-sm">{{ auth()->user()->sellerProfile->products()->count() ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row lg:flex-col space-y-2 sm:space-y-0 sm:space-x-2 lg:space-x-0 lg:space-y-2">
                        <a href="{{ route('seller.dashboard') }}" 
                           class="bg-white bg-opacity-20 hover:bg-opacity-30 transition-colors px-3 sm:px-4 py-2 rounded-lg text-center text-xs sm:text-sm font-medium min-h-[44px] flex items-center justify-center">
                            Ver Dashboard
                        </a>
                        <a href="{{ route('seller.products.index') }}" 
                           class="bg-white bg-opacity-20 hover:bg-opacity-30 transition-colors px-3 sm:px-4 py-2 rounded-lg text-center text-xs sm:text-sm font-medium min-h-[44px] flex items-center justify-center">
                            Gerenciar Produtos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Additional Stats Row - Mobile-first -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Receita Total</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">R$ {{ number_format($stats['revenue_total'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-success-600 text-xs mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['revenue_growth'] ?? '0' }}% vs mês anterior
                    </p>
                </div>
                <div class="p-2 sm:p-3 bg-success-100 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-dollar-sign text-success-600 text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Pedidos Totais</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['orders_total'] ?? 0) }}</p>
                    <p class="text-emerald-600 text-xs mt-1">{{ $stats['orders_today'] ?? 0 }} hoje</p>
                </div>
                <div class="p-2 sm:p-3 bg-emerald-100 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-shopping-cart text-emerald-600 text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Commissions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Comissões Geradas</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">R$ {{ number_format($stats['commissions_total'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-info-600 text-xs mt-1">{{ number_format($stats['commission_rate'] ?? 10, 1) }}% média</p>
                </div>
                <div class="p-2 sm:p-3 bg-info-100 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-percentage text-info-600 text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-gray-500 text-xs sm:text-sm font-medium truncate">Status do Sistema</p>
                    <p class="text-xl sm:text-2xl font-bold text-success-600">
                        <i class="fas fa-check-circle mr-2"></i>Online
                    </p>
                    <p class="text-gray-600 text-xs mt-1">99.9% uptime</p>
                </div>
                <div class="p-2 sm:p-3 bg-success-100 rounded-full flex-shrink-0 ml-3">
                    <i class="fas fa-server text-success-600 text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid - Mobile-first -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-6 sm:mb-8">
        <!-- Pending Sellers (Priority) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-clock mr-2 text-yellow-500"></i>
                            Vendedores Aguardando Aprovação
                        </h3>
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                            @if($stats['sellers_pending'] > 0)
                                <span class="bg-danger-100 text-danger-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $stats['sellers_pending'] }} pendente(s)
                                </span>
                            @endif
                            <a href="{{ route('admin.sellers.index') }}" 
                               class="text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                                Ver todos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($recent_sellers->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_sellers->take(5) as $seller)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-info-50 rounded-lg border border-info-200 space-y-3 sm:space-y-0">
                                    <div class="flex items-start sm:items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-info-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-info-600 text-sm sm:text-lg"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $seller->user->name }}</p>
                                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $seller->company_name ?? 'Nome da empresa não fornecido' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Enviado {{ $seller->submitted_at ? $seller->submitted_at->diffForHumans() : 'recentemente' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end sm:justify-start">
                                        <a href="{{ route('admin.sellers.show', $seller) }}" 
                                           class="bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-emerald-700 transition-colors min-h-[44px] flex items-center justify-center">
                                            <i class="fas fa-eye mr-1"></i>Avaliar
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Nenhum vendedor aguardando aprovação</p>
                            <p class="text-gray-400 text-xs mt-1">Todos os vendedores foram processados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <!-- Action Cards -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bolt mr-2 text-blue-500"></i>Ações Rápidas
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.sellers.index') }}" 
                       class="flex items-center p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors group min-h-[56px]">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 flex-shrink-0">
                            <i class="fas fa-store text-emerald-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Gerenciar Vendedores</p>
                            <p class="text-xs text-gray-500">Aprovar, rejeitar e configurar</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 flex-shrink-0"></i>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center p-3 bg-info-50 rounded-lg hover:bg-info-100 transition-colors group min-h-[56px]">
                        <div class="w-10 h-10 bg-info-100 rounded-lg flex items-center justify-center group-hover:bg-info-200 flex-shrink-0">
                            <i class="fas fa-tags text-info-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Gerenciar Categorias</p>
                            <p class="text-xs text-gray-500">Criar, editar e organizar categorias</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 flex-shrink-0"></i>
                    </a>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg opacity-60 cursor-not-allowed min-h-[56px]">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-box text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Moderar Produtos</p>
                            <p class="text-xs text-gray-500">Funcionalidade em desenvolvimento</p>
                        </div>
                        <span class="bg-danger-100 text-danger-800 text-xs px-2 py-1 rounded-full flex-shrink-0">Indisponível</span>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg opacity-60 cursor-not-allowed min-h-[56px]">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-bar text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Relatórios</p>
                            <p class="text-xs text-gray-500">Funcionalidade em desenvolvimento</p>
                        </div>
                        <span class="bg-danger-100 text-danger-800 text-xs px-2 py-1 rounded-full flex-shrink-0">Indisponível</span>
                    </div>



                    <div class="flex items-center p-3 bg-gray-50 rounded-lg opacity-60 cursor-not-allowed min-h-[56px]">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cog text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Configurações</p>
                            <p class="text-xs text-gray-500">Funcionalidade em desenvolvimento</p>
                        </div>
                        <span class="bg-danger-100 text-danger-800 text-xs px-2 py-1 rounded-full flex-shrink-0">Indisponível</span>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-heartbeat mr-2 text-green-500"></i>Status do Sistema
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Database</span>
                        <span class="flex items-center text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Online
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Cache</span>
                        <span class="flex items-center text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Funcionando
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Storage</span>
                        <span class="flex items-center text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>OK
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Queue</span>
                        <span class="flex items-center text-sm text-yellow-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Sync Mode
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-history mr-2 text-gray-500"></i>Atividade Recente
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @if(isset($recent_activities) && $recent_activities->count() > 0)
                @foreach($recent_activities as $activity)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($activity['type'] == 'seller_approved')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600 text-sm"></i>
                                    </div>
                                @elseif($activity['type'] == 'seller_registered')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                                    </div>
                                @elseif($activity['type'] == 'product_created')
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-purple-600 text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info text-gray-600 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-history text-gray-400 text-lg"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Nenhuma atividade recente</p>
                    <p class="text-gray-400 text-xs">As atividades do sistema aparecerão aqui</p>
                </div>
            @endif
        </div>
    </div>
@endsection