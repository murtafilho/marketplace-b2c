@extends('layouts.marketplace')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm breadcrumbs mb-6">
        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Produtos</a>
        <span class="mx-2">/</span>
        @if($product->category)
            <a href="{{ route('products.category', $product->category) }}" class="text-blue-600 hover:text-blue-800">{{ $product->category->name }}</a>
            <span class="mx-2">/</span>
        @endif
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Imagens do Produto -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border p-4">
                @if($product->images && $product->images->count() > 0)
                    <img src="{{ Storage::url($product->images->first()->file_path) }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-contain">
                    
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            @foreach($product->images as $image)
                                <img src="{{ Storage::url($image->file_path) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 border">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações do Produto -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <!-- Nome e Vendedor -->
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-600">Vendido por: 
                            <span class="font-semibold">{{ $product->sellerProfile->company_name ?? $product->seller->name }}</span>
                        </p>
                    </div>
                    @if($product->rating_average > 0)
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $product->rating_average)
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-gray-600">({{ $product->rating_count }} avaliações)</span>
                        </div>
                    @endif
                </div>

                <!-- Preço -->
                <div class="border-t border-b py-4 mb-4">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            <span class="text-lg text-gray-500 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                -{{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                            </span>
                        </div>
                    @else
                        <span class="text-3xl font-bold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    @endif
                    
                    <p class="text-sm text-gray-600 mt-2">
                        Em até 12x de R$ {{ number_format($product->price / 12, 2, ',', '.') }} sem juros
                    </p>
                </div>

                <!-- Estoque -->
                <div class="mb-6">
                    @if($product->stock_quantity > 0)
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold">Em estoque</span>
                            @if($product->stock_quantity <= 5)
                                <span class="ml-2 text-orange-600">(Últimas {{ $product->stock_quantity }} unidades!)</span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-semibold">Produto indisponível</span>
                        </div>
                    @endif
                </div>

                <!-- Formulário de Compra -->
                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <!-- Quantidade -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Quantidade:
                            </label>
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="decreaseQuantity()" class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->stock_quantity }}"
                                       class="w-20 text-center border border-gray-300 rounded-md py-2">
                                <button type="button" onclick="increaseQuantity()" class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Adicionar ao Carrinho
                            </button>
                            
                            @guest
                                <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                                   class="bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Comprar Agora
                                </a>
                            @else
                                <button type="submit" 
                                        name="buy_now" 
                                        value="1"
                                        class="bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Comprar Agora
                                </button>
                            @endguest
                        </div>
                    </form>
                @endif

                <!-- Calcular Frete -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-2">Calcular frete e prazo:</h3>
                    <div class="flex gap-2">
                        <input type="text" 
                               placeholder="Digite seu CEP" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Calcular
                        </button>
                    </div>
                    <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Não sei meu CEP</a>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mt-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Descrição do Produto</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($product->description)) !!}
                </div>

                @if($product->sku || $product->barcode)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="font-semibold text-gray-800 mb-2">Informações Técnicas:</h3>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            @if($product->sku)
                                <dt class="text-gray-600">SKU:</dt>
                                <dd class="font-medium">{{ $product->sku }}</dd>
                            @endif
                            @if($product->barcode)
                                <dt class="text-gray-600">Código de Barras:</dt>
                                <dd class="font-medium">{{ $product->barcode }}</dd>
                            @endif
                            @if($product->weight)
                                <dt class="text-gray-600">Peso:</dt>
                                <dd class="font-medium">{{ $product->weight }} kg</dd>
                            @endif
                            @if($product->length && $product->width && $product->height)
                                <dt class="text-gray-600">Dimensões (CxLxA):</dt>
                                <dd class="font-medium">{{ $product->length }}x{{ $product->width }}x{{ $product->height }} cm</dd>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Produtos Relacionados -->
    @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produtos Relacionados</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('products.show', $related) }}" class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                        <div class="aspect-square bg-gray-200 rounded-t-lg overflow-hidden">
                            @if($related->images && $related->images->count() > 0)
                                <img src="{{ Storage::url($related->images->first()->file_path) }}" 
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">{{ $related->name }}</h3>
                            <p class="text-lg font-bold text-blue-600">R$ {{ number_format($related->price, 2, ',', '.') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
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
</script>
@endsection