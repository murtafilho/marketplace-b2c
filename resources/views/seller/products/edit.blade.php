@extends('layouts.seller')

@section('title', 'Editar Produto')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Produto</h1>
                <p class="mt-2 text-gray-600">Atualize as informações do seu produto</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('seller.products.show', $product) }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>Visualizar
                </a>
                <a href="{{ route('seller.products.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Corrija os erros abaixo:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Informações Básicas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informações Básicas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome do Produto *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $product->name) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Preço (R$) *
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price', $product->price) }}"
                           step="0.01" 
                           min="0.01"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Categoria *
                    </label>
                    <select id="category_id" 
                            name="category_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantidade em Estoque *
                    </label>
                    <input type="number" 
                           id="stock_quantity" 
                           name="stock_quantity" 
                           value="{{ old('stock_quantity', $product->stock_quantity) }}"
                           min="0"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select id="status" 
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                            Ativo
                        </option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                            Inativo
                        </option>
                        <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>
                            Rascunho
                        </option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Descreva detalhadamente o seu produto...">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Imagens Atuais -->
        @if($product->images->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Imagens Atuais</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $image->file_path) }}" 
                             alt="Imagem do produto" 
                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" 
                                    onclick="removeImage({{ $image->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white p-1 rounded-full text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @if($loop->first)
                            <div class="absolute bottom-2 left-2">
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">Principal</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Novas Imagens -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Adicionar Novas Imagens</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Selecionar Imagens (máximo 5 por vez)
                    </label>
                    <input type="file" 
                           id="images" 
                           name="images[]"
                           multiple
                           accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-2 text-sm text-gray-500">
                        Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 2MB por imagem.
                    </p>
                </div>

                <!-- Preview das novas imagens -->
                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden"></div>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex justify-between items-center pt-6">
            <div class="flex space-x-3">
                <a href="{{ route('seller.products.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        name="status" 
                        value="draft"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Salvar Rascunho
                </button>
                <button type="submit" 
                        name="status" 
                        value="active"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>Salvar e Publicar
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Preview de imagens
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const files = e.target.files;
    
    preview.innerHTML = '';
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        
        for (let i = 0; i < Math.min(files.length, 5); i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Preview" 
                         class="w-full h-32 object-cover rounded-lg border border-gray-200">
                    <div class="absolute bottom-2 left-2">
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Nova</span>
                    </div>
                `;
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }
    } else {
        preview.classList.add('hidden');
    }
});

// Remover imagem existente
function removeImage(imageId) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        fetch(`{{ url('/seller/products/images') }}/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao remover a imagem. Tente novamente.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover a imagem. Tente novamente.');
        });
    }
}
</script>
@endpush
@endsection