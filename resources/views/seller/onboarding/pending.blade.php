<x-layouts.marketplace>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center py-12">
                @if($profile->status === 'pending_approval')
                    <h1 class="text-2xl font-bold mb-4">Conta Pendente de Aprovação</h1>
                    <p class="text-lg text-gray-600 mb-4">Aguardando Aprovação</p>
                    <p>Seus documentos estão sendo analisados.</p>
                    
                    @if($profile->company_name)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Dados Enviados:</h3>
                            <p><strong>Empresa:</strong> {{ $profile->company_name }}</p>
                            <p><strong>Documento:</strong> {{ $profile->document_type }} - {{ $profile->document_number }}</p>
                            <p><strong>Cidade:</strong> {{ $profile->city }}, {{ $profile->state }}</p>
                            <p><strong>Status:</strong> 
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">
                                    Aguardando Aprovação
                                </span>
                            </p>
                        </div>
                    @endif
                @elseif($profile->status === 'rejected')
                    <h1 class="text-2xl font-bold mb-4">Cadastro Rejeitado</h1>
                    @if($profile->rejection_reason)
                        <p class="text-red-600 mb-4">{{ $profile->rejection_reason }}</p>
                    @endif
                    <a href="{{ route('seller.onboarding.index') }}" class="text-blue-600">Atualizar Cadastro</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.marketplace>
