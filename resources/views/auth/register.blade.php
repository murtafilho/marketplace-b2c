@extends('layouts.guest')

@section('title', 'Criar Conta')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Criar sua conta</h1>
        <p class="mt-2 text-sm text-gray-600">Escolha o tipo de conta que deseja criar</p>
    </div>

    <div x-data="{ 
        activeTab: '{{ old('role', request('role', 'customer')) }}',
        role: '{{ old('role', request('role', 'customer')) }}'
    }" class="space-y-4">
        <!-- Role Selection Tabs -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="flex">
                <button 
                    @click="activeTab = 'customer'; role = 'customer'"
                    :class="{ 
                        'bg-emerald-50 text-emerald-700 border-emerald-200': activeTab === 'customer', 
                        'bg-white text-gray-600 hover:bg-gray-50': activeTab !== 'customer' 
                    }"
                    class="flex-1 py-3 px-4 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-inset"
                    type="button">
                    <div class="flex flex-col items-center">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Comprador</span>
                    </div>
                </button>
                <div class="w-px bg-gray-200"></div>
                <button 
                    @click="activeTab = 'seller'; role = 'seller'"
                    :class="{ 
                        'bg-emerald-50 text-emerald-700 border-emerald-200': activeTab === 'seller', 
                        'bg-white text-gray-600 hover:bg-gray-50': activeTab !== 'seller' 
                    }"
                    class="flex-1 py-3 px-4 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-inset"
                    type="button">
                    <div class="flex flex-col items-center">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Vendedor</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Role Description -->
        <div class="text-center">
            <div x-show="activeTab === 'customer'" class="text-sm text-gray-600 bg-blue-50 p-3 rounded-md">
                <svg class="w-4 h-4 text-blue-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Conta para realizar compras no marketplace
            </div>
            <div x-show="activeTab === 'seller'" class="text-sm text-gray-600 bg-orange-50 p-3 rounded-md">
                <svg class="w-4 h-4 text-orange-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Conta para vender produtos no marketplace
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Hidden Role Input -->
            <input type="hidden" name="role" x-model="role">

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nome Completo')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="name" 
                    class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name" 
                    placeholder="Seu nome completo" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('E-mail')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username" 
                    placeholder="seu@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Telefone/WhatsApp')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="phone" 
                    class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" 
                    type="tel" 
                    name="phone" 
                    :value="old('phone')" 
                    required 
                    autocomplete="tel" 
                    placeholder="(11) 99999-9999" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Senha')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password"
                    placeholder="Sua senha" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="password_confirmation" 
                    class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirme sua senha" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Role validation error -->
            <x-input-error :messages="$errors->get('role')" class="mt-2" />

            <!-- Seller Information Notice -->
            <div x-show="role === 'seller'" x-transition class="p-4 bg-orange-50 border border-orange-200 rounded-md">
                <div class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-orange-700">
                            <span class="font-medium">Importante:</span> Após o cadastro como vendedor, você precisará completar seu perfil com documentos e aguardar aprovação do administrador para começar a vender.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <x-primary-button class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                    Criar Conta
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" 
                       class="font-medium text-emerald-600 hover:text-emerald-700 hover:underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-md transition-colors">
                        Entrar
                    </a>
                </p>
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
@endsection
