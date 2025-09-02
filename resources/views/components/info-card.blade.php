{{-- Exemplo de Card Informativo usando a paleta azul --}}
@props([
    'title' => 'Título Informativo',
    'description' => 'Descrição do card',
    'icon' => 'info-circle',
    'variant' => 'default' // 'default', 'filled', 'bordered'
])

<div {{ $attributes->merge([
    'class' => match($variant) {
        'filled' => 'bg-info-500 text-white',
        'bordered' => 'bg-white border-2 border-info-200 text-gray-900',
        default => 'bg-info-50 text-info-900'
    } . ' rounded-lg p-6 transition-all duration-200 hover:shadow-md'
]) }}>
    
    {{-- Header com ícone --}}
    <div class="flex items-start space-x-4">
        {{-- Ícone --}}
        <div @class([
            'flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center',
            'bg-info-100 text-info-600' => $variant === 'default',
            'bg-white/20 text-white' => $variant === 'filled',
            'bg-info-500 text-white' => $variant === 'bordered'
        ])>
            @switch($icon)
                @case('info-circle')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                @case('lightbulb')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    @break
                @case('star')
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @break
                @default
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
            @endswitch
        </div>
        
        {{-- Conteúdo --}}
        <div class="flex-1 min-w-0">
            <h3 @class([
                'text-lg font-semibold mb-2',
                'text-info-900' => $variant === 'default',
                'text-white' => $variant === 'filled',
                'text-gray-900' => $variant === 'bordered'
            ])>
                {{ $title }}
            </h3>
            
            <p @class([
                'text-sm leading-relaxed',
                'text-info-700' => $variant === 'default',
                'text-info-100' => $variant === 'filled',
                'text-gray-600' => $variant === 'bordered'
            ])>
                {{ $description }}
            </p>
            
            {{-- Slot para conteúdo adicional --}}
            {{ $slot }}
        </div>
    </div>
</div>

{{--
Exemplo de uso:

<!-- Card padrão (fundo azul claro) -->
<x-info-card 
    title="Dica Importante" 
    description="Esta é uma informação que ajuda o usuário."
    icon="lightbulb" />

<!-- Card preenchido (fundo azul forte) -->
<x-info-card 
    variant="filled"
    title="Destaque Especial" 
    description="Informação de alta prioridade com fundo azul."
    icon="star" />

<!-- Card com borda (fundo branco, borda azul) -->
<x-info-card 
    variant="bordered"
    title="Informação Adicional" 
    description="Card com borda para destaque sutil."
    class="hover:border-info-300" />

<!-- Card com conteúdo customizado -->
<x-info-card title="Card com Botão" description="Card com ação">
    <div class="mt-4">
        <button class="bg-info-600 text-white px-4 py-2 rounded-lg hover:bg-info-700 transition-colors">
            Ação
        </button>
    </div>
</x-info-card>
--}}