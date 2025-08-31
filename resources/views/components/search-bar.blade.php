{{-- Search Bar com Autocomplete --}}
<div x-data="{
    search: '{{ request('q', '') }}',
    results: [],
    loading: false,
    showResults: false,
    selectedIndex: -1,
    
    async performSearch() {
        if (this.search.length < 2) {
            this.results = [];
            this.showResults = false;
            return;
        }
        
        this.loading = true;
        
        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(this.search)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.results = data.slice(0, 8);
                this.showResults = true;
            }
        } catch (error) {
            console.warn('Search error:', error);
            this.results = [];
        } finally {
            this.loading = false;
        }
    },
    
    selectResult(index) {
        if (this.results[index]) {
            window.location.href = `/products/${this.results[index].id}`;
        }
    },
    
    submitSearch() {
        if (this.search.trim()) {
            window.location.href = `/products?q=${encodeURIComponent(this.search.trim())}`;
        }
    }
}" 
data-search-container
class="relative w-full">
    
    <form @submit.prevent="submitSearch()" class="relative">
        <input 
            type="search" 
            x-model="search"
            @input.debounce.300ms="performSearch()"
            @focus="showResults = true"
            @click.away="showResults = false"
            @keydown.arrow-down.prevent="selectedIndex = Math.min(selectedIndex + 1, results.length - 1)"
            @keydown.arrow-up.prevent="selectedIndex = Math.max(selectedIndex - 1, -1)"
            @keydown.enter.prevent="selectedIndex >= 0 ? selectResult(selectedIndex) : submitSearch()"
            @keydown.escape="showResults = false"
            placeholder="Buscar produtos, vendedores..."
            data-search-input
            class="w-full px-4 py-3 pl-12 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-comercio-azul focus:border-transparent transition-all duration-200 bg-white"
        >
        
        {{-- Search Icon --}}
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        
        {{-- Loading Spinner --}}
        <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
            <svg class="animate-spin h-5 w-5 text-comercio-azul" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        {{-- Clear button --}}
        <button x-show="search.length > 0" 
                @click="search = ''; results = []; showResults = false"
                type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </form>
    
    {{-- Results Dropdown --}}
    <div x-show="showResults && results.length > 0" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-elevated z-50 max-h-96 overflow-y-auto">
        
        <div class="py-2">
            <template x-for="(result, index) in results" :key="result.id">
                <button @click="selectResult(index)" 
                        @mouseenter="selectedIndex = index"
                        class="w-full flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 text-left"
                        :class="{ 'bg-gray-50': selectedIndex === index }">
                    
                    {{-- Product Image --}}
                    <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                        <img :src="result.image || '/images/placeholder-product.jpg'" 
                             :alt="result.name"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    
                    {{-- Product Info --}}
                    <div class="ml-3 flex-1 min-w-0">
                        <p x-text="result.name" class="text-sm font-medium text-gray-900 truncate"></p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span x-text="`R$ ${parseFloat(result.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})}`" 
                                  class="text-sm font-semibold text-comercio-azul"></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span x-text="result.seller_name" class="text-xs text-gray-500 truncate"></span>
                        </div>
                    </div>
                    
                    {{-- Badges --}}
                    <div class="flex flex-col items-end space-y-1">
                        <template x-if="result.discount_percentage">
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">
                                -<span x-text="result.discount_percentage"></span>%
                            </span>
                        </template>
                        
                        <template x-if="result.is_local">
                            <span class="bg-vale-verde-light bg-opacity-20 text-vale-verde text-xs px-2 py-1 rounded-full font-medium">
                                Local
                            </span>
                        </template>
                    </div>
                </button>
            </template>
        </div>
        
        {{-- View All Results --}}
        <div class="border-t border-gray-100 p-3">
            <button @click="submitSearch()" 
                    class="w-full text-center text-sm text-comercio-azul hover:text-comercio-azul-dark font-medium">
                Ver todos os resultados para "<span x-text="search"></span>"
            </button>
        </div>
    </div>
    
    {{-- No Results --}}
    <div x-show="showResults && search.length >= 2 && results.length === 0 && !loading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-elevated z-50 p-6 text-center">
        
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        
        <p class="text-gray-700 font-medium mb-1">Nenhum resultado encontrado</p>
        <p class="text-sm text-gray-500 mb-4">Tente buscar com outras palavras-chave</p>
        
        <div class="flex flex-wrap gap-2 justify-center">
            <button @click="search = 'eletrônicos'; performSearch()" 
                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200">
                eletrônicos
            </button>
            <button @click="search = 'roupas'; performSearch()" 
                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200">
                roupas
            </button>
            <button @click="search = 'casa'; performSearch()" 
                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200">
                casa
            </button>
        </div>
    </div>
</div>