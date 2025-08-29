@extends('layouts.marketplace')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🛒 Carrinho de Compras</h1>
        
        @if($cartItems && $cartItems->count() > 0)
            <div class="text-sm text-gray-600">
                {{ $cartItems->sum('quantity') }} item(s) no carrinho
            </div>
        @endif
    </div>

    @if($cartItems && $cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lista de Produtos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border">
                    @foreach($cartItems as $item)
                        <div class="p-6 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-start space-x-4">
                                <!-- Imagem do Produto -->
                                <div class="flex-shrink-0">
                                    @if($item->product->images && $item->product->images->count() > 0)
                                        <img src="{{ Storage::url($item->product->images->first()->file_path) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-20 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Informações do Produto -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('products.show', $item->product) }}" class="hover:text-blue-600">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Vendido por: {{ $item->product->seller->company_name ?? $item->product->seller->user->name }}
                                            </p>
                                            @if($item->variation)
                                                <p class="text-sm text-gray-500 mt-1">{{ $item->variation->name }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Botão Remover -->
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Remover este item do carrinho?')"
                                                    class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Quantidade e Preço -->
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex items-center space-x-3">
                                            <label class="text-sm text-gray-600">Qtd:</label>
                                            
                                            <div class="flex items-center border border-gray-300 rounded">
                                                <button type="button" 
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                
                                                <input type="number" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock_quantity }}"
                                                       class="w-16 text-center py-1 border-0 focus:outline-none"
                                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                                
                                                <button type="button" 
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                        {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}
                                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">
                                                R$ {{ number_format($item->unit_price, 2, ',', '.') }} cada
                                            </div>
                                            <div class="text-lg font-bold text-gray-900" id="item-total-{{ $item->id }}">
                                                R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Disponibilidade -->
                                    @if($item->product->stock_quantity < $item->quantity)
                                        <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                            ⚠️ Disponível apenas {{ $item->product->stock_quantity }} unidade(s). Ajuste a quantidade.
                                        </div>
                                    @elseif($item->product->stock_quantity <= 5)
                                        <div class="mt-2 p-2 bg-orange-50 border border-orange-200 rounded text-sm text-orange-700">
                                            🔥 Últimas {{ $item->product->stock_quantity }} unidades em estoque!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Ações do Carrinho -->
                <div class="flex justify-between items-center mt-4">
                    <a href="{{ route('products.index') }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        ← Continuar Comprando
                    </a>
                    
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Limpar todo o carrinho?')"
                                class="text-red-600 hover:text-red-800 font-medium">
                            Limpar Carrinho
                        </button>
                    </form>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->sum('quantity') }} itens)</span>
                            <span class="font-medium" id="cart-subtotal">R$ {{ number_format($cart->total_amount, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-green-600">A calcular</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-green-600" id="cart-total">R$ {{ number_format($cart->total_amount, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Calcular Frete -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-800 mb-2">Calcular frete:</h3>
                        <div class="flex gap-2">
                            <input type="text" 
                                   placeholder="CEP de entrega" 
                                   class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button class="bg-gray-800 text-white px-3 py-2 rounded-md hover:bg-gray-700 text-sm">
                                Calcular
                            </button>
                        </div>
                    </div>

                    <!-- Botão de Checkout -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('checkout.index') }}" 
                           class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 block text-center">
                            Finalizar Compra
                        </a>
                        
                        @guest
                            <p class="text-xs text-center text-gray-600">
                                Você pode finalizar a compra sem cadastro ou 
                                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">fazer login</a>
                            </p>
                        @endguest
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="mt-6">
                        <h3 class="font-medium text-gray-800 mb-3">Formas de pagamento:</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                PIX (aprovação imediata)
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Cartão de crédito (até 12x)
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Boleto bancário
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Carrinho Vazio -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Seu carrinho está vazio</h2>
                <p class="text-gray-600 mb-8">Que tal explorar nossos produtos e encontrar algo interessante?</p>
                
                <a href="{{ route('products.index') }}" 
                   class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-block">
                    Explorar Produtos
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function updateQuantity(itemId, quantity) {
    if (quantity < 1) return;
    
    fetch(`/cart/update/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({quantity: quantity})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`item-total-${itemId}`).textContent = 
                'R$ ' + parseFloat(data.item_total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('cart-subtotal').textContent = 
                'R$ ' + parseFloat(data.cart_total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('cart-total').textContent = 
                'R$ ' + parseFloat(data.cart_total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        } else {
            alert(data.error || 'Erro ao atualizar quantidade');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao atualizar carrinho');
    });
}
</script>
@endsection