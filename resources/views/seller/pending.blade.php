<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Conta Pendente de Aprovação
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center py-12">
                    <h1 class="text-2xl font-bold mb-4">Aguardando Aprovação</h1>
                    <p>Seus documentos estão sendo analisados.</p>
                    
                    @if($seller->company_name)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Dados Enviados:</h3>
                            <p><strong>Empresa:</strong> {{ $seller->company_name }}</p>
                            <p><strong>Documento:</strong> {{ $seller->document_type }} - {{ $seller->document_number }}</p>
                            <p><strong>Cidade:</strong> {{ $seller->city }}, {{ $seller->state }}</p>
                            <p><strong>Status:</strong> 
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">
                                    Aguardando Aprovação
                                </span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>