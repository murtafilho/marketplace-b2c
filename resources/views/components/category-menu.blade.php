{{-- Component: Category Menu estilo Mercado Livre --}}
<div x-data="{ 
    openCategory: null, 
    timeoutId: null,
    showMenu: false 
}" class="relative">
    {{-- Botão principal de Categorias --}}
    <button @mouseenter="showMenu = true" 
            @mouseleave="timeoutId = setTimeout(() => showMenu = false, 300)"
            class="flex items-center space-x-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5 text-vale-verde" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
        </svg>
        <span class="font-medium text-gray-700">Categorias</span>
        <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': showMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Menu Dropdown --}}
    <div x-show="showMenu" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @mouseenter="clearTimeout(timeoutId)"
         @mouseleave="timeoutId = setTimeout(() => showMenu = false, 300)"
         class="absolute top-full left-0 mt-1 w-screen max-w-4xl bg-white border border-gray-200 rounded-lg shadow-xl z-50"
         style="display: none;">
        
        <div class="flex">
            {{-- Lista de Categorias Principais --}}
            <div class="w-1/3 border-r border-gray-200">
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Todas as categorias</h3>
                    <div class="space-y-1">
                        @foreach($categories as $category)
                            <a href="{{ route('category.show', $category->slug) }}"
                               @mouseenter="openCategory = {{ $category->id }}"
                               class="flex items-center justify-between p-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-md transition-colors group">
                                <div class="flex items-center space-x-3">
                                    {{-- Ícone da categoria --}}
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-blue-100">
                                        @switch($category->slug)
                                            @case('eletronicos')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/></svg>
                                                @break
                                            @case('roupas-e-acessorios')
                                            @case('moda-e-vestuario')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8a3 3 0 016 0v1h1a1 1 0 011 1v8a1 1 0 01-1 1H6a1 1 0 01-1-1v-8a1 1 0 011-1h1V8z"/></svg>
                                                @break
                                            @case('casa-e-jardim')
                                            @case('casa-e-decoracao')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                                                @break
                                            @case('esportes-e-fitness')
                                            @case('esportes-e-lazer')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>
                                                @break
                                            @case('automotivo')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/></svg>
                                                @break
                                            @default
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                                        @endswitch
                                    </div>
                                    <div>
                                        <span class="font-medium">{{ $category->name }}</span>
                                        @if($category->children->count() > 0)
                                            <span class="text-xs text-gray-500 block">{{ $category->children->count() }} subcategorias</span>
                                        @endif
                                    </div>
                                </div>
                                @if($category->children->count() > 0)
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Painel de Subcategorias --}}
            <div class="flex-1">
                @foreach($categories as $category)
                    @if($category->children->count() > 0)
                        <div x-show="openCategory === {{ $category->id }}" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span>{{ $category->name }}</span>
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="ml-2 text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
                            </h4>
                            
                            {{-- Grid de Subcategorias --}}
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($category->children as $subcategory)
                                    <a href="{{ route('category.show', $subcategory->slug) }}" 
                                       class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">{{ strtoupper(substr($subcategory->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $subcategory->name }}</span>
                                            @if(isset($subcategory->products_count) && $subcategory->products_count > 0)
                                                <span class="text-xs text-gray-500 block">{{ $subcategory->products_count }} produtos</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
                
                {{-- Estado padrão quando nenhuma categoria está selecionada --}}
                <div x-show="openCategory === null" class="p-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                    </svg>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Explore nossas categorias</h4>
                    <p class="text-gray-500">Passe o mouse sobre uma categoria para ver as subcategorias</p>
                </div>
            </div>
        </div>
    </div>
</div>