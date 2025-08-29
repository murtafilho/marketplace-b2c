@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total de Usuários</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['users_total']) }}</p>
                    <p class="text-blue-100 text-xs mt-1">+{{ $stats['users_new_this_month'] ?? 0 }} este mês</p>
                </div>
                <div class="p-3 bg-blue-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Sellers -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Sellers Aprovados</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['sellers_approved']) }}</p>
                    <p class="text-green-100 text-xs mt-1">{{ $stats['sellers_approved_rate'] ?? '0' }}% taxa aprovação</p>
                </div>
                <div class="p-3 bg-green-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-store text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Sellers -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Aguardando Aprovação</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['sellers_pending']) }}</p>
                    <p class="text-yellow-100 text-xs mt-1">
                        @if($stats['sellers_pending'] > 0)
                            <i class="fas fa-exclamation-triangle mr-1"></i>Requer atenção
                        @else
                            Tudo em dia
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-yellow-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Products -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Produtos Ativos</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['products_active']) }}</p>
                    <p class="text-purple-100 text-xs mt-1">{{ $stats['categories_count'] ?? 0 }} categorias</p>
                </div>
                <div class="p-3 bg-purple-400 bg-opacity-30 rounded-full">
                    <i class="fas fa-box text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Receita Total</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($stats['revenue_total'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-green-600 text-xs mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['revenue_growth'] ?? '0' }}% vs mês anterior
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pedidos Totais</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['orders_total'] ?? 0) }}</p>
                    <p class="text-blue-600 text-xs mt-1">{{ $stats['orders_today'] ?? 0 }} hoje</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Commissions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Comissões Geradas</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($stats['commissions_total'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-purple-600 text-xs mt-1">{{ number_format($stats['commission_rate'] ?? 10, 1) }}% média</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Status do Sistema</p>
                    <p class="text-2xl font-bold text-green-600">
                        <i class="fas fa-check-circle mr-2"></i>Online
                    </p>
                    <p class="text-gray-600 text-xs mt-1">99.9% uptime</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-server text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Pending Sellers (Priority) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-clock mr-2 text-yellow-500"></i>
                            Vendedores Aguardando Aprovação
                        </h3>
                        <div class="flex space-x-2">
                            @if($stats['sellers_pending'] > 0)
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $stats['sellers_pending'] }} pendente(s)
                                </span>
                            @endif
                            <a href="{{ route('admin.sellers.index') }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Ver todos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($recent_sellers->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_sellers->take(5) as $seller)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-store text-yellow-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $seller->user->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $seller->company_name ?? 'Nome da empresa não fornecido' }}</p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Enviado {{ $seller->submitted_at ? $seller->submitted_at->diffForHumans() : 'recentemente' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.sellers.show', $seller) }}" 
                                           class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-blue-700 transition-colors">
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
                       class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                            <i class="fas fa-store text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">Gerenciar Vendedores</p>
                            <p class="text-xs text-gray-500">Aprovar, rejeitar e configurar</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600"></i>
                    </a>

                    <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200">
                            <i class="fas fa-box text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">Moderar Produtos</p>
                            <p class="text-xs text-gray-500">Aprovar novos produtos</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Em breve</span>
                    </a>

                    <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200">
                            <i class="fas fa-chart-bar text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">Relatórios</p>
                            <p class="text-xs text-gray-500">Análises e métricas</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Em breve</span>
                    </a>

                    <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200">
                            <i class="fas fa-cog text-gray-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">Configurações</p>
                            <p class="text-xs text-gray-500">Sistema e marketplace</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Em breve</span>
                    </a>
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