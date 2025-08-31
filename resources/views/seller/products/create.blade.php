@extends('layouts.seller')

@section('title', 'Criar Novo Produto')

@section('content')
    <!-- Header personalizado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Criar Novo Produto</h1>
            <p class="text-sm text-gray-600 mt-1">Adicione um novo produto ao seu catálogo</p>
        </div>
        <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">
            Produtos: {{ $currentCount }} / {{ $seller->product_limit }}
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="p-8">
                @csrf
                
                <!-- Campos essenciais visíveis -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-6">📦 Cadastro Rápido de Produto</h3>
                        <p class="text-sm text-gray-600 mb-6">Preencha apenas os campos essenciais para começar</p>
                    </div>

                    <!-- Nome -->
                    <div>
                        <x-input-label for="name" :value="__('Nome do Produto *')" />
                        <x-text-input id="name" name="name" type="text" 
                            class="mt-1 block w-full text-lg" 
                            :value="old('name')" 
                            placeholder="Ex: Smartphone Samsung Galaxy A54"
                            required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Descrição -->
                    <div>
                        <x-input-label for="description" :value="__('Descrição *')" />
                        <textarea id="description" name="description" rows="4" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            placeholder="Descreva seu produto: características principais, benefícios, o que inclui..." required>{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Categoria e Preço na mesma linha -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="category_id" :value="__('Categoria *')" />
                            <select id="category_id" name="category_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Preço (R$) *')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" 
                                class="mt-1 block w-full text-lg font-semibold" 
                                :value="old('price')" 
                                placeholder="0,00"
                                required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                    </div>

                    <!-- Imagens -->
                    <div>
                        <x-input-label for="images" :value="__('Imagens do Produto')" />
                        <input id="images" name="images[]" type="file" multiple accept="image/*" 
                            class="mt-1 block w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-400 focus:border-indigo-500 focus:ring-indigo-500" />
                        <p class="mt-2 text-sm text-gray-500">
                            📷 Opcional: Selecione até 5 imagens (JPG, PNG, WebP - máx. 5MB cada)
                        </p>
                        <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                    </div>
                </div>

                <!-- Campos ocultos com valores padrão -->
                <input type="hidden" name="short_description" value="">
                <input type="hidden" name="compare_at_price" value="">
                <input type="hidden" name="cost" value="">
                <input type="hidden" name="sku" value="">
                <input type="hidden" name="barcode" value="">
                <input type="hidden" name="stock_quantity" value="1">
                <input type="hidden" name="weight" value="">
                <input type="hidden" name="length" value="">
                <input type="hidden" name="width" value="">
                <input type="hidden" name="height" value="">
                <input type="hidden" name="brand" value="">
                <input type="hidden" name="model" value="">
                <input type="hidden" name="warranty_months" value="">
                <input type="hidden" name="meta_title" value="">
                <input type="hidden" name="meta_description" value="">
                <input type="hidden" name="meta_keywords" value="">
                <input type="hidden" name="status" value="draft">

                <!-- Botões -->
                <div class="flex items-center justify-between pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('seller.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        ← Cancelar
                    </a>

                    <div class="space-x-3">
                        <button type="button" onclick="toggleAdvanced()" 
                            class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-50">
                            ⚙️ Mais Opções
                        </button>
                        
                        <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            💾 Criar Produto
                        </button>
                    </div>
                </div>

                <!-- Seção Avançada (inicialmente oculta) -->
                <div id="advanced-section" class="hidden mt-8 pt-6 border-t border-gray-200 space-y-6">
                    <h4 class="font-medium text-gray-900">⚙️ Configurações Avançadas</h4>
                    
                    <!-- Preço comparativo e estoque -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="compare_at_price_visible" :value="__('Preço \"De\" (R$)')" />
                            <x-text-input id="compare_at_price_visible" type="number" step="0.01" min="0.01" 
                                class="mt-1 block w-full" 
                                placeholder="0,00"
                                onchange="document.querySelector('input[name=compare_at_price]').value = this.value" />
                            <p class="text-xs text-gray-500 mt-1">Para mostrar desconto</p>
                        </div>

                        <div>
                            <x-input-label for="stock_quantity_visible" :value="__('Estoque')" />
                            <x-text-input id="stock_quantity_visible" type="number" min="0" 
                                class="mt-1 block w-full" 
                                value="1"
                                onchange="document.querySelector('input[name=stock_quantity]').value = this.value" />
                        </div>
                    </div>

                    <!-- SKU e código de barras -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="sku_visible" :value="__('SKU/Código')" />
                            <x-text-input id="sku_visible" type="text" 
                                class="mt-1 block w-full" 
                                placeholder="Ex: PROD-001"
                                onchange="document.querySelector('input[name=sku]').value = this.value" />
                        </div>

                        <div>
                            <x-input-label for="brand_visible" :value="__('Marca')" />
                            <x-text-input id="brand_visible" type="text" 
                                class="mt-1 block w-full" 
                                placeholder="Ex: Samsung"
                                onchange="document.querySelector('input[name=brand]').value = this.value" />
                        </div>
                    </div>

                    <!-- Dimensões para frete -->
                    <div>
                        <h5 class="font-medium text-gray-700 mb-2">📦 Dimensões para Frete</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <x-input-label for="weight_visible" :value="__('Peso (kg)')" />
                                <x-text-input id="weight_visible" type="number" step="0.001" min="0" 
                                    class="mt-1 block w-full text-sm" 
                                    placeholder="0.5"
                                    onchange="document.querySelector('input[name=weight]').value = this.value" />
                            </div>
                            <div>
                                <x-input-label for="length_visible" :value="__('Comp. (cm)')" />
                                <x-text-input id="length_visible" type="number" step="0.01" min="0" 
                                    class="mt-1 block w-full text-sm" 
                                    placeholder="20"
                                    onchange="document.querySelector('input[name=length]').value = this.value" />
                            </div>
                            <div>
                                <x-input-label for="width_visible" :value="__('Larg. (cm)')" />
                                <x-text-input id="width_visible" type="number" step="0.01" min="0" 
                                    class="mt-1 block w-full text-sm" 
                                    placeholder="15"
                                    onchange="document.querySelector('input[name=width]').value = this.value" />
                            </div>
                            <div>
                                <x-input-label for="height_visible" :value="__('Alt. (cm)')" />
                                <x-text-input id="height_visible" type="number" step="0.01" min="0" 
                                    class="mt-1 block w-full text-sm" 
                                    placeholder="5"
                                    onchange="document.querySelector('input[name=height]').value = this.value" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Alternar seção avançada
        function toggleAdvanced() {
            const section = document.getElementById('advanced-section');
            const button = event.target;
            
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                button.innerHTML = '🔼 Menos Opções';
                button.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                section.classList.add('hidden');
                button.innerHTML = '⚙️ Mais Opções';
            }
        }

        // Preview das imagens selecionadas
        document.getElementById('images').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 5) {
                alert('Máximo de 5 imagens permitidas');
                e.target.value = '';
                return;
            }
            
            files.forEach(file => {
                if (file.size > 5 * 1024 * 1024) { // 5MB para produtos
                    alert(`Arquivo ${file.name} é muito grande. Máximo 5MB por imagem.`);
                    e.target.value = '';
                    return;
                }
            });
            
            // Mostrar preview simples
            if (files.length > 0) {
                const label = this.previousElementSibling;
                label.textContent = `📷 Imagens do Produto (${files.length} selecionada${files.length > 1 ? 's' : ''})`;
                label.style.color = '#059669';
            }
        });

        // Auto-gerar slug/SKU baseado no nome
        document.getElementById('name').addEventListener('blur', function(e) {
            const name = e.target.value;
            const skuField = document.getElementById('sku_visible');
            
            if (name && !skuField.value) {
                // Gerar SKU simples baseado no nome
                const sku = name
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 10) + 
                    '-' + Math.floor(Math.random() * 1000);
                
                skuField.value = sku;
                document.querySelector('input[name=sku]').value = sku;
            }
        });
    </script>
@endsection