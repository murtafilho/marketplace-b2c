{{-- Mobile-First Product Card Component --}}
<article class="group relative bg-white overflow-hidden">
    {{-- Product Image Container --}}
    <div class="aspect-square w-full overflow-hidden bg-gray-200 
                hover:bg-gray-100 transition-colors duration-200
                sm:aspect-[4/3] lg:aspect-square">
        
        {{-- Main Product Image --}}
        @if($product->images && $product->images->count() > 0)
            <img src="{{ $product->images->first()->url }}" 
                 alt="{{ $product->name }}"
                 class="h-full w-full object-cover object-center 
                        group-hover:scale-105 transition-transform duration-300
                        sm:group-hover:scale-110"
                 loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gray-100">
                <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
            </div>
        @endif
        
        {{-- Mobile-optimized badges --}}
        @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
            <div class="absolute left-2 top-2 z-10">
                <span class="inline-flex items-center rounded-full bg-red-600 px-2 py-1 
                           text-xs font-medium text-white
                           sm:px-3 sm:py-1.5 sm:text-sm">
                    -{{ $product->discount_percentage }}%
                </span>
            </div>
        @endif
        
        @if($product->featured ?? false)
            <div class="absolute right-2 top-2 z-10">
                <span class="inline-flex items-center rounded-full bg-emerald-600 px-2 py-1 
                           text-xs font-medium text-white
                           sm:px-3 sm:py-1.5 sm:text-sm">
                    Destaque
                </span>
            </div>
        @endif
        
        {{-- Touch-friendly quick actions - Hidden on mobile, shown on hover for desktop --}}
        <div class="absolute inset-x-2 bottom-2 z-10 
                    hidden opacity-0 transition-opacity duration-200 
                    group-hover:opacity-100 
                    sm:flex sm:gap-2">
            
            {{-- Add to Cart Button --}}
            <button type="button"
                    onclick="addToCart({{ $product->id }})"
                    class="flex-1 rounded-lg bg-emerald-600 px-3 py-2 
                           text-sm font-medium text-white 
                           hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2
                           transition-colors duration-200">
                <span class="sr-only">Adicionar ao carrinho</span>
                <svg class="mx-auto h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
            </button>
            
            {{-- Quick View Button --}}
            <button type="button"
                    onclick="quickView({{ $product->id }})"
                    class="rounded-lg bg-white/90 p-2 
                           text-gray-700 hover:bg-white hover:text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2
                           backdrop-blur-sm transition-colors duration-200">
                <span class="sr-only">Visualização rápida</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>
        
        {{-- Mobile Add to Cart Button - Always visible on mobile --}}
        <div class="absolute inset-x-2 bottom-2 z-10 sm:hidden">
            <button type="button"
                    onclick="addToCart({{ $product->id }})"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 
                           text-sm font-medium text-white 
                           hover:bg-emerald-700 active:bg-emerald-800
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2
                           transition-colors duration-200
                           touch-manipulation">
                Adicionar ao Carrinho
            </button>
        </div>
    </div>
    
    {{-- Product Information --}}
    <div class="mt-3 space-y-1 px-1
                sm:mt-4 sm:space-y-2 sm:px-0">
        
        {{-- Product Name - Clickable Link --}}
        <h3 class="text-sm font-medium text-gray-900 line-clamp-2
                   hover:text-emerald-600 transition-colors duration-200
                   sm:text-base lg:text-sm xl:text-base">
            <a href="{{ route('products.show', $product) }}" class="focus:outline-none">
                <span class="absolute inset-0 z-0" aria-hidden="true"></span>
                {{ $product->name }}
            </a>
        </h3>
        
        {{-- Category --}}
        @if($product->category)
            <p class="text-xs text-gray-500 sm:text-sm">
                {{ $product->category->name }}
            </p>
        @endif
        
        {{-- Price Section --}}
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                {{-- Sale Price --}}
                @if(isset($product->compare_at_price) && $product->compare_at_price > $product->price)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-bold text-emerald-600
                                   sm:text-base lg:text-sm xl:text-base">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </span>
                        <span class="text-xs text-gray-500 line-through
                                   sm:text-sm">
                            R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                        </span>
                    </div>
                @else
                    <span class="text-sm font-bold text-gray-900
                               sm:text-base lg:text-sm xl:text-base">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                @endif
                
                {{-- Installments (if applicable) --}}
                @if($product->price > 100)
                    <p class="text-xs text-gray-500">
                        ou {{ floor($product->price / 50) }}x de R$ {{ number_format($product->price / floor($product->price / 50), 2, ',', '.') }}
                    </p>
                @endif
            </div>
            
            {{-- Rating (if available) --}}
            @if(isset($product->rating_average) && $product->rating_average > 0)
                <div class="flex items-center">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-3 w-3 {{ $i <= $product->rating_average ? 'text-yellow-400' : 'text-gray-300' }}
                                       sm:h-4 sm:w-4" 
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="ml-1 text-xs text-gray-500">
                        ({{ $product->rating_count ?? 0 }})
                    </span>
                </div>
            @endif
        </div>
        
        {{-- Stock Status --}}
        @if(isset($product->stock_status))
            <div class="mt-2">
                @if($product->stock_status === 'in_stock')
                    <span class="inline-flex items-center text-xs text-emerald-700
                               sm:text-sm">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Em estoque
                    </span>
                @elseif($product->stock_status === 'low_stock')
                    <span class="inline-flex items-center text-xs text-yellow-700
                               sm:text-sm">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                        Últimas unidades
                    </span>
                @else
                    <span class="inline-flex items-center text-xs text-red-700
                               sm:text-sm">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        Indisponível
                    </span>
                @endif
            </div>
        @endif
        
        {{-- Seller Info (on larger screens only) --}}
        @if($product->seller && isset($product->seller->name))
            <div class="mt-2 hidden sm:block">
                <p class="text-xs text-gray-500">
                    Vendido por 
                    <span class="font-medium text-gray-700 hover:text-emerald-600">
                        {{ $product->seller->name }}
                    </span>
                </p>
            </div>
        @endif
    </div>
</article>

<script>
// Touch-optimized JavaScript functions
function addToCart(productId) {
    // Add haptic feedback for mobile
    if ('vibrate' in navigator) {
        navigator.vibrate(50);
    }
    
    // Your add to cart logic here
    console.log('Adding product', productId, 'to cart');
    
    // Show success feedback
    showToast('Produto adicionado ao carrinho!', 'success');
}

function quickView(productId) {
    // Your quick view logic here
    console.log('Quick view for product', productId);
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `fixed bottom-20 left-4 right-4 z-50 rounded-lg p-4 text-white text-sm font-medium ${
        type === 'success' ? 'bg-emerald-600' : 'bg-gray-600'
    } sm:left-auto sm:right-4 sm:w-80`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>