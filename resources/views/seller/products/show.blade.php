<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seller.products.edit', $product) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                
                @if($product->status == 'draft')
                    <form method="POST" action="{{ route('seller.products.toggle-status', $product) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Publicar este produto?')">
                            Publicar
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('seller.products.toggle-status', $product) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Despublicar este produto?')">
                            Despublicar
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('seller.products.duplicate', $product) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Duplicar este produto?')">
                        Duplicar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Status e Alertas -->
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                @if($product->status == 'active') bg-green-100 text-green-800
                                @elseif($product->status == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @switch($product->status)
                                    @case('active') 
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Ativo
                                        </span>
                                        @break
                                    @case('draft') 
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Rascunho
                                        </span>
                                        @break
                                    @default {{ ucfirst($product->status) }}
                                @endswitch
                            </span>

                            @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                <span class="px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded-full">
                                    ⚠️ Estoque baixo ({{ $product->stock_quantity }} restantes)
                                </span>
                            @elseif($product->stock_quantity == 0)
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">
                                    🚫 Sem estoque
                                </span>
                            @endif

                            @if($product->status == 'draft' && !$product->images()->exists())
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">
                                    📷 Adicione imagens para publicar
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            SKU: {{ $product->sku }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Coluna 1 - Imagens e Informações Básicas -->
                        <div>
                            <!-- Galeria de Imagens -->
                            @if($product->images->count() > 0)
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">Imagens</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($product->images as $image)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                     alt="{{ $image->alt_text }}"
                                                     class="w-full h-40 object-cover rounded-lg {{ $image->is_primary ? 'ring-2 ring-blue-500' : '' }}">
                                                
                                                @if($image->is_primary)
                                                    <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                                        Principal
                                                    </span>
                                                @endif
                                                
                                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                                    <form method="POST" action="{{ route('seller.products.delete-image', $image) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm"
                                                                onclick="return confirm('Remover esta imagem?')">
                                                            Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">Imagens</h3>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">Nenhuma imagem adicionada</p>
                                        <p class="text-xs text-gray-500">Clique em "Editar" para adicionar imagens</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Informações Básicas -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Básicas</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Categoria:</span>
                                        <span class="font-medium">{{ $product->category->name }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Preço:</span>
                                        <span class="font-bold text-green-600">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    @if($product->compare_at_price)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Preço Comparativo:</span>
                                            <span class="line-through text-gray-500">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Desconto:</span>
                                            <span class="font-medium text-red-600">{{ $product->discount_percentage }}% OFF</span>
                                        </div>
                                    @endif
                                    
                                    @if($product->cost)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Custo:</span>
                                            <span class="text-gray-700">R$ {{ number_format($product->cost, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Margem de Lucro:</span>
                                            <span class="font-medium text-blue-600">
                                                R$ {{ number_format($product->price - $product->cost, 2, ',', '.') }}
                                                ({{ number_format((($product->price - $product->cost) / $product->price) * 100, 1) }}%)
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Estoque:</span>
                                        <span class="font-medium {{ $product->stock_quantity <= 5 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $product->stock_quantity }} unidades
                                        </span>
                                    </div>
                                    
                                    @if($product->weight)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Peso:</span>
                                            <span>{{ $product->weight }} kg</span>
                                        </div>
                                    @endif
                                    
                                    @if($product->brand)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Marca:</span>
                                            <span>{{ $product->brand }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($product->model)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Modelo:</span>
                                            <span>{{ $product->model }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($product->warranty_months)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Garantia:</span>
                                            <span>{{ $product->warranty_months }} meses</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Coluna 2 - Descrições e Estatísticas -->
                        <div class="space-y-6">
                            <!-- Descrição -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Descrição</h3>
                                @if($product->short_description)
                                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                        <h4 class="font-medium text-blue-900 mb-2">Descrição Curta</h4>
                                        <p class="text-blue-800">{{ $product->short_description }}</p>
                                    </div>
                                @endif
                                
                                <div class="prose max-w-none">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>

                            <!-- Estatísticas -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($product->views_count) }}</div>
                                        <div class="text-sm text-gray-600">Visualizações</div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ number_format($product->sales_count) }}</div>
                                        <div class="text-sm text-gray-600">Vendas</div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($product->rating_average, 1) }}</div>
                                        <div class="text-sm text-gray-600">Avaliação Média</div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600">{{ number_format($product->rating_count) }}</div>
                                        <div class="text-sm text-gray-600">Avaliações</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Datas -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico</h3>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Criado em:</span>
                                        <span>{{ $product->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Atualizado em:</span>
                                        <span>{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    
                                    @if($product->published_at)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Publicado em:</span>
                                            <span>{{ $product->published_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Dimensões e SEO -->
                            @if($product->length || $product->width || $product->height || $product->meta_title)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Técnicas</h3>
                                    
                                    @if($product->length || $product->width || $product->height)
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-700 mb-2">Dimensões (cm)</h4>
                                            <div class="text-sm text-gray-600">
                                                {{ $product->length ?? '?' }} x {{ $product->width ?? '?' }} x {{ $product->height ?? '?' }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($product->meta_title || $product->meta_description)
                                        <div>
                                            <h4 class="font-medium text-gray-700 mb-2">SEO</h4>
                                            @if($product->meta_title)
                                                <p class="text-sm text-gray-600 mb-1"><strong>Título:</strong> {{ $product->meta_title }}</p>
                                            @endif
                                            @if($product->meta_description)
                                                <p class="text-sm text-gray-600"><strong>Descrição:</strong> {{ $product->meta_description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ações Inferiores -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
                        <a href="{{ route('seller.products.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            ← Voltar para Lista
                        </a>

                        <div class="space-x-2">
                            <a href="{{ route('seller.products.edit', $product) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Editar Produto
                            </a>
                            
                            <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>