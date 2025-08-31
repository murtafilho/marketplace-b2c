{{-- Product Grid - Vale do Sol --}}
<div x-data="{
    view: 'grid',
    sortBy: 'relevance',
    showFilters: false
}" 
class="space-y-4">
    
    {{-- Header with View Toggle and Sort --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        {{-- Results Count --}}
        <div class="flex items-center space-x-4">
            <p class="text-gray-600">
                <span class="font-medium">{{ $products->total() ?? count($products) }}</span> produtos encontrados
            </p>
            
            {{-- Mobile Filter Button --}}
            <button @click="showFilters = !showFilters" 
                    class="sm:hidden bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                <span>Filtros</span>
            </button>
        </div>
        
        {{-- View Toggle and Sort --}}
        <div class="flex items-center space-x-3">
            {{-- View Toggle --}}
            <div class="hidden sm:flex bg-gray-100 rounded-lg p-1">
                <button @click="view = 'grid'" 
                        class="p-2 rounded-lg transition-colors"
                        :class="view === 'grid' ? 'bg-white text-vale-verde shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button @click="view = 'list'" 
                        class="p-2 rounded-lg transition-colors"
                        :class="view === 'list' ? 'bg-white text-vale-verde shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            {{-- Sort Dropdown --}}
            <div class="relative" x-data="{ sortOpen: false }">
                <button @click="sortOpen = !sortOpen" 
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center space-x-2">
                    <span>Ordenar</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="sortOpen" 
                     @click.away="sortOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                    
                    <button @click="sortBy = 'relevance'; sortOpen = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            :class="sortBy === 'relevance' ? 'bg-vale-verde/10 text-vale-verde' : ''">
                        Relevância
                    </button>
                    
                    <button @click="sortBy = 'price_asc'; sortOpen = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            :class="sortBy === 'price_asc' ? 'bg-vale-verde/10 text-vale-verde' : ''">
                        Menor Preço
                    </button>
                    
                    <button @click="sortBy = 'price_desc'; sortOpen = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            :class="sortBy === 'price_desc' ? 'bg-vale-verde/10 text-vale-verde' : ''">
                        Maior Preço
                    </button>
                    
                    <button @click="sortBy = 'newest'; sortOpen = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            :class="sortBy === 'newest' ? 'bg-vale-verde/10 text-vale-verde' : ''">
                        Mais Recentes
                    </button>
                    
                    <button @click="sortBy = 'rating'; sortOpen = false"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            :class="sortBy === 'rating' ? 'bg-vale-verde/10 text-vale-verde' : ''">
                        Melhor Avaliados
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Mobile Filters Panel --}}
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="sm:hidden bg-white border border-gray-200 rounded-lg p-4 space-y-4">
        
        {{-- Price Range --}}
        <div>
            <h4 class="font-medium text-gray-900 mb-2">Preço</h4>
            <div class="flex space-x-2">
                <input type="number" placeholder="Mín" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <input type="number" placeholder="Máx" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        
        {{-- Categories --}}
        <div>
            <h4 class="font-medium text-gray-900 mb-2">Categoria</h4>
            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Todas as categorias</option>
                {{-- Add categories here --}}
            </select>
        </div>
        
        {{-- Apply Button --}}
        <button @click="showFilters = false"
                class="w-full bg-vale-verde text-white py-2 px-4 rounded-lg font-medium hover:bg-vale-verde-dark transition-colors">
            Aplicar Filtros
        </button>
    </div>
    
    {{-- Products Grid/List --}}
    <div x-show="view === 'grid'" 
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            @include('components.product-card', ['product' => $product])
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-500 mb-4">Tente ajustar os filtros ou buscar por outros termos.</p>
                <button class="bg-vale-verde text-white px-6 py-2 rounded-lg font-medium hover:bg-vale-verde-dark transition-colors">
                    Ver Todos os Produtos
                </button>
            </div>
        @endforelse
    </div>
    
    {{-- List View --}}
    <div x-show="view === 'list'" class="space-y-4">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 p-4 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                {{-- Product Image --}}
                <div class="w-full sm:w-32 h-48 sm:h-32 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                    @if($product->images && $product->images->first())
                        <img src="{{ $product->images->first()->image_path }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Product Info --}}
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:justify-between h-full">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->seller->name ?? 'Vendedor' }}</p>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ $product->description }}</p>
                        </div>
                        
                        <div class="flex flex-row sm:flex-col sm:items-end justify-between sm:justify-start space-x-4 sm:space-x-0 sm:space-y-2">
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                <p class="text-sm text-gray-600">12x R$ {{ number_format($product->price / 12, 2, ',', '.') }}</p>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button @click="$store.cart.addItem({{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price]) }})"
                                        class="bg-vale-verde text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-vale-verde-dark transition-colors">
                                    Adicionar
                                </button>
                                <a href="{{ route('products.show', $product->id) }}" 
                                   class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-500">Tente ajustar os filtros ou buscar por outros termos.</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if(method_exists($products, 'links') && $products->hasPages())
    <div class="flex justify-center">
        {{ $products->links() }}
    </div>
    @endif
</div>