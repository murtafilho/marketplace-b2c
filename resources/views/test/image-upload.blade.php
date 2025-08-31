<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teste de Upload Interativo de Imagens</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Tailwind-like utility classes */
        .bg-gray-100 { background-color: #f3f4f6; }
        .min-h-screen { min-height: 100vh; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        .max-w-4xl { max-width: 56rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .bg-white { background-color: #ffffff; }
        .rounded-lg { border-radius: 0.5rem; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .p-6 { padding: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-bold { font-weight: 700; }
        .text-gray-800 { color: #1f2937; }
        .mb-6 { margin-bottom: 1.5rem; }
        .block { display: block; }
        .text-sm { font-size: 0.875rem; }
        .font-medium { font-weight: 500; }
        .text-gray-700 { color: #374151; }
        .mb-2 { margin-bottom: 0.5rem; }
        .w-full { width: 100%; }
        .p-3 { padding: 0.75rem; }
        .border { border-width: 1px; }
        .border-gray-300 { border-color: #d1d5db; }
        .focus\:ring-2:focus { outline: 2px solid transparent; outline-offset: 2px; box-shadow: 0 0 0 2px #3b82f6; }
        .focus\:ring-blue-500:focus { --tw-ring-color: #3b82f6; }
        .focus\:border-transparent:focus { border-color: transparent; }
    </style>
    <style>
        [x-cloak] { display: none !important; }
        .drag-over {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Teste de Upload Interativo de Imagens</h1>
            
            <!-- Aplicação Principal -->
            <div x-data="mainApp()" x-init="init()">
                <!-- Seletor de Produto -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selecionar Produto:</label>
                    <select x-model="selectedProductId" @change="loadProduct()" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecione um produto...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    
                    <div x-show="selectedProduct" x-cloak class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800" x-text="selectedProduct?.name"></h3>
                        <p class="text-sm text-gray-600" x-text="selectedProduct?.description"></p>
                        <p class="text-lg font-bold text-green-600 mt-2" x-text="selectedProduct?.formatted_price"></p>
                    </div>
                </div>
                
                <!-- Upload de Imagens -->
                <div x-show="selectedProductId" x-cloak class="space-y-6">
                
                <!-- Área de Drop -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors duration-200"
                     :class="{ 'drag-over': isDragOver }"
                     @dragover.prevent="isDragOver = true"
                     @dragleave.prevent="isDragOver = false"
                     @drop.prevent="handleDrop($event)">
                    
                    <div class="space-y-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        
                        <div>
                            <p class="text-lg font-medium text-gray-700">Arraste imagens aqui ou</p>
                            <label class="cursor-pointer">
                                <span class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Selecionar Arquivos
                                </span>
                                <input type="file" class="sr-only" multiple accept="image/*" @change="handleFileSelect($event)">
                            </label>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Suporte: JPEG, PNG, WebP • Máximo: 5MB por arquivo • Até 10 arquivos
                        </p>
                    </div>
                </div>
                
                <!-- Preview de Arquivos Selecionados -->
                <div x-show="selectedFiles.length > 0" x-cloak class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Arquivos Selecionados:</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <div class="relative bg-white border border-gray-200 rounded-lg p-3">
                                <img :src="file.preview" :alt="file.name" class="w-full h-32 object-cover rounded-md mb-2">
                                <p class="text-xs text-gray-600 truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                
                                <button @click="removeFile(index)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                                
                                <!-- Status do Upload -->
                                <div x-show="file.uploading" class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mx-auto mb-1"></div>
                                        <p class="text-xs">Enviando...</p>
                                    </div>
                                </div>
                                
                                <div x-show="file.uploaded" class="absolute inset-0 bg-green-500 bg-opacity-75 rounded-lg flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <svg class="h-6 w-6 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-xs">Enviado!</p>
                                    </div>
                                </div>
                                
                                <div x-show="file.error" class="absolute inset-0 bg-red-500 bg-opacity-75 rounded-lg flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <svg class="h-6 w-6 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-xs">Erro!</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button @click="uploadFiles()" :disabled="uploading || selectedFiles.length === 0" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!uploading">Enviar Imagens</span>
                            <span x-show="uploading" class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Enviando...
                            </span>
                        </button>
                        
                        <button @click="clearFiles()" :disabled="uploading" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Limpar Tudo
                        </button>
                    </div>
                </div>
                
                <!-- Galeria de Imagens Existentes -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Galeria do Produto:</h3>
                        <button @click="loadGallery()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Atualizar Galeria
                        </button>
                    </div>
                    
                    <div x-show="gallery.length === 0" class="text-center py-8 text-gray-500">
                        <p>Nenhuma imagem encontrada para este produto.</p>
                    </div>
                    
                    <div x-show="gallery.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="image in gallery" :key="image.id">
                            <div class="relative bg-white border border-gray-200 rounded-lg p-3 group">
                                <img :src="image.medium_url" :alt="image.name" class="w-full h-32 object-cover rounded-md mb-2">
                                <p class="text-xs text-gray-600 truncate" x-text="image.name"></p>
                                <p class="text-xs text-gray-500" x-text="image.size"></p>
                                <p class="text-xs text-gray-400" x-text="image.created_at"></p>
                                
                                <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center space-x-2">
                                    <button @click="setPrimaryImage(image.id)" class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                        Principal
                                    </button>
                                    <button @click="deleteImage(image.id)" class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Estatísticas de Storage -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Estatísticas de Storage:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-blue-600" x-text="storageStats.total_images || '0'"></p>
                            <p class="text-sm text-gray-600">Total de Imagens</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600" x-text="storageStats.free_space || 'N/A'"></p>
                            <p class="text-sm text-gray-600">Espaço Livre</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-orange-600" x-text="storageStats.used_space || 'N/A'"></p>
                            <p class="text-sm text-gray-600">Espaço Usado</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-purple-600" x-text="(storageStats.usage_percentage || 0) + '%'"></p>
                            <p class="text-sm text-gray-600">% de Uso</p>
                        </div>
                    </div>
                    
                    <button @click="loadStorageStats()" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        Atualizar Estatísticas
                    </button>
                </div>
            </div>
            </div>
            
            <!-- Mensagens -->
            <div x-data="{ messages: [] }" x-init="window.messageSystem = $data" class="fixed top-4 right-4 space-y-2 z-50">
                <template x-for="message in messages" :key="message.id">
                    <div class="max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300"
                         :class="{
                             'bg-green-500 text-white': message.type === 'success',
                             'bg-red-500 text-white': message.type === 'error',
                             'bg-blue-500 text-white': message.type === 'info',
                             'bg-yellow-500 text-black': message.type === 'warning'
                         }"
                         x-show="message.show"
                         x-transition:enter="transform ease-out duration-300 transition"
                         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium" x-text="message.text"></p>
                            <button @click="message.show = false" class="ml-2 text-lg leading-none">&times;</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    <script>
        // Sistema de mensagens global
        function showMessage(text, type = 'info', duration = 5000) {
            if (window.messageSystem) {
                const message = {
                    id: Date.now(),
                    text: text,
                    type: type,
                    show: true
                };
                
                window.messageSystem.messages.push(message);
                
                setTimeout(() => {
                    message.show = false;
                    setTimeout(() => {
                        const index = window.messageSystem.messages.indexOf(message);
                        if (index > -1) {
                            window.messageSystem.messages.splice(index, 1);
                        }
                    }, 300);
                }, duration);
            }
        }
        
        // Aplicação principal que combina seletor de produto e upload
        function mainApp() {
            return {
                // Dados do seletor de produto
                selectedProductId: '',
                selectedProduct: null,
                
                // Dados do upload de imagens
                selectedFiles: [],
                gallery: [],
                storageStats: {},
                uploading: false,
                isDragOver: false,
                
                init() {
                    this.loadStorageStats();
                },
                
                async loadProduct() {
                    if (!this.selectedProductId) {
                        this.selectedProduct = null;
                        this.gallery = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/test/api/products/${this.selectedProductId}`);
                        if (response.ok) {
                            this.selectedProduct = await response.json();
                            showMessage('Produto carregado com sucesso!', 'success');
                            this.loadGallery();
                        } else {
                            showMessage('Produto não encontrado', 'error');
                        }
                    } catch (error) {
                        console.error('Erro ao carregar produto:', error);
                        showMessage('Erro ao carregar produto', 'error');
                    }
                },
                
                handleFileSelect(event) {
                    this.addFiles(Array.from(event.target.files));
                    event.target.value = ''; // Reset input
                },
                
                handleDrop(event) {
                    this.isDragOver = false;
                    this.addFiles(Array.from(event.dataTransfer.files));
                },
                
                addFiles(files) {
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    
                    if (imageFiles.length !== files.length) {
                        showMessage('Apenas arquivos de imagem são aceitos', 'warning');
                    }
                    
                    if (this.selectedFiles.length + imageFiles.length > 10) {
                        showMessage('Máximo de 10 arquivos permitidos', 'warning');
                        return;
                    }
                    
                    imageFiles.forEach(file => {
                        if (file.size > 5 * 1024 * 1024) {
                            showMessage(`Arquivo ${file.name} excede 5MB`, 'error');
                            return;
                        }
                        
                        const fileObj = {
                            file: file,
                            name: file.name,
                            size: file.size,
                            preview: URL.createObjectURL(file),
                            uploading: false,
                            uploaded: false,
                            error: false
                        };
                        
                        this.selectedFiles.push(fileObj);
                    });
                },
                
                removeFile(index) {
                    const file = this.selectedFiles[index];
                    URL.revokeObjectURL(file.preview);
                    this.selectedFiles.splice(index, 1);
                },
                
                clearFiles() {
                    this.selectedFiles.forEach(file => {
                        URL.revokeObjectURL(file.preview);
                    });
                    this.selectedFiles = [];
                },
                
                async uploadFiles() {
                    if (!this.selectedProductId) {
                        showMessage('Selecione um produto primeiro', 'error');
                        return;
                    }
                    
                    this.uploading = true;
                    
                    for (let fileObj of this.selectedFiles) {
                        if (fileObj.uploaded || fileObj.error) continue;
                        
                        fileObj.uploading = true;
                        
                        try {
                            const formData = new FormData();
                            formData.append('image', fileObj.file);
                            
                            const response = await fetch(`/api/products/${this.selectedProductId}/images`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            });
                            
                            const result = await response.json();
                            
                            if (response.ok && result.success) {
                                fileObj.uploaded = true;
                                fileObj.uploading = false;
                                showMessage(`${fileObj.name} enviado com sucesso`, 'success');
                            } else {
                                throw new Error(result.message || 'Erro no upload');
                            }
                        } catch (error) {
                            fileObj.error = true;
                            fileObj.uploading = false;
                            showMessage(`Erro ao enviar ${fileObj.name}: ${error.message}`, 'error');
                        }
                    }
                    
                    this.uploading = false;
                    this.loadGallery();
                    this.loadStorageStats();
                },
                
                async loadGallery() {
                    if (!this.selectedProductId) {
                        this.gallery = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/api/products/${this.selectedProductId}/images`);
                        if (response.ok) {
                            const result = await response.json();
                            this.gallery = result.data || [];
                        }
                    } catch (error) {
                        showMessage('Erro ao carregar galeria', 'error');
                    }
                },
                
                async deleteImage(imageId) {
                    if (!this.selectedProductId) {
                        showMessage('Selecione um produto primeiro', 'error');
                        return;
                    }
                    
                    if (!confirm('Tem certeza que deseja excluir esta imagem?')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/api/products/${this.selectedProductId}/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (response.ok && result.success) {
                            showMessage('Imagem excluída com sucesso', 'success');
                            this.loadGallery();
                            this.loadStorageStats();
                        } else {
                            throw new Error(result.message || 'Erro ao excluir');
                        }
                    } catch (error) {
                        showMessage(`Erro ao excluir imagem: ${error.message}`, 'error');
                    }
                },
                
                async setPrimaryImage(imageId) {
                    if (!this.selectedProductId) {
                        showMessage('Selecione um produto primeiro', 'error');
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/api/products/${this.selectedProductId}/images/${imageId}/primary`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (response.ok && result.success) {
                            showMessage('Imagem principal definida', 'success');
                            this.loadGallery();
                        } else {
                            throw new Error(result.message || 'Erro ao definir imagem principal');
                        }
                    } catch (error) {
                        showMessage(`Erro: ${error.message}`, 'error');
                    }
                },
                
                async loadStorageStats() {
                    try {
                        const response = await fetch('/api/storage/stats');
                        if (response.ok) {
                            const result = await response.json();
                            this.storageStats = result.data || {};
                        }
                    } catch (error) {
                        console.error('Erro ao carregar estatísticas:', error);
                    }
                },
                
                formatFileSize(bytes) {
                    const units = ['B', 'KB', 'MB', 'GB'];
                    let size = bytes;
                    let unitIndex = 0;
                    
                    while (size >= 1024 && unitIndex < units.length - 1) {
                        size /= 1024;
                        unitIndex++;
                    }
                    
                    return `${size.toFixed(2)} ${units[unitIndex]}`;
                }
            }
        }
    </script>
</body>
</html>