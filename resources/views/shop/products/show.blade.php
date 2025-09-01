@extends('layouts.base')

@section('title', $product->name . ' - Marketplace')

@section('content')
<!-- Mobile-First Product Detail Page -->
<div class="space-y-6">
    
    <!-- Mobile Breadcrumb -->
    <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-emerald-600">Início</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-emerald-600">Produtos</a></li>
            @if($product->category)
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('products.category', $product->category) }}" class="hover:text-emerald-600">{{ $product->category->name }}</a></li>
            @endif
            <li><span class="text-gray-400">/</span></li>
            <li class="text-gray-900 truncate">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>
    
    <!-- Product Images Section -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4">
            @if($product->images && $product->images->count() > 0)
                <!-- Main Image -->
                <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100 mb-4">
                    <img id="mainImage" 
                         src="{{ Storage::url($product->images->first()->file_path) }}" 
                         alt="{{ $product->name }}"
                         class="h-full w-full object-contain">
                </div>
                
                <!-- Thumbnail Gallery -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 lg:grid-cols-8">
                        @foreach($product->images as $image)
                            <button onclick="changeMainImage('{{ Storage::url($image->file_path) }}')" 
                                    class="aspect-square overflow-hidden rounded border-2 border-transparent hover:border-emerald-500 focus:border-emerald-500">
                                <img src="{{ Storage::url($image->file_path) }}" 
                                     alt="{{ $product->name }}"
                                     class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="aspect-square w-full flex items-center justify-center bg-gray-100 rounded-lg">
                    <svg class="h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Product Info Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
        <!-- Product Title and Rating -->
        <div class="space-y-3">
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl lg:text-3xl">{{ $product->name }}</h1>
            
            <!-- Mobile Rating and Seller -->
            <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="text-sm text-gray-600">
                    <span>Vendido por: </span>
                    <span class="font-semibold text-gray-900">{{ $product->sellerProfile->company_name ?? $product->seller->name }}</span>
                </div>
                
                @if(isset($product->rating_average) && $product->rating_average > 0)
                    <div class="flex items-center">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $product->rating_average ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if(isset($product->rating_count) && $product->rating_count > 0)
                            <span class="ml-2 text-sm text-gray-500">({{ $product->rating_count }} avaliações)</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Price Section -->
        <div class="mt-6 border-t border-b border-gray-200 py-6">
            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                <div class="space-y-2">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl font-bold text-emerald-600 sm:text-3xl">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </span>
                        <span class="text-lg text-gray-500 line-through">
                            R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                        </span>
                    </div>
                    <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                        -{{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}% OFF
                    </span>
                </div>
            @else
                <span class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </span>
            @endif
            
            <p class="mt-2 text-sm text-gray-600">
                Em até 12x de R$ {{ number_format($product->price / 12, 2, ',', '.') }} sem juros
            </p>
        </div>
        
        <!-- Stock Status -->
        <div class="mt-6">
            @if($product->stock_quantity > 0)
                <div class="flex items-center text-emerald-600">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">Em estoque</span>
                    @if($product->stock_quantity <= 5)
                        <span class="ml-2 rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800">
                            Restam apenas {{ $product->stock_quantity }} unidades!
                        </span>
                    @endif
                </div>
            @else
                <div class="flex items-center text-red-600">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="font-semibold">Produto indisponível</span>
                </div>
            @endif
        </div>
        
        <!-- Purchase Form -->
        @if($product->stock_quantity > 0)
            <form action="{{ route('cart.add') }}" method="POST" class="mt-8 space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Quantity Selector -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-3">
                        Quantidade:
                    </label>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                onclick="decreaseQuantity()" 
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                            </svg>
                        </button>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="1" 
                               min="1" 
                               max="{{ $product->stock_quantity }}"
                               class="w-20 rounded-lg border-0 py-2 text-center ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600">
                        <button type="button" 
                                onclick="increaseQuantity()" 
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Add to Cart - Full width on mobile -->
                    <button type="submit" 
                            class="w-full flex items-center justify-center rounded-lg bg-emerald-600 px-6 py-4 text-base font-semibold text-white hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        Adicionar ao Carrinho
                    </button>
                    
                    <!-- Buy Now -->
                    @guest
                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                           class="w-full flex items-center justify-center rounded-lg bg-blue-600 px-6 py-4 text-base font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Comprar Agora
                        </a>
                    @else
                        <button type="submit" 
                                name="buy_now" 
                                value="1"
                                class="w-full flex items-center justify-center rounded-lg bg-blue-600 px-6 py-4 text-base font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Comprar Agora
                        </button>
                    @endguest
                </div>
            </form>
        @endif
        
        <!-- Shipping Calculator -->
        <div class="mt-8 rounded-lg bg-gray-50 p-4">
            <h3 class="mb-3 font-semibold text-gray-900">Calcular frete e prazo</h3>
            <div class="space-y-3">
                <div class="flex space-x-2">
                    <input type="text" 
                           placeholder="Digite seu CEP" 
                           class="flex-1 rounded-lg border-0 py-2 px-3 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 placeholder:text-gray-400 text-sm">
                    <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Calcular
                    </button>
                </div>
                <a href="#" class="inline-block text-sm text-emerald-600 hover:text-emerald-500">Não sei meu CEP</a>
            </div>
        </div>
    </div>
    
    <!-- Product Description -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 sm:text-xl">Descrição do Produto</h2>
        <div class="prose max-w-none text-gray-600 text-sm sm:text-base">
            {!! nl2br(e($product->description)) !!}
        </div>
        
        <!-- Technical Information -->
        @if($product->sku || $product->barcode || $product->weight || ($product->length && $product->width && $product->height))
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="mb-3 font-semibold text-gray-900">Informações Técnicas</h3>
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    @if($product->sku)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">SKU:</dt>
                            <dd class="font-medium text-gray-900">{{ $product->sku }}</dd>
                        </div>
                    @endif
                    @if($product->barcode)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Código de Barras:</dt>
                            <dd class="font-medium text-gray-900">{{ $product->barcode }}</dd>
                        </div>
                    @endif
                    @if($product->weight)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Peso:</dt>
                            <dd class="font-medium text-gray-900">{{ $product->weight }} kg</dd>
                        </div>
                    @endif
                    @if($product->length && $product->width && $product->height)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Dimensões (CxLxA):</dt>
                            <dd class="font-medium text-gray-900">{{ $product->length }}×{{ $product->width }}×{{ $product->height }} cm</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>
    
    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 sm:text-xl">Produtos Relacionados</h2>
            
            <!-- Mobile: Horizontal scroll, Desktop: Grid -->
            <div class="flex gap-4 overflow-x-auto pb-4 sm:grid sm:grid-cols-2 sm:gap-6 sm:overflow-visible sm:pb-0 lg:grid-cols-4">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('products.show', $related) }}" 
                       class="group relative flex-none w-48 sm:w-auto">
                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-100 border border-gray-200">
                            @if($related->images && $related->images->count() > 0)
                                <img src="{{ Storage::url($related->images->first()->file_path) }}" 
                                     alt="{{ $related->name }}"
                                     class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-emerald-600">
                                {{ $related->name }}
                            </h3>
                            <p class="mt-1 text-sm font-bold text-emerald-600">
                                R$ {{ number_format($related->price, 2, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Mobile-Optimized JavaScript -->
<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

// Touch optimization
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback for buttons
    const buttons = document.querySelectorAll('button, a[class*="bg-"]');
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        button.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });
    
    // Image gallery touch optimization
    const thumbnails = document.querySelectorAll('button[onclick*="changeMainImage"]');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('touchstart', function() {
            this.style.opacity = '0.7';
        });
        
        thumb.addEventListener('touchend', function() {
            this.style.opacity = '';
        });
    });
});
</script>
@endsection