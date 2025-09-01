@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <!-- Header -->
    <div class="mb-6 text-center">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
            Entrar na sua conta
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Acesse seu marketplace favorito
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail')" class="text-sm font-medium text-gray-700" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" 
                type="email" 
                name="email" 
                :value="old('email', request('email'))" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Senha')" class="text-sm font-medium text-gray-700" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-emerald-600 hover:text-emerald-700 hover:underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-md transition-colors" 
                       href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <x-text-input 
                id="password" 
                class="block mt-1 w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                type="password"
                name="password"
                value="{{ request('password') }}"
                required 
                autocomplete="current-password"
                placeholder="Sua senha" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" 
                   type="checkbox" 
                   class="h-4 w-4 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" 
                   name="remember">
            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                Lembrar de mim
            </label>
        </div>

        <!-- Login Button -->
        <div class="pt-2">
            <x-primary-button class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                Entrar
            </x-primary-button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Não tem uma conta?
                <a href="{{ route('register') }}" 
                   class="font-medium text-emerald-600 hover:text-emerald-700 hover:underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-md transition-colors">
                    Criar conta
                </a>
            </p>
        </div>
    </form>

    <!-- Quick Actions (Mobile) -->
    <div class="mt-6 sm:hidden">
        <div class="text-center">
            <p class="text-xs text-gray-500 mb-3">Acesso rápido</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('register', ['role' => 'customer']) }}" 
                   class="flex flex-col items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs text-gray-600">Comprador</span>
                </a>
                <a href="{{ route('register', ['role' => 'seller']) }}" 
                   class="flex flex-col items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-xs text-gray-600">Vendedor</span>
                </a>
            </div>
        </div>
    </div>
@endsection
