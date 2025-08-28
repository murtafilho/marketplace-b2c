<x-layouts.marketplace>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.products.index') }}" 
                   class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Novo Produto</h1>
            </div>
            <p class="text-gray-600">Preencha as informações do seu produto</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" 
              x-data="{ 
                  status: '{{ old('status', 'draft') }}',
                  digital: {{ old('digital', false) ? 'true' : 'false' }},
                  featured: {{ old('featured', false) ? 'true' : 'false' }},
                  previewImages: []
              }">
            @csrf

            <div class="bg-white shadow rounded-lg">
                <!-- Basic Information -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informações Básicas</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nome do Produto')" />
                        <x-text-input id="name" name="name" type="text" 
                                      class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category -->
                    <div>
                        <x-input-label for="category_id" :value="__('Categoria')" />
                        <select id="category_id" name="category_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Descrição Completa')" />
                        <textarea id="description" name="description" rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Descreva seu produto em detalhes...">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Short Description -->
                    <div>
                        <x-input-label for="short_description" :value="__('Descrição Curta')" />
                        <textarea id="short_description" name="short_description" rows="2" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  maxlength="500" placeholder="Descrição resumida (máx. 500 caracteres)">{{ old('short_description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
                    </div>
                </div>

                <!-- Pricing -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Preços</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Preço de Venda (R$)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('price')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Compare Price -->
                        <div>
                            <x-input-label for="compare_at_price" :value="__('Preço Comparativo (R$)')" />
                            <x-text-input id="compare_at_price" name="compare_at_price" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('compare_at_price')" />
                            <p class="mt-1 text-sm text-gray-500">Preço original para mostrar desconto</p>
                            <x-input-error class="mt-2" :messages="$errors->get('compare_at_price')" />
                        </div>

                        <!-- Cost -->
                        <div>
                            <x-input-label for="cost" :value="__('Custo (R$)')" />
                            <x-text-input id="cost" name="cost" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('cost')" />
                            <p class="mt-1 text-sm text-gray-500">Apenas para seu controle</p>
                            <x-input-error class="mt-2" :messages="$errors->get('cost')" />
                        </div>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Estoque</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- SKU -->
                        <div>
                            <x-input-label for="sku" :value="__('SKU (Código)')" />
                            <x-text-input id="sku" name="sku" type="text" 
                                          class="mt-1 block w-full" :value="old('sku')" />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <x-input-label for="stock_quantity" :value="__('Quantidade em Estoque')" />
                            <x-text-input id="stock_quantity" name="stock_quantity" type="number" 
                                          class="mt-1 block w-full" :value="old('stock_quantity', 0)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('stock_quantity')" />
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <x-input-label for="stock_status" :value="__('Status do Estoque')" />
                            <select id="stock_status" name="stock_status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="in_stock" {{ old('stock_status', 'in_stock') === 'in_stock' ? 'selected' : '' }}>Em Estoque</option>
                                <option value="out_of_stock" {{ old('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Sem Estoque</option>
                                <option value="backorder" {{ old('stock_status') === 'backorder' ? 'selected' : '' }}>Sob Encomenda</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('stock_status')" />
                        </div>
                    </div>
                </div>

                <!-- Dimensions (optional) -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Dimensões (Opcional)</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Weight -->
                        <div>
                            <x-input-label for="weight" :value="__('Peso (kg)')" />
                            <x-text-input id="weight" name="weight" type="number" step="0.001" 
                                          class="mt-1 block w-full" :value="old('weight')" />
                            <x-input-error class="mt-2" :messages="$errors->get('weight')" />
                        </div>

                        <!-- Length -->
                        <div>
                            <x-input-label for="length" :value="__('Comprimento (cm)')" />
                            <x-text-input id="length" name="length" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('length')" />
                            <x-input-error class="mt-2" :messages="$errors->get('length')" />
                        </div>

                        <!-- Width -->
                        <div>
                            <x-input-label for="width" :value="__('Largura (cm)')" />
                            <x-text-input id="width" name="width" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('width')" />
                            <x-input-error class="mt-2" :messages="$errors->get('width')" />
                        </div>

                        <!-- Height -->
                        <div>
                            <x-input-label for="height" :value="__('Altura (cm)')" />
                            <x-text-input id="height" name="height" type="number" step="0.01" 
                                          class="mt-1 block w-full" :value="old('height')" />
                            <x-input-error class="mt-2" :messages="$errors->get('height')" />
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Imagens (Máx. 5)</h3>
                </div>
                
                <div class="p-6">
                    <div>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" 
                               class="hidden" 
                               @change="
                                   previewImages = [];
                                   Array.from($event.target.files).forEach(file => {
                                       if (file && file.type.startsWith('image/')) {
                                           const reader = new FileReader();
                                           reader.onload = e => previewImages.push(e.target.result);
                                           reader.readAsDataURL(file);
                                       }
                                   });">
                        
                        <label for="images" 
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Clique para enviar</span> ou arraste e solte
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG ou WebP (MAX. 2MB por imagem)</p>
                            </div>
                        </label>
                        
                        <!-- Image Preview -->
                        <div x-show="previewImages.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4">
                            <template x-for="(image, index) in previewImages" :key="index">
                                <div class="relative">
                                    <img :src="image" class="w-full h-20 object-cover rounded-lg border">
                                    <span x-show="index === 0" class="absolute top-1 left-1 bg-green-500 text-white text-xs px-1 rounded">Principal</span>
                                </div>
                            </template>
                        </div>
                        
                        <x-input-error class="mt-2" :messages="$errors->get('images')" />
                        <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                    </div>
                </div>

                <!-- Settings -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Configurações</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status do Produto')" />
                        <select id="status" name="status" x-model="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="draft">Rascunho</option>
                            <option value="active">Ativo (Visível na loja)</option>
                            <option value="inactive">Inativo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="featured" name="featured" value="1" 
                                   x-model="featured"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="featured" class="ml-2 block text-sm text-gray-900">
                                Produto em Destaque
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="digital" name="digital" value="1" 
                                   x-model="digital"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="digital" class="ml-2 block text-sm text-gray-900">
                                Produto Digital (sem envio físico)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <a href="{{ route('seller.products.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </a>
                    <x-primary-button>
                        {{ __('Criar Produto') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.marketplace>