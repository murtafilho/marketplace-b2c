@extends('layouts.admin')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header - Mobile-first -->
    <div class="px-4 sm:px-0">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl sm:text-2xl font-semibold text-white">Gestão de Vendedores</h1>
                <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-300">Gerencie todos os vendedores da plataforma</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Mobile-first responsive -->
    <div class="px-4 sm:px-0">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- Total -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-gray-500 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-5 min-w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pendentes - Laranja (Informativo) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-info-500 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-5 min-w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Pendentes</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $stats['pending'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aprovados - Verde (Sucesso) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-5 min-w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Aprovados</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $stats['approved'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejeitados - Vermelho (Erro) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-danger-600 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-5 min-w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Rejeitados</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $stats['rejected'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suspensos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 col-span-2 lg:col-span-1">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-ban text-gray-500 text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-5 min-w-0 flex-1">
                            <dl>
                                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Suspensos</dt>
                                <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $stats['suspended'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters - Mobile-first -->
    <div class="px-4 sm:px-0">
        <div class="bg-white shadow-sm rounded-xl border border-gray-200">
            <div class="px-4 py-4 sm:p-6">
                <form method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Buscar vendedores</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full pl-10 pr-3 py-3 sm:py-2 border border-gray-300 rounded-lg text-sm leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                   placeholder="Buscar por nome, email ou empresa...">
                        </div>
                    </div>
                    
                    <div class="sm:min-w-[180px]">
                        <select name="status" class="block w-full pl-3 pr-10 py-3 sm:py-2 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 rounded-lg transition-colors">
                            <option value="">Todos os status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente Aprovação</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                        </select>
                    </div>

                    <div class="sm:min-w-[120px]">
                        <button type="submit" class="w-full flex justify-center items-center py-3 sm:py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors min-h-[44px]">
                            <i class="fas fa-filter mr-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sellers Table - Mobile-first -->
    <div class="px-4 sm:px-0">
        <div class="bg-white shadow-sm overflow-hidden rounded-xl border border-gray-200">
            <!-- Mobile Card View -->
            <div class="block sm:hidden">
                @foreach($sellers as $seller)
                    <div class="border-b border-gray-200 last:border-b-0 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $seller->user->name }}</h3>
                                <p class="text-xs text-gray-500 truncate mt-1">{{ $seller->user->email }}</p>
                                <p class="text-xs text-gray-900 mt-1">{{ $seller->company_name }}</p>
                            </div>
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                                @if($seller->status === 'approved') bg-success-100 text-success-800
                                @elseif($seller->status === 'pending') bg-info-100 text-info-800
                                @elseif($seller->status === 'rejected') bg-danger-100 text-danger-800
                                @elseif($seller->status === 'suspended') bg-gray-100 text-gray-800
                                @endif">
                                @if($seller->status === 'approved') Aprovado
                                @elseif($seller->status === 'pending') Pendente
                                @elseif($seller->status === 'rejected') Rejeitado
                                @elseif($seller->status === 'suspended') Suspenso
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>Comissão: {{ number_format($seller->commission_rate, 2) }}%</span>
                            <span>{{ $seller->created_at->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.sellers.show', $seller) }}" 
                               class="flex-1 bg-emerald-600 text-white text-center py-2 px-3 rounded-lg text-xs font-medium hover:bg-emerald-700 transition-colors min-h-[44px] flex items-center justify-center">
                                <i class="fas fa-eye mr-1"></i>Ver Detalhes
                            </a>
                            
                            @if($seller->status === 'pending')
                                <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-success-600 text-white py-2 px-3 rounded-lg text-xs font-medium hover:bg-success-700 transition-colors min-h-[44px] flex items-center justify-center"
                                            onclick="return confirm('Aprovar este vendedor?')">
                                        <i class="fas fa-check mr-1"></i>Aprovar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendedor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Empresa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Comissão
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cadastro
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sellers as $seller)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $seller->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $seller->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $seller->company_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $seller->document_number ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($seller->status === 'approved') bg-success-100 text-success-800
                                        @elseif($seller->status === 'pending') bg-info-100 text-info-800
                                        @elseif($seller->status === 'rejected') bg-danger-100 text-danger-800
                                        @elseif($seller->status === 'suspended') bg-gray-100 text-gray-800
                                        @endif">
                                        @if($seller->status === 'approved') Aprovado
                                        @elseif($seller->status === 'pending') Pendente Aprovação
                                        @elseif($seller->status === 'rejected') Rejeitado
                                        @elseif($seller->status === 'suspended') Suspenso
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($seller->commission_rate, 2) }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $seller->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.sellers.show', $seller) }}" 
                                       class="text-emerald-600 hover:text-emerald-800 mr-3 transition-colors">Ver</a>
                                    
                                    @if($seller->status === 'pending')
                                        <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-success-600 hover:text-success-800 mr-3 transition-colors"
                                                    onclick="return confirm('Aprovar este vendedor?')">
                                                Aprovar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 sm:px-6">
                {{ $sellers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection