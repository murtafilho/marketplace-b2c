{{--
Arquivo: resources/views/shop/checkout/index.blade.php
Descrição: Página de checkout/finalização da compra
Laravel Version: 12.x
Criado em: 29/08/2025
--}}
<x-layouts.marketplace>
    <x-slot name="title">Finalizar Compra</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Finalizar Compra</h1>
                <p class="text-gray-600 mt-2">Complete as informações para finalizar seu pedido</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulário de checkout -->
                <div class="lg:col-span-2 space-y-8">
                    <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                        @csrf
                        
                        <!-- Dados de cobrança -->
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Dados de cobrança</h2>
                            </div>
                            
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                                    <input type="text" 
                                           name="billing_address[name]" 
                                           id="billing_name"
                                           value="{{ old('billing_address.name', auth()->user()->name) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.name')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" 
                                           name="billing_address[email]" 
                                           id="billing_email"
                                           value="{{ old('billing_address.email', auth()->user()->email) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                    <input type="tel" 
                                           name="billing_address[phone]" 
                                           id="billing_phone"
                                           value="{{ old('billing_address.phone', auth()->user()->phone) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.phone')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                                    <input type="text" 
                                           name="billing_address[address]" 
                                           id="billing_address"
                                           value="{{ old('billing_address.address') }}"
                                           placeholder="Rua, número, complemento"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.address')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                    <input type="text" 
                                           name="billing_address[city]" 
                                           id="billing_city"
                                           value="{{ old('billing_address.city') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.city')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <input type="text" 
                                           name="billing_address[state]" 
                                           id="billing_state"
                                           value="{{ old('billing_address.state') }}"
                                           placeholder="Ex: SP"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.state')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                                    <input type="text" 
                                           name="billing_address[postal_code]" 
                                           id="billing_postal_code"
                                           value="{{ old('billing_address.postal_code') }}"
                                           placeholder="00000-000"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                    @error('billing_address.postal_code')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Método de pagamento -->
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Método de pagamento</h2>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="relative">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="pix" 
                                               class="sr-only peer"
                                               {{ old('payment_method', 'pix') === 'pix' ? 'checked' : '' }}
                                               required>
                                        <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300 transition-colors">
                                            <div class="flex flex-col items-center text-center">
                                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-2 peer-checked:bg-indigo-200">
                                                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1.5 14.5L6 12l1.5-1.5L10 13l6-6 1.5 1.5L10 16.5z"/>
                                                    </svg>
                                                </div>
                                                <h3 class="font-semibold text-gray-900">PIX</h3>
                                                <p class="text-sm text-gray-600">Aprovação instantânea</p>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="credit_card" 
                                               class="sr-only peer"
                                               {{ old('payment_method') === 'credit_card' ? 'checked' : '' }}>
                                        <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300 transition-colors">
                                            <div class="flex flex-col items-center text-center">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2 peer-checked:bg-indigo-200">
                                                    <svg class="w-6 h-6 text-gray-600 peer-checked:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                    </svg>
                                                </div>
                                                <h3 class="font-semibold text-gray-900">Cartão</h3>
                                                <p class="text-sm text-gray-600">Parcelamento disponível</p>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="boleto" 
                                               class="sr-only peer"
                                               {{ old('payment_method') === 'boleto' ? 'checked' : '' }}>
                                        <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300 transition-colors">
                                            <div class="flex flex-col items-center text-center">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2 peer-checked:bg-indigo-200">
                                                    <svg class="w-6 h-6 text-gray-600 peer-checked:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <h3 class="font-semibold text-gray-900">Boleto</h3>
                                                <p class="text-sm text-gray-600">Vencimento em 3 dias</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                @error('payment_method')
                                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-indigo-600 text-white py-3 px-8 rounded-md text-lg font-semibold hover:bg-indigo-700 transition-colors"
                                    id="submit-button">
                                Finalizar Pedido
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Resumo do pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border sticky top-4">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Resumo do pedido</h2>
                        </div>
                        
                        <div class="p-6">
                            <!-- Items agrupados por vendedor -->
                            @foreach($itemsBySeller as $sellerId => $sellerItems)
                                @php $seller = $sellerItems->first()->product->seller @endphp
                                <div class="mb-6 {{ !$loop->last ? 'border-b border-gray-200 pb-4' : '' }}">
                                    <h3 class="font-medium text-gray-900 mb-3">
                                        {{ $seller->user->name }}
                                    </h3>
                                    
                                    <div class="space-y-3">
                                        @foreach($sellerItems as $item)
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if($item->product->images->first())
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                             alt="{{ $item->product->name }}" 
                                                             class="h-12 w-12 object-cover rounded">
                                                    @else
                                                        <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $item->product->name }}
                                                    </p>
                                                    
                                                    @if($item->variation)
                                                        <p class="text-xs text-gray-500">
                                                            {{ $item->variation->name }}: {{ $item->variation->value }}
                                                        </p>
                                                    @endif
                                                    
                                                    <p class="text-xs text-gray-500">
                                                        {{ $item->quantity }}x R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                
                                                <div class="text-sm font-medium text-gray-900">
                                                    R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="space-y-3 pt-4 border-t border-gray-200">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">R$ {{ number_format($cart->total_amount, 2, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Frete</span>
                                    <span class="text-gray-600">Grátis</span>
                                </div>
                                
                                <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                                    <span>Total</span>
                                    <span>R$ {{ number_format($cart->total_amount, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('checkout-form');
        const submitButton = document.getElementById('submit-button');
        
        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.textContent = 'Processando...';
        });
        
        // Auto-format CEP
        const cepInput = document.getElementById('billing_postal_code');
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
        
        // Auto-format phone
        const phoneInput = document.getElementById('billing_phone');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 11) {
                value = value.substring(0, 2) + ' ' + value.substring(2, 7) + '-' + value.substring(7, 11);
            } else if (value.length >= 7) {
                value = value.substring(0, 2) + ' ' + value.substring(2, 6) + '-' + value.substring(6);
            } else if (value.length >= 2) {
                value = value.substring(0, 2) + ' ' + value.substring(2);
            }
            e.target.value = value;
        });
    });
    </script>
</x-layouts.marketplace>