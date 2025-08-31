{{--
Componente: Advanced Image Editor
Descrição: Editor de imagens integrado com canvas e ferramentas avançadas
Uso: Para edição de imagens no painel administrativo
--}}
@props([
    'imageUrl' => '',
    'imagePath' => '',
    'showToolbar' => true,
    'allowSave' => true,
    'width' => '800',
    'height' => '600'
])

<div class="image-editor-container bg-white rounded-lg shadow-lg overflow-hidden" 
     x-data="imageEditor('{{ $imageUrl }}', '{{ base64_encode($imagePath) }}')" 
     x-init="init()">
     
    <!-- Editor Header -->
    <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h3 class="text-lg font-semibold text-gray-900">Editor de Imagem</h3>
                <div class="text-sm text-gray-500" x-show="imageLoaded">
                    <span x-text="originalDimensions.width + ' × ' + originalDimensions.height"></span>
                    <span class="ml-2" x-text="'(' + Math.round(originalDimensions.width * originalDimensions.height / 1000000 * 10) / 10 + 'MP)'"></span>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <!-- Zoom Controls -->
                <div class="flex items-center bg-white rounded-md border border-gray-300">
                    <button @click="zoomOut()" class="px-2 py-1 hover:bg-gray-100 rounded-l-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <span class="px-3 py-1 text-sm border-x border-gray-300" x-text="Math.round(zoom * 100) + '%'"></span>
                    <button @click="zoomIn()" class="px-2 py-1 hover:bg-gray-100 rounded-r-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Reset Button -->
                <button @click="resetImage()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    Reset
                </button>
                
                @if($allowSave)
                <!-- Save Button -->
                <button @click="saveImage()" 
                        :disabled="!hasChanges || saving"
                        :class="hasChanges && !saving ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="px-4 py-1 rounded-md text-sm transition-colors">
                    <span x-show="!saving">Salvar</span>
                    <span x-show="saving" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Salvando...
                    </span>
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="flex" style="height: {{ $height }}px;">
        @if($showToolbar)
        <!-- Toolbar -->
        <div class="w-64 bg-gray-50 border-r border-gray-200 overflow-y-auto">
            <!-- Tool Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button @click="activeTab = 'adjust'" 
                            :class="activeTab === 'adjust' ? 'bg-white border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                            class="flex-1 px-3 py-2 text-sm font-medium">
                        Ajustar
                    </button>
                    <button @click="activeTab = 'filters'" 
                            :class="activeTab === 'filters' ? 'bg-white border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                            class="flex-1 px-3 py-2 text-sm font-medium">
                        Filtros
                    </button>
                    <button @click="activeTab = 'transform'" 
                            :class="activeTab === 'transform' ? 'bg-white border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                            class="flex-1 px-3 py-2 text-sm font-medium">
                        Redimensionar
                    </button>
                </nav>
            </div>
            
            <!-- Adjust Panel -->
            <div x-show="activeTab === 'adjust'" class="p-4 space-y-4">
                <!-- Brightness -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Brilho <span x-text="filters.brightness"></span>
                    </label>
                    <input type="range" x-model="filters.brightness" @input="applyFilters()"
                           min="-100" max="100" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <!-- Contrast -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Contraste <span x-text="filters.contrast"></span>
                    </label>
                    <input type="range" x-model="filters.contrast" @input="applyFilters()"
                           min="-100" max="100" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <!-- Saturation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Saturação <span x-text="filters.saturation"></span>
                    </label>
                    <input type="range" x-model="filters.saturation" @input="applyFilters()"
                           min="-100" max="100" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <!-- Hue -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Matiz <span x-text="filters.hue + '°'"></span>
                    </label>
                    <input type="range" x-model="filters.hue" @input="applyFilters()"
                           min="0" max="360" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <!-- Blur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Desfoque <span x-text="filters.blur + 'px'"></span>
                    </label>
                    <input type="range" x-model="filters.blur" @input="applyFilters()"
                           min="0" max="10" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
            </div>
            
            <!-- Filters Panel -->
            <div x-show="activeTab === 'filters'" class="p-4">
                <div class="grid grid-cols-2 gap-2">
                    <button @click="applyPresetFilter('grayscale')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        P&B
                    </button>
                    <button @click="applyPresetFilter('sepia')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Sépia
                    </button>
                    <button @click="applyPresetFilter('vintage')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Vintage
                    </button>
                    <button @click="applyPresetFilter('cool')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Frio
                    </button>
                    <button @click="applyPresetFilter('warm')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Quente
                    </button>
                    <button @click="applyPresetFilter('dramatic')" 
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                        Dramático
                    </button>
                </div>
            </div>
            
            <!-- Transform Panel -->
            <div x-show="activeTab === 'transform'" class="p-4 space-y-4">
                <!-- Rotate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rotação</label>
                    <div class="flex space-x-2">
                        <button @click="rotate(-90)" class="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                            ↶ 90°
                        </button>
                        <button @click="rotate(90)" class="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                            ↷ 90°
                        </button>
                    </div>
                </div>
                
                <!-- Flip -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Espelhar</label>
                    <div class="flex space-x-2">
                        <button @click="flip('horizontal')" class="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                            ⟷ Horizontal
                        </button>
                        <button @click="flip('vertical')" class="flex-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                            ↕ Vertical
                        </button>
                    </div>
                </div>
                
                <!-- Resize -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redimensionar</label>
                    <div class="space-y-2">
                        <div class="flex space-x-2">
                            <input type="number" x-model="resizeWidth" placeholder="Largura" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <input type="number" x-model="resizeHeight" placeholder="Altura"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="maintainAspectRatio" class="mr-2">
                            <span class="text-sm text-gray-600">Manter proporção</span>
                        </label>
                        <button @click="resize()" 
                                class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">
                            Aplicar Tamanho
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- History Panel -->
            <div class="border-t border-gray-200 p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Histórico</h4>
                <div class="space-y-1 max-h-32 overflow-y-auto">
                    <template x-for="(action, index) in history" :key="index">
                        <div class="flex items-center justify-between text-xs text-gray-600 py-1">
                            <span x-text="action.name"></span>
                            <button @click="revertToStep(index)" class="text-indigo-600 hover:text-indigo-800">
                                Voltar
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Canvas Area -->
        <div class="flex-1 bg-gray-100 relative overflow-hidden">
            <!-- Loading State -->
            <div x-show="!imageLoaded" class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Carregando imagem...</p>
                </div>
            </div>
            
            <!-- Canvas Container -->
            <div x-show="imageLoaded" class="w-full h-full flex items-center justify-center p-4">
                <div class="relative" :style="`transform: scale(${zoom})`">
                    <canvas x-ref="canvas" 
                            class="max-w-full max-h-full border border-gray-300 rounded-lg shadow-lg bg-white"
                            :width="canvasWidth" 
                            :height="canvasHeight">
                    </canvas>
                    
                    <!-- Crop Overlay -->
                    <div x-show="cropMode" 
                         x-ref="cropOverlay"
                         class="absolute border-2 border-dashed border-indigo-500 bg-indigo-500 bg-opacity-20"
                         style="display: none;">
                        <!-- Crop Handles -->
                        <div class="absolute -top-1 -left-1 w-2 h-2 bg-indigo-500 cursor-nw-resize"></div>
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-indigo-500 cursor-ne-resize"></div>
                        <div class="absolute -bottom-1 -left-1 w-2 h-2 bg-indigo-500 cursor-sw-resize"></div>
                        <div class="absolute -bottom-1 -right-1 w-2 h-2 bg-indigo-500 cursor-se-resize"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Bar -->
    <div class="bg-gray-50 border-t border-gray-200 px-4 py-2">
        <div class="flex items-center justify-between text-sm text-gray-600">
            <div class="flex items-center space-x-4">
                <span x-show="imageLoaded">
                    Dimensões: <span x-text="canvasWidth + ' × ' + canvasHeight"></span>
                </span>
                <span x-show="hasChanges" class="text-orange-600">• Não salvo</span>
            </div>
            
            <div class="flex items-center space-x-2">
                <span x-text="'Zoom: ' + Math.round(zoom * 100) + '%'"></span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function imageEditor(imageUrl, encodedPath) {
    return {
        // State
        imageLoaded: false,
        hasChanges: false,
        saving: false,
        
        // Canvas
        canvas: null,
        ctx: null,
        originalImage: null,
        currentImageData: null,
        
        // Dimensions
        originalDimensions: { width: 0, height: 0 },
        canvasWidth: 800,
        canvasHeight: 600,
        
        // View
        zoom: 1,
        activeTab: 'adjust',
        
        // Filters
        filters: {
            brightness: 0,
            contrast: 0,
            saturation: 0,
            hue: 0,
            blur: 0
        },
        
        // Transform
        rotation: 0,
        flipH: false,
        flipV: false,
        resizeWidth: 0,
        resizeHeight: 0,
        maintainAspectRatio: true,
        
        // Crop
        cropMode: false,
        cropArea: { x: 0, y: 0, width: 0, height: 0 },
        
        // History
        history: [],
        
        init() {
            this.$nextTick(() => {
                this.canvas = this.$refs.canvas;
                this.ctx = this.canvas.getContext('2d');
                this.loadImage(imageUrl);
            });
        },
        
        async loadImage(url) {
            try {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                
                img.onload = () => {
                    this.originalImage = img;
                    this.originalDimensions = {
                        width: img.width,
                        height: img.height
                    };
                    
                    this.resizeWidth = img.width;
                    this.resizeHeight = img.height;
                    
                    this.updateCanvasSize();
                    this.drawImage();
                    this.saveCurrentState('Imagem carregada');
                    this.imageLoaded = true;
                };
                
                img.onerror = () => {
                    console.error('Erro ao carregar imagem');
                };
                
                img.src = url;
            } catch (error) {
                console.error('Erro:', error);
            }
        },
        
        updateCanvasSize() {
            const maxWidth = 800;
            const maxHeight = 600;
            
            let { width, height } = this.originalDimensions;
            
            // Redimensionar para caber na viewport
            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width *= ratio;
                height *= ratio;
            }
            
            this.canvasWidth = Math.round(width);
            this.canvasHeight = Math.round(height);
            
            this.canvas.width = this.canvasWidth;
            this.canvas.height = this.canvasHeight;
        },
        
        drawImage() {
            if (!this.originalImage) return;
            
            this.ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
            
            // Aplicar transformações
            this.ctx.save();
            
            // Centro do canvas
            const centerX = this.canvasWidth / 2;
            const centerY = this.canvasHeight / 2;
            
            this.ctx.translate(centerX, centerY);
            
            // Aplicar rotação
            if (this.rotation !== 0) {
                this.ctx.rotate((this.rotation * Math.PI) / 180);
            }
            
            // Aplicar espelhamento
            let scaleX = this.flipH ? -1 : 1;
            let scaleY = this.flipV ? -1 : 1;
            this.ctx.scale(scaleX, scaleY);
            
            // Desenhar imagem
            this.ctx.drawImage(
                this.originalImage,
                -this.canvasWidth / 2,
                -this.canvasHeight / 2,
                this.canvasWidth,
                this.canvasHeight
            );
            
            this.ctx.restore();
            
            // Aplicar filtros CSS
            this.applyCanvasFilters();
            
            this.currentImageData = this.ctx.getImageData(0, 0, this.canvasWidth, this.canvasHeight);
        },
        
        applyCanvasFilters() {
            const filterString = [
                `brightness(${100 + this.filters.brightness}%)`,
                `contrast(${100 + this.filters.contrast}%)`,
                `saturate(${100 + this.filters.saturation}%)`,
                `hue-rotate(${this.filters.hue}deg)`,
                `blur(${this.filters.blur}px)`
            ].join(' ');
            
            this.canvas.style.filter = filterString;
        },
        
        applyFilters() {
            this.hasChanges = true;
            this.drawImage();
        },
        
        applyPresetFilter(preset) {
            const presets = {
                grayscale: { brightness: 0, contrast: 10, saturation: -100, hue: 0, blur: 0 },
                sepia: { brightness: 10, contrast: 15, saturation: -30, hue: 30, blur: 0 },
                vintage: { brightness: 5, contrast: 20, saturation: -20, hue: 10, blur: 0 },
                cool: { brightness: 0, contrast: 10, saturation: 20, hue: 180, blur: 0 },
                warm: { brightness: 10, contrast: 15, saturation: 15, hue: 30, blur: 0 },
                dramatic: { brightness: -10, contrast: 40, saturation: 30, hue: 0, blur: 0 }
            };
            
            if (presets[preset]) {
                this.filters = { ...presets[preset] };
                this.applyFilters();
                this.saveCurrentState(`Filtro ${preset} aplicado`);
            }
        },
        
        rotate(angle) {
            this.rotation = (this.rotation + angle) % 360;
            this.hasChanges = true;
            this.drawImage();
            this.saveCurrentState(`Rotação ${angle}°`);
        },
        
        flip(direction) {
            if (direction === 'horizontal') {
                this.flipH = !this.flipH;
            } else {
                this.flipV = !this.flipV;
            }
            this.hasChanges = true;
            this.drawImage();
            this.saveCurrentState(`Espelhar ${direction}`);
        },
        
        resize() {
            if (!this.resizeWidth || !this.resizeHeight) return;
            
            // Implementar redimensionamento
            this.canvasWidth = parseInt(this.resizeWidth);
            this.canvasHeight = parseInt(this.resizeHeight);
            
            this.canvas.width = this.canvasWidth;
            this.canvas.height = this.canvasHeight;
            
            this.hasChanges = true;
            this.drawImage();
            this.saveCurrentState(`Redimensionar para ${this.resizeWidth}×${this.resizeHeight}`);
        },
        
        zoomIn() {
            this.zoom = Math.min(this.zoom * 1.2, 5);
        },
        
        zoomOut() {
            this.zoom = Math.max(this.zoom / 1.2, 0.1);
        },
        
        resetImage() {
            this.filters = {
                brightness: 0,
                contrast: 0,
                saturation: 0,
                hue: 0,
                blur: 0
            };
            
            this.rotation = 0;
            this.flipH = false;
            this.flipV = false;
            this.zoom = 1;
            
            this.updateCanvasSize();
            this.drawImage();
            this.hasChanges = false;
            this.history = [];
            this.saveCurrentState('Reset');
        },
        
        saveCurrentState(actionName) {
            this.history.push({
                name: actionName,
                timestamp: Date.now(),
                state: {
                    filters: { ...this.filters },
                    rotation: this.rotation,
                    flipH: this.flipH,
                    flipV: this.flipV,
                    canvasWidth: this.canvasWidth,
                    canvasHeight: this.canvasHeight
                }
            });
            
            // Limitar histórico a 20 ações
            if (this.history.length > 20) {
                this.history.shift();
            }
        },
        
        revertToStep(index) {
            if (index >= 0 && index < this.history.length) {
                const state = this.history[index].state;
                
                this.filters = { ...state.filters };
                this.rotation = state.rotation;
                this.flipH = state.flipH;
                this.flipV = state.flipV;
                this.canvasWidth = state.canvasWidth;
                this.canvasHeight = state.canvasHeight;
                
                this.canvas.width = this.canvasWidth;
                this.canvas.height = this.canvasHeight;
                
                this.drawImage();
                this.hasChanges = true;
                
                // Remover ações posteriores do histórico
                this.history = this.history.slice(0, index + 1);
            }
        },
        
        async saveImage() {
            if (!this.hasChanges) return;
            
            this.saving = true;
            
            try {
                // Converter canvas para blob
                const blob = await new Promise(resolve => {
                    this.canvas.toBlob(resolve, 'image/jpeg', 0.9);
                });
                
                // Criar FormData
                const formData = new FormData();
                const operations = [];
                
                // Adicionar operações baseadas no estado atual
                if (this.filters.brightness !== 0) {
                    operations.push({
                        type: 'brightness',
                        params: { value: this.filters.brightness }
                    });
                }
                
                if (this.filters.contrast !== 0) {
                    operations.push({
                        type: 'contrast',
                        params: { value: this.filters.contrast }
                    });
                }
                
                if (this.rotation !== 0) {
                    operations.push({
                        type: 'rotate',
                        params: { angle: this.rotation }
                    });
                }
                
                formData.append('operations', JSON.stringify(operations));
                
                // Fazer requisição
                const response = await fetch(`/admin/media/edit/${encodedPath}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.hasChanges = false;
                    this.showToast('Imagem salva com sucesso!', 'success');
                } else {
                    this.showToast('Erro ao salvar imagem: ' + result.message, 'error');
                }
                
            } catch (error) {
                console.error('Erro ao salvar:', error);
                this.showToast('Erro ao salvar imagem', 'error');
            } finally {
                this.saving = false;
            }
        },
        
        showToast(message, type = 'success') {
            if (window.showToast) {
                window.showToast(message, type);
            }
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.image-editor-container input[type="range"] {
    -webkit-appearance: none;
    background: transparent;
    cursor: pointer;
}

.image-editor-container input[type="range"]::-webkit-slider-track {
    background: #e5e7eb;
    height: 6px;
    border-radius: 3px;
}

.image-editor-container input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    background: #4f46e5;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    cursor: pointer;
}

.image-editor-container input[type="range"]::-moz-range-track {
    background: #e5e7eb;
    height: 6px;
    border-radius: 3px;
    border: none;
}

.image-editor-container input[type="range"]::-moz-range-thumb {
    background: #4f46e5;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    cursor: pointer;
    border: none;
}

.image-editor-container canvas {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}
</style>
@endpush