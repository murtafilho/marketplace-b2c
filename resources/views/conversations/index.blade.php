{{-- 
    Arquivo: resources/views/conversations/index.blade.php
    Descrição: Lista de conversas do usuário
    Laravel Version: 12.x
    Criado em: 03/01/2025
--}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Minhas Conversas</h1>
            
            {{-- Busca --}}
            <div class="relative">
                <input type="text" 
                       id="search-conversations"
                       placeholder="Buscar conversas..." 
                       class="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Lista de Conversas --}}
        <div class="bg-white rounded-lg shadow">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = auth()->user()->isSeller() ? $conversation->customer : $conversation->sellerUser;
                    $unreadCount = auth()->user()->isSeller() ? $conversation->unread_seller : $conversation->unread_customer;
                @endphp
                
                <a href="{{ route('conversations.show', $conversation) }}" 
                   class="block hover:bg-gray-50 transition-colors duration-150">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                {{-- Avatar --}}
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold">
                                        {{ substr($otherUser->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                
                                {{-- Informações da Conversa --}}
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="font-semibold text-gray-900">
                                            {{ $otherUser->name ?? 'Usuário' }}
                                        </h3>
                                        @if($unreadCount > 0)
                                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $conversation->subject ?? 'Sem assunto' }}
                                    </p>
                                    
                                    @if($conversation->lastMessage)
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">
                                            {{ Str::limit($conversation->lastMessage->content, 100) }}
                                        </p>
                                    @endif
                                    
                                    @if($conversation->product)
                                        <div class="flex items-center mt-2 text-xs text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ Str::limit($conversation->product->name, 30) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Meta informações --}}
                            <div class="text-right">
                                <p class="text-xs text-gray-500">
                                    {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'Nova' }}
                                </p>
                                
                                @if($conversation->status === 'archived')
                                    <span class="inline-block mt-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                        Arquivada
                                    </span>
                                @endif
                                
                                @if(auth()->user()->isSeller() && $conversation->priority !== 'normal')
                                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded
                                        {{ $conversation->priority === 'high' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                        {{ $conversation->priority === 'high' ? 'Alta' : 'Baixa' }} prioridade
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma conversa</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece uma conversa com um vendedor ou comprador.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($conversations->hasPages())
            <div class="mt-6">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Busca de conversas
    document.getElementById('search-conversations')?.addEventListener('input', function(e) {
        const query = e.target.value;
        
        if (query.length < 2) return;
        
        // Implementar busca AJAX
        fetch(`{{ route('conversations.search') }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Atualizar lista de conversas
            console.log(data);
        });
    });
</script>
@endpush
@endsection
