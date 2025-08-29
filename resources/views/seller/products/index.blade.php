@extends('layouts.seller')

@section('title', 'Meus Produtos')

@section('content')
    <!-- Header personalizado com botão -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Meus Produtos</h1>
            <p class="text-sm text-gray-600 mt-1">Gerencie seus produtos e controle o estoque</p>
        </div>
        <a href="{{ route('seller.products.create') }}" 
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Produto
        </a>
    </div>

    <div class="space-y-6">
            
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Total</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Ativos</p>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['active']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Rascunhos</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['draft']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Inativos</p>
                                <p class="text-2xl font-bold text-gray-600">{{ number_format($stats['inactive']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Filtros -->
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <form method="GET" class="flex gap-4 items-center">
                            <select name="status" class="rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos os status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                    Ativos
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                    Rascunhos
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                    Inativos
                                </option>
                            </select>

                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Buscar produtos..."
                                   class="rounded-md border-gray-300 shadow-sm">

                            <button type="submit" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    <!-- Grid de Produtos -->
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($products as $product)
                                <div class="bg-gray-50 rounded-lg overflow-hidden shadow">
                                    <!-- Imagem do Produto -->
                                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                        @if($product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path ?? $product->images->first()->image_path) }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-400">Sem imagem</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Conteúdo -->
                                    <div class="p-4">
                                        <!-- Status Badge -->
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($product->status == 'active') bg-green-100 text-green-800
                                                @elseif($product->status == 'draft') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @switch($product->status)
                                                    @case('active') Ativo @break
                                                    @case('draft') Rascunho @break
                                                    @case('inactive') Inativo @break
                                                    @default {{ ucfirst($product->status) }}
                                                @endswitch
                                            </span>

                                            @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                                <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                                    Estoque baixo
                                                </span>
                                            @elseif($product->stock_quantity == 0)
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                                    Sem estoque
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Nome do Produto -->
                                        <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">
                                            {{ $product->name }}
                                        </h3>

                                        <!-- Preço -->
                                        <div class="mb-3">
                                            <p class="text-lg font-bold text-gray-900">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                            </p>
                                            @if($product->compare_at_price)
                                                <p class="text-sm text-gray-500 line-through">
                                                    R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Estoque -->
                                        <p class="text-sm text-gray-600 mb-3">
                                            Estoque: {{ $product->stock_quantity }} unidades
                                        </p>

                                        <!-- Ações -->
                                        <div class="flex gap-2">
                                            <a href="{{ route('seller.products.show', $product) }}" 
                                               class="flex-1 text-center bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-3 rounded">
                                                Ver
                                            </a>
                                            <a href="{{ route('seller.products.edit', $product) }}" 
                                               class="flex-1 text-center bg-gray-500 hover:bg-gray-700 text-white text-sm font-bold py-2 px-3 rounded">
                                                Editar
                                            </a>
                                            
                                            @if($product->status == 'draft')
                                                <form method="POST" action="{{ route('seller.products.toggle-status', $product) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-2 px-3 rounded"
                                                            onclick="return confirm('Publicar este produto?')">
                                                        Publicar
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('seller.products.toggle-status', $product) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="bg-yellow-500 hover:bg-yellow-700 text-white text-sm font-bold py-2 px-3 rounded"
                                                            onclick="return confirm('Despublicar este produto?')">
                                                        Despub
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                            <p class="text-gray-500 mb-4">Comece criando seu primeiro produto!</p>
                            <a href="{{ route('seller.products.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Criar Primeiro Produto
                            </a>
                        </div>
                    @endif
                </div>
    </div>
@endsection