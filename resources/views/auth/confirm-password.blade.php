@extends('layouts.guest')

@section('content')
<!-- Mobile-First Confirm Password Form -->
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-1a2 2 0 00-2-2H6a2 2 0 00-2 2v1a2 2 0 002 2zM12 9a3 3 0 100-6 3 3 0 000 6z"></path>
            </svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Área Segura</h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 px-2">
            {{ __('Esta é uma área segura. Por favor, confirme sua senha para continuar.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">
                Senha Atual <span class="text-red-500">*</span>
            </label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="current-password"
                   class="w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('password') border-red-500 @enderror"
                   placeholder="Digite sua senha atual">
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full min-h-[44px] bg-emerald-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-colors">
                Confirmar
            </button>

            <div class="text-center">
                <a href="{{ route('dashboard') }}" 
                   class="text-sm text-gray-600 hover:text-gray-700 font-medium">
                    ← Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
