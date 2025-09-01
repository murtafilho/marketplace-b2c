@extends('layouts.base')

@section('title', 'Carrinho de Compras - Marketplace')

@section('content')
<!-- Mobile-First Cart Page -->
<div class="space-y-6">
    
    <!-- Cart Header -->
    <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                🛒 Carrinho de Compras
            </h1>
            @if(isset($cartItems) && $cartItems->count() > 0)
                <p class="mt-1 text-sm text-gray-600">
                    {{ $cartItems->sum('quantity') }} item(s) no carrinho
                </p>
            @endif
        </div>
        
        <!-- Mobile Continue Shopping Button -->
        <div class="sm:hidden">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500">
                <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Continuar Comprando
            </a>
        </div>
    </div>

    @if(isset($cartItems) && $cartItems->count() > 0)
        <!-- Mobile: Stack vertically, Desktop: Two columns -->
        <div class="space-y-6 lg:grid lg:grid-cols-3 lg:gap-8 lg:space-y-0">
            
            <!-- Cart Items - Mobile Full Width, Desktop 2/3 -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($item->product->images && $item->product->images->count() > 0)
                                        <img src="{{ Storage::url($item->product->images->first()->file_path) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="h-20 w-20 rounded-lg object-cover sm:h-24 sm:w-24">
                                    @else
                                        <div class="h-20 w-20 rounded-lg bg-gray-100 flex items-center justify-center sm:h-24 sm:w-24">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <!-- Product Name and Remove Button -->
                                    <div class="flex items-start justify-between">
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 sm:text-base">
                                                <a href="{{ route('products.show', $item->product) }}" 
                                                   class="hover:text-emerald-600 line-clamp-2">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="mt-1 text-xs text-gray-600 sm:text-sm">
                                                Vendido por: {{ $item->product->seller->company_name ?? $item->product->seller->user->name }}
                                            </p>
                                            @if(isset($item->variation))
                                                <p class="mt-1 text-xs text-gray-500">{{ $item->variation->name }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Remove Button -->
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Remover este item do carrinho?')"
                                                    class="p-1 text-gray-400 hover:text-red-500">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Quantity and Price Section -->
                                    <div class="mt-4 flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-600 sm:text-sm">Qtd:</span>
                                            <div class="flex items-center rounded-lg border border-gray-300">
                                                <button type="button" 
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                                        class="flex h-8 w-8 items-center justify-center text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                    </svg>
                                                </button>
                                                
                                                <input type="number" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock_quantity }}"
                                                       class="w-12 border-0 py-1 text-center text-sm focus:ring-0"
                                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                                
                                                <button type="button" 
                                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                        {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}
                                                        class="flex h-8 w-8 items-center justify-center text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Price Information -->
                                        <div class="flex items-center justify-between sm:flex-col sm:items-end">
                                            <div class="text-xs text-gray-500 sm:text-sm">
                                                R$ {{ number_format($item->unit_price, 2, ',', '.') }} cada
                                            </div>
                                            <div class="text-lg font-bold text-gray-900" id="item-total-{{ $item->id }}">
                                                R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock Warning -->
                                    @if($item->product->stock_quantity < $item->quantity)
                                        <div class="mt-3 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                                            <div class="flex items-start">
                                                <svg class="mr-2 mt-0.5 h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                </svg>
                                                <span>Disponível apenas {{ $item->product->stock_quantity }} unidade(s). Ajuste a quantidade.</span>
                                            </div>
                                        </div>
                                    @elseif($item->product->stock_quantity <= 5)
                                        <div class="mt-3 rounded-lg bg-orange-50 border border-orange-200 p-3 text-sm text-orange-700">
                                            <div class="flex items-start">
                                                <svg class="mr-2 mt-0.5 h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.601a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                                </svg>
                                                <span>🔥 Últimas {{ $item->product->stock_quantity }} unidades em estoque!</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Actions - Mobile -->
                <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                    <a href="{{ route('products.index') }}" 
                       class="hidden sm:inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500">
                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Continuar Comprando
                    </a>
                    
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Limpar todo o carrinho?')"
                                class="flex w-full items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 sm:w-auto">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Limpar Carrinho
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary - Mobile Full Width, Desktop 1/3 -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 lg:sticky lg:top-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 sm:text-xl">Resumo do Pedido</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->sum('quantity') }} itens)</span>
                            <span class="font-medium" id="cart-subtotal">R$ {{ number_format($cart->total_amount ?? 0, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-emerald-600">A calcular</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="flex justify-between text-base font-bold sm:text-lg">
                            <span>Total</span>
                            <span class="text-emerald-600" id="cart-total">R$ {{ number_format($cart->total_amount ?? 0, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Shipping Calculator -->
                    <div class="mt-6 rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-3 font-medium text-gray-800">Calcular frete:</h3>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   placeholder="CEP de entrega" 
                                   class="flex-1 rounded-lg border-0 py-2 px-3 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 placeholder:text-gray-400">
                            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Calcular
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('checkout.index') }}" 
                           class="flex w-full items-center justify-center rounded-lg bg-emerald-600 px-6 py-4 text-base font-semibold text-white hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Finalizar Compra
                        </a>
                        
                        @guest
                            <p class="text-center text-xs text-gray-600">
                                Você pode finalizar a compra sem cadastro ou 
                                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-500">fazer login</a>
                            </p>
                        @endguest
                    </div>

                    <!-- Payment Methods -->
                    <div class="mt-6">
                        <h3 class="mb-3 font-medium text-gray-800">Formas de pagamento:</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="mr-2 h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                PIX (aprovação imediata)
                            </div>
                            <div class="flex items-center">
                                <svg class="mr-2 h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Cartão de crédito (até 12x)
                            </div>
                            <div class="flex items-center">
                                <svg class="mr-2 h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Boleto bancário
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-24 w-24 mb-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <h2 class="text-xl font-bold text-gray-900 mb-2 sm:text-2xl">Seu carrinho está vazio</h2>
                <p class="text-gray-600 mb-8 text-sm sm:text-base">Que tal explorar nossos produtos e encontrar algo interessante?</p>
                
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center rounded-lg bg-emerald-600 px-8 py-3 text-base font-semibold text-white hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Explorar Produtos
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Mobile-Optimized JavaScript -->
<script>
function updateQuantity(itemId, quantity) {
    if (quantity < 1) return;
    
    // Add loading state
    const itemTotal = document.getElementById(`item-total-${itemId}`);
    const originalText = itemTotal.textContent;
    itemTotal.textContent = '...';
    
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
            itemTotal.textContent = originalText;
            alert(data.error || 'Erro ao atualizar quantidade');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        itemTotal.textContent = originalText;
        alert('Erro ao atualizar carrinho');
    });
}

// Touch optimization
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback for buttons
    const buttons = document.querySelectorAll('button, a[class*="bg-"]');
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        button.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });
    
    // Optimize quantity input for mobile
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('focus', function() {
            // Select all text for easier mobile editing
            this.select();
        });
    });
});
</script>
@endsection