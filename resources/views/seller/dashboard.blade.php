@extends('layouts.seller')

@section('title', 'Dashboard')

@section('content')
    <!-- Mobile Header with Welcome -->
    <div class="mb-6 lg:hidden">
        <h1 class="text-xl font-bold text-gray-900">Olá, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600 text-sm mt-1">Resumo do seu desempenho</p>
    </div>

    <!-- Desktop Header with Welcome -->
    <div class="hidden lg:block mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Olá, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600 mt-2">Aqui está um resumo do seu desempenho hoje</p>
    </div>

    <!-- Alertas e Notificações -->
    @if(count($alerts) > 0)
        <div class="mb-4 lg:mb-6 space-y-3">
            @foreach($alerts as $alert)
                <div class="bg-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'emerald') }}-50 border border-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'emerald') }}-200 text-{{ $alert['type'] === 'danger' ? 'red' : ($alert['type'] === 'warning' ? 'yellow' : 'emerald') }}-800 px-4 py-3 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start">
                            <i class="fas fa-{{ $alert['type'] === 'danger' ? 'exclamation-circle' : ($alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle') }} mr-3 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm">{{ $alert['message'] }}</span>
                        </div>
                        @if($alert['action'] !== '#')
                            <a href="{{ $alert['action'] }}" class="text-xs sm:text-sm underline hover:no-underline mt-2 sm:mt-0 sm:ml-4 self-start">Ver detalhes →</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Cards de Estatísticas Principais -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Total de Produtos -->
        <div class="bg-white rounded-lg lg:rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between lg:block">
                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 line-clamp-1">Total de Produtos</p>
                            <p class="text-lg sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">{{ number_format($stats['products_total']) }}</p>
                        </div>
                        <div class="bg-emerald-100 rounded-full p-2 lg:hidden">
                            <i class="fas fa-box text-emerald-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2 space-y-1 lg:space-y-0">
                        <div><span class="text-green-600">{{ $stats['products_active'] }} ativos</span></div>
                        <div><span class="text-yellow-600">{{ $stats['products_draft'] }} rascunhos</span></div>
                    </div>
                </div>
                <div class="hidden lg:block bg-emerald-100 rounded-full p-3">
                    <i class="fas fa-box text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Vendas do Mês -->
        <div class="bg-white rounded-lg lg:rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between lg:block">
                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 line-clamp-1">Vendas do Mês</p>
                            <p class="text-lg sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">{{ number_format($stats['orders_total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 lg:hidden">
                            <i class="fas fa-shopping-cart text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="text-orange-600">{{ $stats['orders_pending'] }} pendentes</span>
                    </p>
                </div>
                <div class="hidden lg:block bg-blue-100 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Receita do Mês -->
        <div class="bg-white rounded-lg lg:rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between lg:block">
                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 line-clamp-1">Receita do Mês</p>
                            <p class="text-base sm:text-xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">R$ {{ number_format($stats['revenue_month'], 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2 lg:hidden">
                            <i class="fas fa-dollar-sign text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Comissão: {{ $stats['commission_rate'] }}%
                    </p>
                </div>
                <div class="hidden lg:block bg-purple-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Visualizações -->
        <div class="bg-white rounded-lg lg:rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center justify-between lg:block">
                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 line-clamp-1">Visualizações</p>
                            <p class="text-lg sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 lg:mt-2">{{ number_format($stats['views_total']) }}</p>
                        </div>
                        <div class="bg-pink-100 rounded-full p-2 lg:hidden">
                            <i class="fas fa-eye text-pink-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Total acumulado
                    </p>
                </div>
                <div class="hidden lg:block bg-pink-100 rounded-full p-3">
                    <i class="fas fa-eye text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Conteúdo Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Produtos Recentes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Produtos Recentes</h2>
                        <a href="{{ route('seller.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Ver todos →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentProducts->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentProducts as $product)
                                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                                            @if($product->images->first())
                                                <img src="{{ Storage::url($product->images->first()->file_path) }}" 
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ Str::limit($product->name, 30) }}</h4>
                                            <p class="text-sm text-gray-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->stock_quantity }} em estoque</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $product->status === 'active' ? 'Ativo' : 'Rascunho' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Nenhum produto cadastrado ainda</p>
                            <a href="{{ route('seller.products.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800">
                                Criar primeiro produto →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Produtos com Baixo Estoque -->
            @if($lowStockProducts->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                Baixo Estoque
                            </h2>
                            <span class="text-sm text-gray-500">Menos de 5 unidades</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($lowStockProducts as $product)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-yellow-600">{{ $product->stock_quantity }}</span>
                                        <p class="text-xs text-gray-500">unidades</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Coluna Lateral (1/3) -->
        <div class="space-y-6">
            <!-- Ações Rápidas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Ações Rápidas</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('seller.products.create') }}" 
                       class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <div class="flex items-center">
                            <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Adicionar Produto</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('seller.products.index') }}" 
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <i class="fas fa-box text-gray-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Gerenciar Produtos</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    
                    <a href="#" 
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-gray-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Ver Relatórios</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <i class="fas fa-cog text-gray-600 mr-3"></i>
                            <span class="font-medium text-gray-900">Configurações</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Status da Conta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Status da Conta</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            Aprovado
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Plano</span>
                        <span class="font-medium text-gray-900">Free</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Limite de Produtos</span>
                        <span class="font-medium text-gray-900">{{ $stats['products_total'] }}/{{ $seller->product_limit }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Taxa de Comissão</span>
                        <span class="font-medium text-gray-900">{{ $stats['commission_rate'] }}%</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Membro desde</span>
                        <span class="font-medium text-gray-900">{{ $stats['member_since']->format('d/m/Y') }}</span>
                    </div>

                    @if(!$seller->mp_connected)
                        <div class="pt-4 border-t border-gray-100">
                            <button class="w-full bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-yellow-600 transition">
                                <i class="fab fa-cc-mastercard mr-2"></i>
                                Conectar Mercado Pago
                            </button>
                        </div>
                    @else
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="text-sm">Mercado Pago Conectado</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Produtos Mais Vistos -->
            @if($topProducts->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Top Produtos</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($topProducts->take(3) as $index => $product)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-bold {{ $index === 0 ? 'text-yellow-500' : ($index === 1 ? 'text-gray-400' : 'text-orange-600') }}">
                                            {{ $index + 1 }}º
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ Str::limit($product->name, 25) }}</h4>
                                            <p class="text-xs text-gray-500">{{ number_format($product->views_count) }} visualizações</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Adicionar animações e interatividade
    document.addEventListener('DOMContentLoaded', function() {
        // Fade in dos cards
        const cards = document.querySelectorAll('.bg-white');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            }, 0);
        });
        
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('[class*="bg-red-50"], [class*="bg-yellow-50"], [class*="bg-blue-50"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>
@endpush