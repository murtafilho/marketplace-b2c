@extends('layouts.admin')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.reports.index') }}" class="text-gray-700 hover:text-gray-900">
                            📊 Relatórios
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="mx-2 text-gray-400">/</span>
                            <span class="text-gray-500">Financeiro</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                💰 Relatório Financeiro
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
            <a href="{{ route('admin.reports.export', ['type' => 'financial', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                📊 Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.reports.financial') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="{{ $endDate->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ações</label>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Atualizar Relatório
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Métricas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <span class="text-blue-600 text-lg">🛒</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <span class="text-green-600 text-lg">💰</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Receita Total</p>
                        <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <span class="text-purple-600 text-lg">🏦</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Comissão Total</p>
                        <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($metrics['total_commission'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <span class="text-yellow-600 text-lg">⏳</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Pedidos Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['pending_orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Vendedores -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    🏆 Top Vendedores
                </h3>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        @forelse($topSellers as $seller)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $seller->company_name ?: $seller->seller_name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $seller->total_orders }} pedidos
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        R$ {{ number_format($seller->total_sales, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 text-center text-gray-500">
                            Nenhuma venda no período
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Métodos de Pagamento -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    💳 Métodos de Pagamento
                </h3>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        @forelse($paymentMethods as $method)
                        <li class="py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 capitalize">
                                        {{ $method->payment_method }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">
                                        ({{ $method->count }} pedidos)
                                    </span>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    R$ {{ number_format($method->total, 2, ',', '.') }}
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 text-center text-gray-500">
                            Nenhum pagamento no período
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendas por Dia -->
    @if($salesByDay->count() > 0)
    <div class="mt-6 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                📈 Vendas por Dia
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pedidos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Receita
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salesByDay as $day)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $day->orders }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                R$ {{ number_format($day->revenue, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection