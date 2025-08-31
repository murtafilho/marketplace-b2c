{{-- Upload de Imagens com Drag & Drop --}}
<div class="bg-white rounded-lg shadow-sm p-6" x-data="imageUploader()">
    <h3 class="text-lg font-semibold mb-4">Imagens do Produto</h3>
    
    {{-- Área de Drag & Drop --}}
    <div class="mb-6">
        <div 
            @drop.prevent="handleDrop($event)"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            :class="{'border-blue-500 bg-blue-50': dragOver}"
            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            
            <p class="mt-4 text-lg text-gray-600">
                Arraste imagens aqui ou 
                <label for="image-upload" class="text-blue-600 hover:text-blue-800 cursor-pointer font-semibold">
                    clique para selecionar
                </label>
            </p>
            
            <input 
                type="file" 
                id="image-upload" 
                class="hidden" 
                multiple 
                accept="image/jpeg,image/jpg,image/png,image/webp"
                @change="handleFileSelect($event)"
            >
            
            <p class="mt-2 text-sm text-gray-500">
                JPEG, PNG ou WebP • Máximo 5MB • Mínimo 400x400px • Até 10 imagens
            </p>
        </div>
    </div>
    
    {{-- Preview das Imagens --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" x-show="images.length > 0">
        <template x-for="(image, index) in images" :key="index">
            <div class="relative group">
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img 
                        :src="image.preview" 
                        :alt="image.name"
                        class="w-full h-full object-cover"
                    >
                </div>
                
                {{-- Overlay de ações --}}
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2">
                    {{-- Botão para definir como principal --}}
                    <button 
                        @click="setPrimary(index)"
                        :class="{'bg-yellow-500': image.isPrimary, 'bg-white': !image.isPrimary}"
                        class="p-2 rounded-full hover:bg-yellow-500 transition"
                        title="Definir como principal"
                    >
                        <svg class="w-5 h-5" :class="{'text-white': image.isPrimary, 'text-gray-700': !image.isPrimary}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                    
                    {{-- Botão para remover --}}
                    <button 
                        @click="removeImage(index)"
                        class="p-2 bg-white rounded-full hover:bg-red-500 hover:text-white transition"
                        title="Remover imagem"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Badge de imagem principal --}}
                <div x-show="image.isPrimary" class="absolute top-2 left-2">
                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">
                        Principal
                    </span>
                </div>
                
                {{-- Status de upload --}}
                <div x-show="image.uploading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 mt-2" x-text="image.progress + '%'"></span>
                    </div>
                </div>
                
                {{-- Erro no upload --}}
                <div x-show="image.error" class="absolute inset-0 bg-red-100 bg-opacity-90 flex items-center justify-center rounded-lg">
                    <div class="text-center p-2">
                        <svg class="w-8 h-8 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs text-red-600 mt-1" x-text="image.errorMessage"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    {{-- Botão de upload --}}
    <div class="mt-6" x-show="images.length > 0">
        <button 
            @click="uploadImages()"
            :disabled="uploading"
            class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!uploading">Enviar Imagens</span>
            <span x-show="uploading" class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enviando...
            </span>
        </button>
    </div>
</div>

<script>
function imageUploader() {
    return {
        images: [],
        dragOver: false,
        uploading: false,
        productId: {{ $product->id ?? 'null' }},
        
        handleDrop(event) {
            this.dragOver = false;
            this.handleFiles(event.dataTransfer.files);
        },
        
        handleFileSelect(event) {
            this.handleFiles(event.target.files);
        },
        
        handleFiles(files) {
            // Validar número máximo de imagens
            if (this.images.length + files.length > 10) {
                alert('Máximo de 10 imagens permitidas');
                return;
            }
            
            Array.from(files).forEach(file => {
                // Validar tipo
                if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert(`${file.name} não é um formato válido`);
                    return;
                }
                
                // Validar tamanho (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`${file.name} excede o tamanho máximo de 5MB`);
                    return;
                }
                
                // Criar preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Validar dimensões mínimas
                    const img = new Image();
                    img.onload = () => {
                        if (img.width < 400 || img.height < 400) {
                            alert(`${file.name} deve ter no mínimo 400x400px`);
                            return;
                        }
                        
                        this.images.push({
                            file: file,
                            name: file.name,
                            preview: e.target.result,
                            isPrimary: this.images.length === 0,
                            uploading: false,
                            progress: 0,
                            error: false,
                            errorMessage: ''
                        });
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        },
        
        setPrimary(index) {
            this.images.forEach((img, i) => {
                img.isPrimary = i === index;
            });
        },
        
        removeImage(index) {
            this.images.splice(index, 1);
            // Redefinir imagem principal se necessário
            if (this.images.length > 0 && !this.images.some(img => img.isPrimary)) {
                this.images[0].isPrimary = true;
            }
        },
        
        async uploadImages() {
            if (!this.productId) {
                alert('Salve o produto antes de adicionar imagens');
                return;
            }
            
            this.uploading = true;
            
            const formData = new FormData();
            this.images.forEach((image, index) => {
                formData.append(`images[${index}]`, image.file);
            });
            
            try {
                const response = await fetch(`/seller/products/${this.productId}/images`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Limpar imagens após upload bem-sucedido
                    this.images = [];
                    
                    // Mostrar mensagem de sucesso
                    alert('Imagens enviadas com sucesso!');
                    
                    // Recarregar página para mostrar imagens
                    window.location.reload();
                } else {
                    alert(data.message || 'Erro ao enviar imagens');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao enviar imagens. Tente novamente.');
            } finally {
                this.uploading = false;
            }
        }
    }
}
</script>