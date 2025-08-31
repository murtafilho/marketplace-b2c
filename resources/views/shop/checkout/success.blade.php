{{--
Arquivo: resources/views/shop/checkout/success.blade.php
Descrição: Página de sucesso após finalização da compra
Laravel Version: 12.x
Criado em: 29/08/2025
--}}
@extends('layouts.marketplace-simple')

@section('title', 'Pedido Realizado com Sucesso')

@section('content')

    <div class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Ícone de sucesso -->
            <div class="mb-8">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <!-- Mensagem de sucesso -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Pedido realizado com sucesso!
            </h1>
            
            <p class="text-lg text-gray-600 mb-8">
                Obrigado pela sua compra. Seu pedido foi processado e você receberá um email com os detalhes.
            </p>
            
            <!-- Informações do pedido -->
            <div class="bg-white rounded-lg shadow-sm border p-8 mb-8">
                <div class="grid grid-cols-2 gap-6 text-left">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Número do Pedido</h3>
                        <p class="text-xl font-semibold text-gray-900 mt-1">{{ $order->order_number }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total</h3>
                        <p class="text-xl font-semibold text-gray-900 mt-1">
                            R$ {{ number_format($order->total, 2, ',', '.') }}
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Método de Pagamento</h3>
                        <p class="text-lg text-gray-900 mt-1 capitalize">
                            {{ $order->payment_method === 'pix' ? 'PIX' : ($order->payment_method === 'credit_card' ? 'Cartão de Crédito' : 'Boleto') }}
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status</h3>
                        <p class="text-lg text-gray-900 mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Pendente
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Instruções de pagamento -->
            @if($order->payment_method === 'pix')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3 text-left">
                            <h3 class="text-sm font-medium text-blue-800">Instruções para pagamento PIX</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>1. Abra o aplicativo do seu banco</p>
                                <p>2. Escaneie o QR Code enviado por email</p>
                                <p>3. Confirme o pagamento</p>
                                <p class="mt-2 font-medium">O pagamento será confirmado em poucos minutos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($order->payment_method === 'boleto')
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3 text-left">
                            <h3 class="text-sm font-medium text-orange-800">Instruções para pagamento do Boleto</h3>
                            <div class="mt-2 text-sm text-orange-700">
                                <p>1. Verifique sua caixa de entrada</p>
                                <p>2. Baixe e imprima o boleto</p>
                                <p>3. Pague até a data de vencimento</p>
                                <p class="mt-2 font-medium">Vencimento: {{ now()->addDays(3)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Ações -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Voltar ao início
                </a>
                
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Continuar comprando
                </a>
            </div>
            
            <!-- Informações de contato -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-gray-600 text-sm">
                    Tem alguma dúvida sobre seu pedido? 
                    <a href="mailto:suporte@marketplace.com" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Entre em contato conosco
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection