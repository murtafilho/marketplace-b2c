<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Criar Conta') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('Escolha o tipo de conta que deseja criar') }}</p>
    </div>

    <div x-data="{ 
        activeTab: '{{ old('role', request('role', 'customer')) }}',
        role: '{{ old('role', request('role', 'customer')) }}'
    }" class="mb-6">
        <!-- Role Selection Tabs -->
        <div class="flex border-b border-gray-200">
            <button 
                @click="activeTab = 'customer'; role = 'customer'"
                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'customer', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'customer' }"
                class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none"
                type="button">
                {{ __('Comprador') }}
            </button>
            <button 
                @click="activeTab = 'seller'; role = 'seller'"
                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'seller', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'seller' }"
                class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none"
                type="button">
                {{ __('Vendedor') }}
            </button>
        </div>

        <div class="mt-4">
            <div x-show="activeTab === 'customer'" class="text-sm text-gray-600">
                {{ __('Conta para realizar compras no marketplace') }}
            </div>
            <div x-show="activeTab === 'seller'" class="text-sm text-gray-600">
                {{ __('Conta para vender produtos no marketplace') }}
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Hidden Role Input -->
            <input type="hidden" name="role" x-model="role">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome Completo')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Telefone/WhatsApp')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="(11) 99999-9999" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role validation error -->
        <x-input-error :messages="$errors->get('role')" class="mt-2" />

        <!-- Seller Information Notice -->
        <div x-show="role === 'seller'" x-transition class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex">
                <svg class="flex-shrink-0 h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ __('Após o cadastro como vendedor, você precisará completar seu perfil com documentos e aguardar aprovação do administrador para começar a vender.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Já tem uma conta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Criar Conta') }}
            </x-primary-button>
        </div>
        </form>
    </div>

    <script>
        // Sync tabs with radio buttons
        document.addEventListener('alpine:init', () => {
            Alpine.data('registerForm', () => ({
                init() {
                    this.$watch('activeTab', (value) => {
                        this.role = value;
                    });
                }
            }));
        });
    </script>
</x-guest-layout>
