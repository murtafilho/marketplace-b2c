<x-layouts.marketplace>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.sellers.index') }}" 
                   class="text-indigo-600 hover:text-indigo-500 mr-4">
                    ← Voltar à lista
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $seller->business_name }}</h1>
                    <p class="text-gray-600">{{ $seller->user->name }} • {{ $seller->user->email }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Seller Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informações do Vendedor</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nome do Negócio</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $seller->business_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Proprietário</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $seller->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $seller->user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $seller->phone ?? $seller->user->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($seller->document_number)
                                        {{ strtoupper($seller->document_type) }}: {{ $seller->document_number }}
                                    @else
                                        <span class="text-gray-400">Não informado</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Comissão</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($seller->commission_rate, 1) }}%</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Address Information -->
                @if($seller->address)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Endereço</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cidade</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->state }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">CEP</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->postal_code }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif

                <!-- Bank Information -->
                @if($seller->bank_name)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Dados Bancários</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Banco</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->bank_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Conta</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $seller->bank_account }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif

                <!-- Documents -->
                @if($seller->address_proof_path || $seller->identity_proof_path)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Documentos</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @if($seller->address_proof_path)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Comprovante de Endereço</p>
                                                <p class="text-sm text-gray-500">Documento enviado pelo vendedor</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.sellers.download-document', [$seller, 'address_proof']) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Download
                                        </a>
                                    </div>
                                @endif

                                @if($seller->identity_proof_path)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Documento de Identidade</p>
                                                <p class="text-sm text-gray-500">{{ $seller->document_type === 'cpf' ? 'RG ou CNH' : 'Contrato Social ou CNPJ' }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.sellers.download-document', [$seller, 'identity_proof']) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Download
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 text-gray-800',
                                'pending_approval' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'suspended' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$seller->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $seller->status)) }}
                        </span>

                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <p>Cadastrado: {{ $seller->created_at->format('d/m/Y H:i') }}</p>
                            @if($seller->submitted_at)
                                <p>Enviado: {{ $seller->submitted_at->format('d/m/Y H:i') }}</p>
                            @endif
                            @if($seller->approved_at)
                                <p>Aprovado: {{ $seller->approved_at->format('d/m/Y H:i') }}</p>
                            @endif
                            @if($seller->rejected_at)
                                <p>Rejeitado: {{ $seller->rejected_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>

                        @if($seller->rejection_reason)
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm font-medium text-red-800">Motivo da Rejeição:</p>
                                <p class="text-sm text-red-700 mt-1">{{ $seller->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Ações</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($seller->status === 'pending_approval')
                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" 
                                  onsubmit="return confirm('Tem certeza que deseja aprovar este vendedor?')">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Aprovar Vendedor
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <button type="button" 
                                    onclick="toggleRejectForm()" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Rejeitar Vendedor
                            </button>

                            <!-- Reject Form (Hidden by default) -->
                            <form id="rejectForm" method="POST" action="{{ route('admin.sellers.reject', $seller) }}" 
                                  class="hidden mt-4 p-4 border border-gray-200 rounded-lg">
                                @csrf
                                <div class="mb-3">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                                        Motivo da Rejeição
                                    </label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                              placeholder="Explique o motivo da rejeição..." required></textarea>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" 
                                            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                        Confirmar Rejeição
                                    </button>
                                    <button type="button" onclick="toggleRejectForm()" 
                                            class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        @endif

                        @if($seller->status === 'approved')
                            <!-- Suspend Button -->
                            <form method="POST" action="{{ route('admin.sellers.suspend', $seller) }}" 
                                  onsubmit="return confirm('Tem certeza que deseja suspender este vendedor?')">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Suspender Vendedor
                                </button>
                            </form>
                        @endif

                        @if($seller->status === 'suspended')
                            <!-- Activate Button -->
                            <form method="POST" action="{{ route('admin.sellers.activate', $seller) }}" 
                                  onsubmit="return confirm('Tem certeza que deseja reativar este vendedor?')">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Reativar Vendedor
                                </button>
                            </form>
                        @endif

                        <!-- Update Commission -->
                        <div class="pt-4 border-t border-gray-200">
                            <form method="POST" action="{{ route('admin.sellers.update-commission', $seller) }}">
                                @csrf
                                <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                    Taxa de Comissão (%)
                                </label>
                                <div class="flex">
                                    <input type="number" id="commission_rate" name="commission_rate" 
                                           value="{{ $seller->commission_rate }}" 
                                           min="0" max="50" step="0.1" 
                                           class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    <button type="submit" 
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 text-sm">
                                        Atualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectForm() {
            const form = document.getElementById('rejectForm');
            form.classList.toggle('hidden');
        }
    </script>
</x-layouts.marketplace>