{{-- Arquivo: resources/views/components/layout/footer.blade.php --}}
{{-- Descrição: Footer do layout --}}

<footer class="bg-primary-900 dark:bg-gray-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Logo e Descrição --}}
            <div class="md:col-span-2">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-secondary-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.25c-5.376 0-9.75 4.374-9.75 9.75s4.374 9.75 9.75 9.75 9.75-4.374 9.75-9.75S17.376 2.25 12 2.25zM12 18.75c-3.722 0-6.75-3.028-6.75-6.75S8.278 5.25 12 5.25s6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"/>
                            <path d="M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold">{{ config('app.name') }}</h3>
                </div>
                <p class="text-gray-300 mb-4 max-w-md">
                    O marketplace que conecta a comunidade local com produtos autênticos e vendedores verificados.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.112.333.085.515-.09.394-.293 1.187-.334 1.345-.053.225-.172.271-.402.163-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.750-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.017z.001"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Links Rápidos --}}
            <div>
                <h4 class="font-semibold mb-4">Links Rápidos</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors">Início</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-300 hover:text-white transition-colors">Produtos</a></li>
                    <li><a href="{{ route('seller.register') }}" class="text-gray-300 hover:text-white transition-colors">Seja um Vendedor</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Sobre Nós</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contato</a></li>
                </ul>
            </div>

            {{-- Suporte --}}
            <div>
                <h4 class="font-semibold mb-4">Suporte</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Central de Ajuda</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Política de Privacidade</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Termos de Uso</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Política de Devolução</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Denunciar Produto</a></li>
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <p class="text-sm text-gray-300">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                </p>
                <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                    <span class="text-sm text-gray-300">Desenvolvido com</span>
                    <svg class="w-4 h-4 text-secondary-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-gray-300">para a comunidade</span>
                </div>
            </div>
        </div>
    </div>
</footer>