{{-- Arquivo: resources/views/components/layout/modals.blade.php --}}
{{-- Descrição: Sistema de modais globais --}}

{{-- Modal Base --}}
<div x-data="{ modalOpen: false }" 
     @modal-open.window="modalOpen = true"
     @modal-close.window="modalOpen = false"
     @keydown.escape.window="modalOpen = false">
     
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="modalOpen = false">
         
        <div x-show="modalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
             
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" x-text="$store.modal?.title || 'Modal'"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Modal Content --}}
            <div class="p-6" x-html="$store.modal?.content || ''"></div>
            
            {{-- Modal Footer --}}
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200">
                <button @click="modalOpen = false" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button @click="$store.modal?.onConfirm?.(); modalOpen = false" 
                        x-show="$store.modal?.showConfirm"
                        class="px-4 py-2 text-sm font-medium text-white bg-vale-verde rounded-md hover:bg-vale-verde-dark">
                    <span x-text="$store.modal?.confirmText || 'Confirmar'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Store --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('modal', {
        title: '',
        content: '',
        showConfirm: false,
        confirmText: 'Confirmar',
        onConfirm: null,
        
        show(options = {}) {
            this.title = options.title || 'Modal';
            this.content = options.content || '';
            this.showConfirm = options.showConfirm ?? false;
            this.confirmText = options.confirmText || 'Confirmar';
            this.onConfirm = options.onConfirm || null;
            
            window.dispatchEvent(new CustomEvent('modal-open'));
        },
        
        hide() {
            window.dispatchEvent(new CustomEvent('modal-close'));
        },
        
        confirm(title, message, callback) {
            this.show({
                title: title,
                content: `<p class="text-gray-600">${message}</p>`,
                showConfirm: true,
                onConfirm: callback
            });
        },
        
        alert(title, message) {
            this.show({
                title: title,
                content: `<p class="text-gray-600">${message}</p>`,
                showConfirm: false
            });
        }
    });
});
</script>