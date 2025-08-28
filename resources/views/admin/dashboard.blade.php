<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Administrativo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['users_total']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Vendedores Aprovados</p>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['sellers_approved']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Aguardando Aprovação</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['sellers_pending']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Produtos Ativos</p>
                                <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['products_active']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Sellers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vendedores Pendentes</h3>
                        
                        @if($recent_sellers->count() > 0)
                            <div class="space-y-3">
                                @foreach($recent_sellers as $seller)
                                    <div class="flex items-center justify-between py-2">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $seller->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $seller->company_name }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.sellers.show', $seller) }}" 
                                               class="text-blue-600 text-xs hover:text-blue-800">Ver detalhes</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('admin.sellers.index') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800">Ver todos os vendedores →</a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Nenhum vendedor pendente.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pedidos Recentes</h3>
                        
                        @if($recent_orders->count() > 0)
                            <div class="space-y-3">
                                @foreach($recent_orders as $order)
                                    <div class="flex items-center justify-between py-2">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->user->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Nenhum pedido recente.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>