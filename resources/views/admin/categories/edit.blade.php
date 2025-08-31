@extends('layouts.admin')

@section('title', 'Editar Categoria')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.categories.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Editar Categoria</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome da Categoria *
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $category->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categoria Pai -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoria Pai
                        </label>
                        <select name="parent_id" 
                                id="parent_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde">
                            <option value="">Nenhuma (categoria principal)</option>
                            @foreach($categories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                    {{ $parentCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ícone -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Ícone (emoji)
                        </label>
                        <input type="text" 
                               name="icon" 
                               id="icon" 
                               value="{{ old('icon', $category->icon) }}"
                               placeholder="🏠 🚗 📱 🎮 🎨"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde">
                        <p class="mt-1 text-sm text-gray-500">
                            Use emojis ou caracteres especiais para representar a categoria
                        </p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Imagem Atual -->
                    @if($category->image_path)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Imagem Atual
                            </label>
                            <div class="flex items-center space-x-4">
                                <img src="{{ Storage::url($category->image_path) }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-20 h-20 object-cover rounded-lg border">
                                <div>
                                    <p class="text-sm text-gray-600">{{ basename($category->image_path) }}</p>
                                    <p class="text-xs text-gray-500">Para alterar, selecione uma nova imagem abaixo</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Nova Imagem -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $category->image_path ? 'Nova Imagem' : 'Imagem da Categoria' }}
                        </label>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde">
                        <p class="mt-1 text-sm text-gray-500">
                            Formatos aceitos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB
                        </p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ordem -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Ordem de Exibição
                        </label>
                        <input type="number" 
                               name="sort_order" 
                               id="sort_order" 
                               value="{{ old('sort_order', $category->sort_order) }}"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-vale-verde focus:border-vale-verde">
                        <p class="mt-1 text-sm text-gray-500">
                            Categorias com menor número aparecem primeiro
                        </p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-vale-verde focus:ring-vale-verde border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Categoria ativa</span>
                        </label>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-vale-verde">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-vale-verde hover:bg-vale-verde-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-vale-verde">
                        Atualizar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

