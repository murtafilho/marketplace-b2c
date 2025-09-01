@extends('layouts.guest')

@section('content')
<!-- Mobile-First Reset Password Form -->
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Redefinir Senha</h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 px-2">
            Digite sua nova senha abaixo
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">
                E-mail <span class="text-red-500">*</span>
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}"
                   required 
                   autofocus 
                   autocomplete="username"
                   class="w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">
                Nova Senha <span class="text-red-500">*</span>
            </label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('password') border-red-500 @enderror"
                   placeholder="Mínimo 8 caracteres">
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirmar Nova Senha <span class="text-red-500">*</span>
            </label>
            <input id="password_confirmation" 
                   type="password"
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors"
                   placeholder="Digite a senha novamente">
            @error('password_confirmation')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full min-h-[44px] bg-emerald-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-colors">
                Redefinir Senha
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
