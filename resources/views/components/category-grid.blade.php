{{-- Component: Category Grid Design Limpo e Moderno --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    {{-- Header Limpo e Elegante --}}
    <div class="px-6 py-8 sm:px-8 bg-gradient-to-r from-gray-50/50 to-white border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Explore por <span class="text-vale-verde">Categorias</span>
                </h2>
                <p class="text-gray-600">Descubra a diversidade de produtos locais</p>
            </div>
            <a href="#" class="hidden sm:flex items-center space-x-2 text-vale-verde hover:text-vale-verde-dark font-semibold transition-colors">
                <span>Ver todas</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    
    {{-- Grid de Categorias --}}
    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 sm:gap-6">
            @foreach($categories->take(12) as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group block p-5 bg-white rounded-xl border border-gray-200 hover:border-vale-verde/30 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    
                    {{-- Container do Ícone --}}
                    <div class="flex flex-col items-center text-center">
                        {{-- Imagem ou Ícone da Categoria --}}
                        <div class="w-16 h-16 mb-4 rounded-2xl overflow-hidden flex items-center justify-center group-hover:scale-105 transition-transform duration-300 bg-gradient-to-br from-gray-50 to-gray-100 group-hover:shadow-lg">
                            @if($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}" 
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                     title="IMG: {{ $category->image_path }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 group-hover:text-vale-verde transition-colors">
                            
                            @switch($category->slug)
                                @case('eletronicos')
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                    </svg>
                                    @break
                                
                                @case('roupas-e-acessorios')
                                @case('moda-e-vestuario')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    @break
                                
                                @case('casa-e-jardim')
                                @case('casa-e-decoracao')
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                    </svg>
                                    @break
                                
                                @case('esportes-e-fitness')
                                @case('esportes-e-lazer')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    @break
                                
                                @case('beleza-e-cuidados')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    @break
                                
                                @case('automotivo')
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                    </svg>
                                    @break
                                
                                @case('livros-e-educacao')
                                @case('livros-e-papelaria')
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-.979 0-1.92.13-2.82.378a1 1 0 00-.706 1.365l.935 3.741A2 2 0 004.861 11H8v8.414L9 20l1-.586V11h3.139a2 2 0 001.952-1.516l.935-3.741a1 1 0 00-.706-1.365A7.952 7.952 0 0014.5 4 7.968 7.968 0 0011 4.804V9H9V4.804z"/>
                                    </svg>
                                    @break
                                
                                @case('games-e-entretenimento')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M16 14h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @break
                                
                                @case('alimentos-e-bebidas')
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l1.5 6m4.5-6h.01M19 13h.01M7 19a2 2 0 11-4 0 2 2 0 014 0zM17 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    @break
                                
                                @case('pet-shop')
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v6.114a4 4 0 100 1.772V6.114l10-2V10a4 4 0 100 1.772V3z"/>
                                    </svg>
                                    @break
                                
                                @default
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                    </svg>
                            @endswitch
                                </div>
                            @endif
                        </div>
                        
                        {{-- Nome da Categoria --}}
                        <h3 class="text-sm font-bold text-gray-900 group-hover:text-vale-verde transition-colors mb-2 leading-tight">
                            {{ $category->name }}
                        </h3>
                        
                        {{-- Contador de Produtos --}}
                        @if(isset($category->products_count) && $category->products_count > 0)
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-md group-hover:bg-vale-verde/10 group-hover:text-vale-verde transition-colors">
                                {{ $category->products_count }} produtos
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Em breve</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        
        {{-- Botão Ver Todas --}}
        @if($categories->count() > 12)
            <div class="mt-8 text-center">
                <a href="#" class="inline-flex items-center space-x-2 bg-vale-verde text-white px-6 py-3 rounded-xl font-semibold hover:bg-vale-verde-dark hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <span>Ver todas as {{ $categories->count() }} categorias</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>