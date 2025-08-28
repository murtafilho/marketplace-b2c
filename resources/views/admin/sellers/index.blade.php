<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Vendedores
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Filtros e Busca -->
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <form method="GET" class="flex gap-4 items-center">
                            <select name="status" class="rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos os status</option>
                                <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>
                                    Aguardando Aprovação
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                    Aprovados
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    Rejeitados
                                </option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                                    Suspensos
                                </option>
                            </select>

                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Buscar por nome ou email..."
                                   class="rounded-md border-gray-300 shadow-sm">

                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    <!-- Tabela de Vendedores -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vendedor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Empresa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Comissão
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sellers as $seller)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $seller->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $seller->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $seller->company_name ?? '-' }}</div>
                                            <div class="text-sm text-gray-500">{{ $seller->document_number ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($seller->status == 'approved') bg-green-100 text-green-800
                                                @elseif($seller->status == 'pending_approval') bg-yellow-100 text-yellow-800
                                                @elseif($seller->status == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @switch($seller->status)
                                                    @case('approved') Aprovado @break
                                                    @case('pending_approval') Pendente @break
                                                    @case('rejected') Rejeitado @break
                                                    @case('suspended') Suspenso @break
                                                    @default {{ $seller->status }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($seller->commission_rate, 2) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $seller->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.sellers.show', $seller) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                            
                                            @if($seller->status == 'pending_approval')
                                                <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 mr-3"
                                                            onclick="return confirm('Aprovar este vendedor?')">
                                                        Aprovar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum vendedor encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $sellers->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>