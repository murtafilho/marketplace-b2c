{{-- 
    Arquivo: resources/views/conversations/show.blade.php
    Descrição: Visualização de conversa individual (chat)
    Laravel Version: 12.x
    Criado em: 03/01/2025
--}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        {{-- Header da Conversa --}}
        <div class="bg-white rounded-t-lg shadow-sm border-b">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('conversations.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        
                        @php
                            $otherUser = auth()->user()->isSeller() ? $conversation->customer : $conversation->sellerUser;
                        @endphp
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">
                                    {{ substr($otherUser->name ?? 'U', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-900">{{ $otherUser->name ?? 'Usuário' }}</h2>
                                <p class="text-sm text-gray-500">
                                    {{ auth()->user()->isSeller() ? 'Comprador' : 'Vendedor' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Ações --}}
                    <div class="flex items-center space-x-2">
                        @if(auth()->user()->isSeller())
                            <form action="{{ route('conversations.set-priority', $conversation) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="priority" onchange="this.form.submit()" 
                                        class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="low" {{ $conversation->priority === 'low' ? 'selected' : '' }}>Baixa Prioridade</option>
                                    <option value="normal" {{ $conversation->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $conversation->priority === 'high' ? 'selected' : '' }}>Alta Prioridade</option>
                                </select>
                            </form>
                        @endif
                        
                        @if($conversation->status !== 'archived')
                            <form action="{{ route('conversations.archive', $conversation) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
                {{-- Informações do Produto/Pedido --}}
                @if($conversation->product || $conversation->order)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        @if($conversation->product)
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 mr-2">Produto:</span>
                                <a href="{{ route('products.show', $conversation->product) }}" 
                                   class="text-blue-600 hover:underline">
                                    {{ $conversation->product->name }}
                                </a>
                            </div>
                        @endif
                        
                        @if($conversation->order)
                            <div class="flex items-center text-sm mt-1">
                                <span class="text-gray-500 mr-2">Pedido:</span>
                                <span class="font-mono">{{ $conversation->order->order_number }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Área de Mensagens --}}
        <div class="bg-gray-50 h-96 overflow-y-auto p-6" id="messages-container">
            @foreach($conversation->messages as $message)
                <div class="mb-4 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                    <div class="inline-block max-w-xs lg:max-w-md">
                        {{-- Nome do remetente --}}
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $message->sender->name }} 
                            @if($message->sender_type === 'system')
                                <span class="bg-gray-200 text-gray-600 px-1 rounded text-xs">Sistema</span>
                            @endif
                        </div>
                        
                        {{-- Conteúdo da mensagem --}}
                        <div class="rounded-lg px-4 py-2 inline-block
                            {{ $message->sender_id === auth()->id() 
                                ? 'bg-blue-500 text-white' 
                                : 'bg-white text-gray-800 border border-gray-200' }}">
                            
                            {{-- Proposta de Entrega --}}
                            @if($message->type === 'delivery_proposal' && $message->delivery_info)
                                <div class="mb-2">
                                    <div class="font-semibold mb-1">📦 Proposta de Entrega</div>
                                    <div class="text-sm space-y-1">
                                        <div>Tipo: {{ $message->delivery_info['type'] ?? '' }}</div>
                                        <div>Taxa: R$ {{ number_format($message->delivery_info['fee'] ?? 0, 2, ',', '.') }}</div>
                                        @if(isset($message->delivery_info['date']))
                                            <div>Data: {{ \Carbon\Carbon::parse($message->delivery_info['date'])->format('d/m/Y') }}</div>
                                        @endif
                                        @if(isset($message->delivery_info['time']))
                                            <div>Horário: {{ $message->delivery_info['time'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Texto da mensagem --}}
                            <p class="break-words">{{ $message->content }}</p>
                            
                            {{-- Anexos --}}
                            @if($message->attachments && count($message->attachments) > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($message->attachments as $attachment)
                                        <div class="flex items-center space-x-2 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <a href="{{ Storage::url($attachment['path']) }}" 
                                               target="_blank" 
                                               class="underline">
                                                {{ $attachment['name'] }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        {{-- Hora e status --}}
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->is_edited)
                                <span class="ml-1">(editado)</span>
                            @endif
                            @if($message->sender_id === auth()->id())
                                @if($message->is_read)
                                    <span class="ml-1">✓✓</span>
                                @else
                                    <span class="ml-1">✓</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- Acordos de Entrega Ativos --}}
            @if($conversation->activeDeliveryAgreement)
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">✅ Acordo de Entrega Ativo</h4>
                    <p class="text-sm text-green-700">
                        {{ $conversation->activeDeliveryAgreement->description }}
                    </p>
                    <div class="mt-2 text-sm text-green-600">
                        Taxa: R$ {{ number_format($conversation->activeDeliveryAgreement->delivery_fee, 2, ',', '.') }}
                        @if($conversation->activeDeliveryAgreement->estimated_date)
                            | Data: {{ $conversation->activeDeliveryAgreement->estimated_date->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Formulário de Envio --}}
        <div class="bg-white rounded-b-lg shadow-sm border-t">
            <form action="{{ route('conversations.send-message', $conversation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-4">
                    <div class="flex items-end space-x-2">
                        {{-- Campo de mensagem --}}
                        <div class="flex-1">
                            <textarea name="content" 
                                      rows="3" 
                                      placeholder="Digite sua mensagem..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      required></textarea>
                        </div>
                        
                        {{-- Botões de ação --}}
                        <div class="flex flex-col space-y-2">
                            {{-- Anexar arquivo --}}
                            <label class="cursor-pointer text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <input type="file" name="attachments[]" multiple class="hidden" accept="image/*,.pdf,.doc,.docx">
                            </label>
                            
                            {{-- Proposta de entrega --}}
                            <button type="button" 
                                    onclick="openDeliveryProposal()"
                                    class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            
                            {{-- Enviar --}}
                            <button type="submit" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de Proposta de Entrega --}}
<div id="delivery-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Proposta de Entrega</h3>
        
        <form action="{{ route('delivery-agreements.store') }}" method="POST">
            @csrf
            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
            <input type="hidden" name="sub_order_id" value="{{ $conversation->order?->subOrders?->first()?->id ?? '' }}">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de Entrega</label>
                    <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pickup">Retirada no Local</option>
                        <option value="meet_location">Encontro em Local Combinado</option>
                        <option value="custom_delivery">Entrega Personalizada</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Taxa de Entrega (R$)</label>
                    <input type="number" name="delivery_fee" step="0.01" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data Estimada</label>
                    <input type="date" name="estimated_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Horário Estimado</label>
                    <input type="text" name="estimated_time" placeholder="Ex: 14:00 - 16:00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeDeliveryProposal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Enviar Proposta
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll para última mensagem
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    });
    
    // Modal de proposta de entrega
    function openDeliveryProposal() {
        document.getElementById('delivery-modal').classList.remove('hidden');
    }
    
    function closeDeliveryProposal() {
        document.getElementById('delivery-modal').classList.add('hidden');
    }
    
    // Atualização em tempo real (implementar WebSocket/Pusher posteriormente)
    // setInterval(() => {
    //     // Verificar novas mensagens via AJAX
    // }, 5000);
</script>
@endpush
@endsection
