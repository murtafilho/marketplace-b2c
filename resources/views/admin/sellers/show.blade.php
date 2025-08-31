@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-white">{{ $seller->company_name }}</h1>
            <p class="mt-2 text-sm text-gray-300">Detalhes do vendedor {{ $seller->user->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.sellers.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar à Lista
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Seller Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informações Básicas</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->user->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->user->email }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Empresa</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->company_name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Documento</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($seller->document_number)
                                    {{ strtoupper($seller->document_type) }}: {{ $seller->document_number }}
                                @else
                                    Não informado
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->phone ?: 'Não informado' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Taxa de Comissão</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($seller->commission_rate, 1) }}%</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cadastro</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Address Information -->
            @if($seller->address)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Endereço</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Endereço Completo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $seller->address }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Business Description -->
            @if($seller->business_description)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Descrição do Negócio</h3>
                    <p class="text-sm text-gray-900">{{ $seller->business_description }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Status</h3>
                    
                    <div class="flex items-center justify-center">
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                            @if($seller->status === 'approved') bg-green-100 text-green-800
                            @elseif($seller->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($seller->status === 'rejected') bg-red-100 text-red-800
                            @elseif($seller->status === 'suspended') bg-gray-100 text-gray-800
                            @endif
                        ">
                            @if($seller->status === 'approved') Aprovado
                            @elseif($seller->status === 'pending') Aguardando Aprovação
                            @elseif($seller->status === 'rejected') Rejeitado
                            @elseif($seller->status === 'suspended') Suspenso
                            @endif
                        </span>
                    </div>

                    @if($seller->approved_at)
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Aprovado em {{ $seller->approved_at->format('d/m/Y H:i') }}
                        </p>
                    @elseif($seller->rejected_at)
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Rejeitado em {{ $seller->rejected_at->format('d/m/Y H:i') }}
                        </p>
                        @if($seller->rejection_reason)
                            <p class="text-xs text-red-600 text-center mt-1">
                                Motivo: {{ $seller->rejection_reason }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ações</h3>
                    
                    <div class="space-y-3">
                        @if($seller->status === 'pending')
                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                                        onclick="return confirm('Aprovar este vendedor?')">
                                    <i class="fas fa-check mr-2"></i>
                                    Aprovar Vendedor
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <button type="button" 
                                    class="w-full flex justify-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                    onclick="showRejectModal()">
                                <i class="fas fa-times mr-2"></i>
                                Rejeitar Vendedor
                            </button>
                        @endif

                        @if($seller->status === 'approved')
                            <!-- Suspend Button -->
                            <form method="POST" action="{{ route('admin.sellers.suspend', $seller) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                        onclick="return confirm('Suspender este vendedor?')">
                                    <i class="fas fa-ban mr-2"></i>
                                    Suspender
                                </button>
                            </form>
                        @endif

                        <!-- Update Commission -->
                        <button type="button" 
                                class="w-full flex justify-center py-2 px-4 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-white hover:bg-blue-50"
                                onclick="showCommissionModal()">
                            <i class="fas fa-percentage mr-2"></i>
                            Alterar Comissão
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Vendedor</h3>
            <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo da Rejeição
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                              class="shadow-sm focus:ring-red-500 focus:border-red-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                              placeholder="Descreva o motivo da rejeição..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                        Rejeitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Commission Modal -->
<div id="commissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Alterar Taxa de Comissão</h3>
            <form method="POST" action="{{ route('admin.sellers.commission', $seller) }}">
                @csrf
                <div class="mb-4">
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Taxa de Comissão (%)
                    </label>
                    <input type="number" name="commission_rate" id="commission_rate" step="0.01" min="0" max="100"
                           value="{{ $seller->commission_rate }}" required
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideCommissionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showCommissionModal() {
    document.getElementById('commissionModal').classList.remove('hidden');
}

function hideCommissionModal() {
    document.getElementById('commissionModal').classList.add('hidden');
}
</script>
@endsection