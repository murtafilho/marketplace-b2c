<x-layouts.marketplace>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center py-12">
                @if($profile->status === 'pending_approval')
                    <h1 class="text-2xl font-bold mb-4">Aguardando Aprovação</h1>
                    <p>Seus documentos estão sendo analisados.</p>
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
