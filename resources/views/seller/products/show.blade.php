<x-layouts.marketplace>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('seller.products.index') }}" 
                       class="text-gray-500 hover:text-gray-700 mr-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                        <p class="text-gray-600">
                            Criado em {{ $product->created_at->format('d/m/Y H:i') }}
                            @if($product->updated_at != $product->created_at)
                                • Atualizado em {{ $product->updated_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('seller.products.edit', $product) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Editar
                    </a>
                    
                    <form method="POST" action="{{ route('seller.products.toggle-status', $product) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out"
                                onclick="return confirm('Tem certeza que deseja {{ $product->status === 'active' ? 'desativar' : 'ativar' }} este produto?')">
                            {{ $product->status === 'active' ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Badges -->
            <div class="flex space-x-2 mb-6">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    @if($product->status === 'active') bg-green-100 text-green-800
                    @elseif($product->status === 'inactive') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($product->status === 'active') Ativo
                    @elseif($product->status === 'inactive') Inativo
                    @else Rascunho @endif
                </span>

                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    @if($product->stock_status === 'in_stock') bg-green-100 text-green-800
                    @elseif($product->stock_status === 'out_of_stock') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    @if($product->stock_status === 'in_stock') Em Estoque ({{ $product->stock_quantity }})
                    @elseif($product->stock_status === 'out_of_stock') Sem Estoque
                    @else Sob Encomenda @endif
                </span>

                @if($product->featured)
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        Destaque
                    </span>
                @endif

                @if($product->digital)
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        Digital
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Images -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Imagens</h2>
                    
                    @if($product->images->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($product->images->sortBy('sort_order') as $image)
                                <div class="relative">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $image->alt_text }}" 
                                         class="w-full h-48 object-cover rounded-lg border">
                                    @if($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                            Principal
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Nenhuma imagem cadastrada</p>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrição</h2>
                    
                    @if($product->short_description)
                        <div class="mb-4">
                            <h3 class="font-medium text-gray-900 mb-2">Descrição Curta</h3>
                            <p class="text-gray-700">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    @if($product->description)
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Descrição Completa</h3>
                            <div class="text-gray-700 whitespace-pre-line">{{ $product->description }}</div>
                        </div>
                    @else
                        <p class="text-gray-500">Nenhuma descrição cadastrada</p>
                    @endif
                </div>

                <!-- Variations (if any) -->
                @if($product->variations->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6 mt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Variações</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->variations->sortBy('sort_order') as $variation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $variation->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variation->value }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variation->formatted_final_price }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variation->stock_quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $variation->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $variation->is_active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h2>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Preço de Venda</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $product->formatted_price }}</dd>
                        </div>

                        @if($product->compare_at_price)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Preço Comparativo</dt>
                                <dd class="text-sm text-gray-500 line-through">R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}</dd>
                            </div>
                        @endif

                        @if($product->cost)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Custo</dt>
                                <dd class="text-sm text-gray-900">R$ {{ number_format($product->cost, 2, ',', '.') }}</dd>
                            </div>
                        @endif

                        @if($product->sku)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                <dd class="text-sm text-gray-900">{{ $product->sku }}</dd>
                            </div>
                        @endif

                        @if($product->barcode)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Código de Barras</dt>
                                <dd class="text-sm text-gray-900">{{ $product->barcode }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Dimensions -->
                @if($product->weight || $product->length || $product->width || $product->height)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Dimensões</h2>
                        
                        <dl class="space-y-3">
                            @if($product->weight)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Peso</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->weight }} kg</dd>
                                </div>
                            @endif
                            
                            @if($product->length)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Comprimento</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->length }} cm</dd>
                                </div>
                            @endif
                            
                            @if($product->width)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Largura</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->width }} cm</dd>
                                </div>
                            @endif
                            
                            @if($product->height)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Altura</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->height }} cm</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                <!-- SEO -->
                @if($product->meta_title || $product->meta_description || $product->meta_keywords)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">SEO</h2>
                        
                        <dl class="space-y-3">
                            @if($product->meta_title)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Título SEO</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->meta_title }}</dd>
                                </div>
                            @endif
                            
                            @if($product->meta_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Descrição SEO</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->meta_description }}</dd>
                                </div>
                            @endif
                            
                            @if($product->meta_keywords)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Palavras-chave</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->meta_keywords }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h2>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Visualizações</dt>
                            <dd class="text-sm text-gray-900">{{ $product->views_count }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Vendas</dt>
                            <dd class="text-sm text-gray-900">{{ $product->sales_count }}</dd>
                        </div>
                        
                        @if($product->rating_count > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Avaliação</dt>
                                <dd class="text-sm text-gray-900">{{ $product->rating_average }}/5.00 ({{ $product->rating_count }} avaliações)</dd>
                            </div>
                        @endif
                        
                        @if($product->published_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Publicado em</dt>
                                <dd class="text-sm text-gray-900">{{ $product->published_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.marketplace>