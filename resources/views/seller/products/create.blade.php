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

    <div class="max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Coluna 1 - Informações Básicas -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Básicas</h3>
                            </div>

                            <!-- Nome -->
                            <div>
                                <x-input-label for="name" :value="__('Nome do Produto')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Descrição Curta -->
                            <div>
                                <x-input-label for="short_description" :value="__('Descrição Curta')" />
                                <textarea id="short_description" name="short_description" rows="3" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Breve descrição do produto...">{{ old('short_description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
                            </div>

                            <!-- Descrição -->
                            <div>
                                <x-input-label for="description" :value="__('Descrição Completa')" />
                                <textarea id="description" name="description" rows="6" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Descrição detalhada do produto, características, benefícios..." required>{{ old('description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <!-- Categoria -->
                            <div>
                                <x-input-label for="category_id" :value="__('Categoria')" />
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

                            <!-- Imagens -->
                            <div>
                                <x-input-label for="images" :value="__('Imagens do Produto')" />
                                <input id="images" name="images[]" type="file" multiple accept="image/*" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                <p class="mt-2 text-sm text-gray-500">
                                    Selecione até 5 imagens. Formatos aceitos: JPG, PNG, WebP. Máximo 2MB cada.
                                </p>
                                <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                            </div>
                        </div>

                        <!-- Coluna 2 - Preços e Estoque -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Preços e Estoque</h3>
                            </div>

                            <!-- Preços -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="price" :value="__('Preço (R$)')" />
                                    <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" 
                                        class="mt-1 block w-full" :value="old('price')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('price')" />
                                </div>

                                <div>
                                    <x-input-label for="compare_at_price" :value="__('Preço Comparativo (R$)')" />
                                    <x-text-input id="compare_at_price" name="compare_at_price" type="number" step="0.01" min="0.01" 
                                        class="mt-1 block w-full" :value="old('compare_at_price')" />
                                    <p class="text-xs text-gray-500 mt-1">Preço "De" para mostrar desconto</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('compare_at_price')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="cost" :value="__('Custo (R$) - Opcional')" />
                                <x-text-input id="cost" name="cost" type="number" step="0.01" min="0" 
                                    class="mt-1 block w-full" :value="old('cost')" />
                                <p class="text-xs text-gray-500 mt-1">Seu custo - não será mostrado ao cliente</p>
                                <x-input-error class="mt-2" :messages="$errors->get('cost')" />
                            </div>

                            <!-- Códigos -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="sku" :value="__('SKU - Opcional')" />
                                    <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku')" />
                                    <p class="text-xs text-gray-500 mt-1">Será gerado automaticamente se vazio</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                                </div>

                                <div>
                                    <x-input-label for="barcode" :value="__('Código de Barras')" />
                                    <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                                </div>
                            </div>

                            <!-- Estoque -->
                            <div>
                                <x-input-label for="stock_quantity" :value="__('Quantidade em Estoque')" />
                                <x-text-input id="stock_quantity" name="stock_quantity" type="number" min="0" 
                                    class="mt-1 block w-full" :value="old('stock_quantity', 0)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_quantity')" />
                            </div>

                            <!-- Dimensões e Peso -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Dimensões para Frete</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="weight" :value="__('Peso (kg)')" />
                                        <x-text-input id="weight" name="weight" type="number" step="0.001" min="0" 
                                            class="mt-1 block w-full" :value="old('weight')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('weight')" />
                                    </div>

                                    <div>
                                        <x-input-label for="length" :value="__('Comprimento (cm)')" />
                                        <x-text-input id="length" name="length" type="number" step="0.01" min="0" 
                                            class="mt-1 block w-full" :value="old('length')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('length')" />
                                    </div>

                                    <div>
                                        <x-input-label for="width" :value="__('Largura (cm)')" />
                                        <x-text-input id="width" name="width" type="number" step="0.01" min="0" 
                                            class="mt-1 block w-full" :value="old('width')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('width')" />
                                    </div>

                                    <div>
                                        <x-input-label for="height" :value="__('Altura (cm)')" />
                                        <x-text-input id="height" name="height" type="number" step="0.01" min="0" 
                                            class="mt-1 block w-full" :value="old('height')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('height')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Informações Adicionais -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Informações Adicionais</h4>
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="brand" :value="__('Marca')" />
                                        <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('brand')" />
                                    </div>

                                    <div>
                                        <x-input-label for="model" :value="__('Modelo')" />
                                        <x-text-input id="model" name="model" type="text" class="mt-1 block w-full" :value="old('model')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('model')" />
                                    </div>

                                    <div>
                                        <x-input-label for="warranty_months" :value="__('Garantia (meses)')" />
                                        <x-text-input id="warranty_months" name="warranty_months" type="number" min="0" max="120" 
                                            class="mt-1 block w-full" :value="old('warranty_months')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('warranty_months')" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO (Opcional) -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO - Opcional</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="meta_title" :value="__('Título SEO')" />
                                <x-text-input id="meta_title" name="meta_title" type="text" class="mt-1 block w-full" :value="old('meta_title')" />
                                <p class="text-xs text-gray-500 mt-1">Se vazio, usará o nome do produto</p>
                                <x-input-error class="mt-2" :messages="$errors->get('meta_title')" />
                            </div>

                            <div>
                                <x-input-label for="meta_keywords" :value="__('Palavras-chave')" />
                                <x-text-input id="meta_keywords" name="meta_keywords" type="text" class="mt-1 block w-full" :value="old('meta_keywords')" />
                                <p class="text-xs text-gray-500 mt-1">Separadas por vírgula</p>
                                <x-input-error class="mt-2" :messages="$errors->get('meta_keywords')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="meta_description" :value="__('Descrição SEO')" />
                                <textarea id="meta_description" name="meta_description" rows="3" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    placeholder="Descrição que aparece no Google...">{{ old('meta_description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('meta_description')" />
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                        <a href="{{ route('seller.products.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancelar
                        </a>

                        <div class="space-x-2">
                            <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar como Rascunho
                            </button>
                        </div>
                    </div>
                </form>
            </div>
    </div>

    <script>
        // Preview das imagens selecionadas
        document.getElementById('images').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 5) {
                alert('Máximo de 5 imagens permitidas');
                e.target.value = '';
                return;
            }
            
            files.forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`Arquivo ${file.name} é muito grande. Máximo 2MB por imagem.`);
                    e.target.value = '';
                    return;
                }
            });
        });
    </script>
@endsection