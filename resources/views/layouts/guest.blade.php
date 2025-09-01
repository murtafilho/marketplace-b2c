<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @hasSection('title')
                @yield('title') - {{ config('app.name', 'Laravel') }}
            @else
                {{ config('app.name', 'Laravel') }}
            @endif
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col">
            <!-- Mobile Header -->
            <div class="flex-shrink-0 bg-white shadow-sm border-b sm:hidden">
                <div class="px-4 py-3 flex items-center justify-between">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('storage/assets/logo_coruja.svg') }}" 
                             alt="Vale do Sol Logo" 
                             class="w-8 h-8">
                        <span class="ml-2 text-lg font-semibold text-gray-900">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col justify-center py-6 sm:py-12">
                <!-- Desktop Logo -->
                <div class="hidden sm:flex justify-center mb-6">
                    <a href="/" class="flex flex-col items-center">
                        <img src="{{ asset('storage/assets/logo_coruja.svg') }}" 
                             alt="Vale do Sol Logo" 
                             class="w-16 h-16">
                        <span class="mt-2 text-xl font-semibold text-gray-900">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </a>
                </div>

                <!-- Auth Form Container -->
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="w-full max-w-md mx-auto">
                        <div class="bg-white py-6 px-4 sm:px-6 shadow-lg border border-gray-200 sm:rounded-xl">
                            @yield('content')
                        </div>
                        
                        <!-- Back to Home Link (Mobile) -->
                        <div class="mt-6 text-center sm:hidden">
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Voltar ao Marketplace
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 py-4 px-4 sm:px-6 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </body>
</html>
