{{-- Notification Toast Container - Componente global de notificações --}}
<div x-data
     x-show="$store.notifications.items.length > 0"
     class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm w-full pointer-events-none"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0">
    
    {{-- Loop através das notificações --}}
    <template x-for="notification in $store.notifications.items" :key="notification.id">
        <div @click="$store.notifications.handleClick(notification)"
             x-show="true"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2 translate-x-full"
             x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
             x-transition:leave-end="opacity-0 translate-y-2 translate-x-full"
             class="pointer-events-auto relative overflow-hidden rounded-lg shadow-elevated border-l-4 backdrop-blur-sm"
             :class="notification.bgColor + ' ' + notification.borderColor">
            
            <div class="p-4">
                <div class="flex items-start">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6" :class="notification.textColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="$store.notifications.getIconPath(notification.icon)"></path>
                        </svg>
                    </div>
                    
                    {{-- Content --}}
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium" :class="notification.textColor" x-text="notification.message"></p>
                        
                        {{-- Action button --}}
                        <div x-show="notification.actionText" class="mt-2">
                            <button @click.stop="$store.notifications.handleClick(notification)"
                                    class="text-sm font-medium underline hover:no-underline transition-all duration-200"
                                    :class="notification.textColor"
                                    x-text="notification.actionText">
                            </button>
                        </div>
                    </div>
                    
                    {{-- Close button --}}
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click.stop="$store.notifications.remove(notification.id)"
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200"
                                :class="notification.textColor + ' hover:bg-black hover:bg-opacity-10 focus:ring-white focus:ring-opacity-50'">
                            <span class="sr-only">Fechar</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Progress bar for auto-dismiss --}}
            <div x-show="notification.duration > 0 && !notification.persistent"
                 class="absolute bottom-0 left-0 h-1 bg-black bg-opacity-20">
                <div class="h-full bg-white bg-opacity-50 transition-all ease-linear"
                     :style="`width: 100%; animation: shrink ${notification.duration}ms linear;`">
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Global styles for notifications --}}
<style>
    @keyframes shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>