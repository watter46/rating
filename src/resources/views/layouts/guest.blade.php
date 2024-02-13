<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen font-sans antialiased text-gray-900">
        <div class="flex flex-col h-screen bg-gray-100 sm:justify-center sm:pt-0 dark:bg-gray-900">
            {{-- Header --}}
            <div class="relative flex items-center p-5 bg-gray-900 gap-x-5">
                <p class="w-full text-3xl font-black text-gray-400">BluesRate</p>

                @if (Route::has('login'))
                    <div class="flex items-center justify-end w-full h-full sm:hidden"
                        x-data="{ isOpen: false }">
                        <x-svg.setting-image
                            class="w-8 h-8 cursor-pointer fill-gray-400"
                            @click="isOpen = !isOpen" />

                        <div x-show="isOpen"
                            @click.outside="isOpen = false"
                            x-cloak
                            class="absolute right-0 flex flex-col w-full px-5 bg-sky-950 py-7 gap-y-7 top-full">
                            @auth
                                <a href="{{ url('/fixtures') }}" class="text-2xl font-black text-gray-400 dark:text-gray-400">
                                    fixtures
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-2xl font-black text-gray-400 dark:text-gray-400">
                                    Login
                                </a>
                                
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-2xl font-black text-gray-400 dark:text-gray-400">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                
                    <div class="fixed top-0 right-0 hidden px-6 py-4 sm:block">
                        @auth
                            <a href="{{ url('/fixtures') }}" class="text-lg font-black text-gray-400 dark:text-gray-400">
                                fixtures</a>
                        @else
                            <a href="{{ route('login') }}" class="text-lg font-black text-gray-400 dark:text-gray-400">
                                Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-lg font-black text-gray-400 dark:text-gray-400">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            {{-- Main --}}
            <div class="flex items-center justify-center h-full">
                <div class="w-full h-full px-6 py-3 overflow-hidden shadow-md">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
