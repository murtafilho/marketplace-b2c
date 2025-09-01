@extends('layouts.guest')

@section('content')
<!-- Mobile-First Forgot Password Form -->
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Esqueceu sua senha?</h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 px-2">
            {{ __('Sem problema! Digite seu e-mail e enviaremos um link para redefinir sua senha.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">
                E-mail <span class="text-red-500">*</span>
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   required 
                   autofocus
                   class="w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('email') border-red-500 @enderror"
                   placeholder="seu@email.com">
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full min-h-[44px] bg-emerald-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-colors">
                Enviar Link de Redefinição
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" 
                   class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    ← Voltar ao login
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
