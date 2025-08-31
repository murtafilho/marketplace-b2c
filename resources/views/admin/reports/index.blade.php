@extends('layouts.admin')

@section('title', 'Relatórios')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                📊 Relatórios
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Análise de desempenho e métricas do marketplace
            </p>
        </div>
    </div>

    <!-- Cards de Navegação -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Relatório Financeiro -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <span class="text-green-600 text-lg">💰</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            Relatório Financeiro
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Receitas, comissões e análise de vendas
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.reports.financial') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Ver Relatório
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Relatório de Vendedores -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <span class="text-blue-600 text-lg">👥</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            Relatório de Vendedores
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Performance e análise dos vendedores
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.reports.sellers') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Relatório
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Relatório de Produtos -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <span class="text-purple-600 text-lg">📦</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            Relatório de Produtos
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Produtos mais vendidos e categorias
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.reports.products') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Ver Relatório
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Rápido -->
    <div class="mt-12 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">📈 Resumo dos Últimos 30 Dias</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-gray-900">
                    {{ \App\Models\Order::where('created_at', '>=', now()->subDays(30))->count() }}
                </div>
                <div class="text-sm text-gray-500">Pedidos</div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-green-600">
                    R$ {{ number_format(\App\Models\Order::where('created_at', '>=', now()->subDays(30))->where('status', 'paid')->sum('total'), 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">Receita</div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-blue-600">
                    {{ \App\Models\User::where('role', 'seller')->where('created_at', '>=', now()->subDays(30))->count() }}
                </div>
                <div class="text-sm text-gray-500">Novos Vendedores</div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-purple-600">
                    {{ \App\Models\Product::where('created_at', '>=', now()->subDays(30))->count() }}
                </div>
                <div class="text-sm text-gray-500">Novos Produtos</div>
            </div>
        </div>
    </div>
</div>
@endsection