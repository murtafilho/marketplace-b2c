{{-- Product Card Inclusivo - Vale do Sol --}}
<div x-data="{ 
    imageIndex: 0,
    quickView: false 
}"
class="bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
    
    {{-- Badges --}}
    <div class="absolute top-2 left-2 z-10 flex flex-col space-y-1">
        @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
        <span class="bg-red-500 text-white px-2 py-1 text-xs font-bold rounded">
            -{{ $product->discount_percentage }}%
        </span>
        @endif
    </div>
    
    {{-- Quick Actions --}}
    <div class="absolute top-2 right-2 z-10 flex flex-col space-y-1 opacity-0 group-hover:opacity-100 transition-opacity">
        <button @click="$store.cart.addItem({{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price]) }})"
                class="bg-white/90 backdrop-blur-sm rounded-full p-2 hover:bg-white transition-colors">
            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </button>
        
        <button class="bg-white/90 backdrop-blur-sm rounded-full p-2 hover:bg-white transition-colors">
            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>
    
    {{-- Image Gallery --}}
    <a href="{{ route('products.show', $product->id) }}" class="block">
        <div class="relative aspect-square overflow-hidden">
            @if($product->images && $product->images->count() > 0)
                <div class="flex transition-transform duration-300"
                     :style="`transform: translateX(-${imageIndex * 100}%)`">
                    @foreach($product->images as $index => $image)
                    <img src="{{ $image->image_path }}" 
                         alt="{{ $product->name }}"
                         loading="lazy"
                         class="w-full h-full object-cover flex-shrink-0">
                    @endforeach
                </div>
                
                {{-- Image Dots --}}
                @if($product->images->count() > 1)
                <div class="absolute bottom-2 left-0 right-0 flex justify-center space-x-1">
                    @foreach($product->images as $index => $image)
                    <button @click.prevent="imageIndex = {{ $index }}"
                            class="w-2 h-2 rounded-full transition-all"
                            :class="imageIndex === {{ $index }} ? 'bg-white w-4' : 'bg-white/50'">
                    </button>
                    @endforeach
                </div>
                @endif
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </div>
    </a>
    
    {{-- Product Info --}}
    <div class="p-4">
        {{-- Seller Badge --}}
        <div class="flex items-center space-x-2 mb-2">
            <div class="w-6 h-6 bg-vale-verde rounded-full flex items-center justify-center">
                <span class="text-xs text-white font-medium">
                    {{ strtoupper(substr($product->seller->name ?? 'V', 0, 1)) }}
                </span>
            </div>
            <span class="text-xs text-gray-600 truncate">{{ $product->seller->name ?? 'Vendedor' }}</span>
        </div>
        
        {{-- Product Name --}}
        <a href="{{ route('products.show', $product->id) }}">
            <h3 class="font-medium text-gray-900 line-clamp-2 mb-2 hover:text-vale-verde transition-colors">
                {{ $product->name }}
            </h3>
        </a>
        
        {{-- Price Display --}}
        <div class="space-y-1 mb-3">
            @if(isset($product->old_price) && $product->old_price > $product->price)
            <p class="text-sm text-gray-400 line-through">
                R$ {{ number_format($product->old_price, 2, ',', '.') }}
            </p>
            @endif
            
            <p class="text-xl font-bold text-gray-900">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </p>
            
            {{-- Parcelamento --}}
            <p class="text-sm text-gray-600">
                em até 12x de R$ {{ number_format($product->price / 12, 2, ',', '.') }}
            </p>
            
            {{-- PIX Discount --}}
            <div class="bg-green-50 border border-green-200 rounded px-2 py-1">
                <p class="text-xs text-green-700 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                    R$ {{ number_format($product->price * 0.95, 2, ',', '.') }} no PIX (5% desc.)
                </p>
            </div>
        </div>
        
        {{-- Rating --}}
        @if($product->rating_average > 0)
        <div class="flex items-center space-x-2 mb-3">
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 {{ $i <= $product->rating_average ? 'text-sol-dourado' : 'text-gray-300' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                @endfor
            </div>
            <span class="text-sm text-gray-600">({{ $product->reviews_count ?? 0 }})</span>
        </div>
        @endif
        
        {{-- Action Buttons --}}
        <div class="space-y-2">
            <button @click="$store.cart.addItem({{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price]) }})"
                    class="w-full bg-vale-verde text-white py-2 px-4 rounded-lg font-medium hover:bg-vale-verde-dark transition-colors flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Adicionar</span>
            </button>
            
            <button class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                Comprar Agora
            </button>
        </div>
    </div>
</div>