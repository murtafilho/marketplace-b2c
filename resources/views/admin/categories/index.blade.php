@extends('layouts.admin')

@section('title', 'Gerenciar Categorias')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Header - Mobile-first -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Gerenciar Categorias</h1>
            <p class="mt-1 text-sm text-gray-300">Organize e configure as categorias da plataforma</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 min-h-[44px]">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Categoria
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-success-100 border border-success-200 text-success-800 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-danger-100 border border-danger-200 text-danger-800 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Categories Table/Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="block sm:hidden divide-y divide-gray-200">
            @forelse($categories as $category)
                <div class="p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start space-x-3 min-w-0 flex-1">
                            @if($category->icon)
                                <span class="text-2xl flex-shrink-0">{{ $category->icon }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $category->name }}</h3>
                                @if($category->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($category->description, 50) }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">{{ $category->slug }}</p>
                            </div>
                        </div>
                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ $category->is_active ? 'bg-success-100 text-success-800' : 'bg-gray-100 text-gray-800' }} flex-shrink-0">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <div class="flex items-center space-x-4">
                            @if($category->parent)
                                <span class="px-2 py-1 bg-info-100 text-info-800 rounded-full">
                                    Pai: {{ $category->parent->name }}
                                </span>
                            @else
                                <span>Categoria principal</span>
                            @endif
                            <span>Ordem: {{ $category->sort_order }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" 
                           class="flex-1 bg-emerald-600 text-white text-center py-2 px-3 rounded-lg text-xs font-medium hover:bg-emerald-700 transition-colors min-h-[44px] flex items-center justify-center">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        
                        <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-info-600 text-white py-2 px-3 rounded-lg text-xs font-medium hover:bg-info-700 transition-colors min-h-[44px] flex items-center justify-center">
                                <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} mr-1"></i>
                                {{ $category->is_active ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-tags text-gray-400 text-lg"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Nenhuma categoria encontrada</p>
                    <p class="text-gray-400 text-xs mt-1">Crie sua primeira categoria para começar</p>
                </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slug
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoria Pai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ordem
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($category->icon)
                                        <span class="text-2xl mr-3">{{ $category->icon }}</span>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $category->name }}
                                        </div>
                                        @if($category->description)
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($category->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($category->parent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-success-100 text-success-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-emerald-600 hover:text-emerald-800 transition-colors">
                                        Editar
                                    </a>
                                    
                                    <form action="{{ route('admin.categories.toggle-status', $category) }}" 
                                          method="POST" 
                                          class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-info-600 hover:text-info-800 transition-colors">
                                            {{ $category->is_active ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-danger-600 hover:text-danger-800 transition-colors">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-tags text-gray-400 text-lg"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Nenhuma categoria encontrada</p>
                                <p class="text-gray-400 text-xs mt-1">Crie sua primeira categoria para começar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

