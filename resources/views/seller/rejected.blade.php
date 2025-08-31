<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cadastro Rejeitado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center py-12">
                    <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-2xl font-bold mb-4 text-red-900">Cadastro Rejeitado</h1>
                    
                    @if($seller->rejection_reason)
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-medium text-red-800 mb-2">Motivo da rejeição:</p>
                            <p class="text-red-700">{{ $seller->rejection_reason }}</p>
                        </div>
                    @endif
                    
                    @if($seller->company_name)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Dados Enviados:</h3>
                            <div class="text-left max-w-md mx-auto">
                                <p><strong>Empresa:</strong> {{ $seller->company_name }}</p>
                                @if($seller->document_number)
                                    <p><strong>Documento:</strong> {{ strtoupper($seller->document_type) }} - {{ $seller->document_number }}</p>
                                @endif
                                @if($seller->city && $seller->state)
                                    <p><strong>Cidade:</strong> {{ $seller->city }}, {{ $seller->state }}</p>
                                @endif
                                <p><strong>Status:</strong> 
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
                                        Rejeitado
                                    </span>
                                </p>
                                @if($seller->rejected_at)
                                    <p><strong>Rejeitado em:</strong> {{ $seller->rejected_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-8">
                        <p class="text-gray-600 mb-4">Você pode corrigir as informações e enviar novamente:</p>
                        <a href="{{ route('seller.onboarding.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Atualizar Cadastro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>