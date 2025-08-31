<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Marketplace'))</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom Layout CSS --}}
    @if(!empty($customLayoutCSS ?? ''))
        <style id="custom-layout-css">
            {!! $customLayoutCSS !!}
        </style>
    @endif
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold">{{ config('app.name', 'Marketplace') }}</h1>
                        </div>
                        
                        @auth
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.sellers.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">
                                        Vendedores
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>
                    
                    @auth
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                        {{ Auth::user()->name }}
                                    </button>
                                </x-slot>
                                
                                <x-slot name="content">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                            Sair
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>