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
    </head>
    <body class="min-h-screen font-sans antialiased text-gray-900">
        <div class="flex flex-col h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0 dark:bg-gray-900">
            {{-- Header --}}
            <div class="flex items-center p-2 border-b-2 border-gray-500 bg-sky-900 gap-x-5">
                <div class="w-fit">
                    <a href="/">
                        <x-application-logo class="w-12 h-12 text-gray-500 fill-current" />
                    </a>
                </div>

                <p class="w-full text-4xl font-black text-gray-400">Rating</p>

                @if (Route::has('login'))
                    <div class="fixed top-0 right-0 hidden px-6 py-4 sm:block">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-lg font-black text-gray-400 underline dark:text-gray-400">
                                DashBoard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-lg font-black text-gray-400 underline dark:text-gray-400">
                                Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-lg font-black text-gray-400 underline dark:text-gray-400">Register</a>
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
    </body>
</html>
