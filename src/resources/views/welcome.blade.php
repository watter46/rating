<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>myapp</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="antialiased">
<div class="relative flex justify-center min-h-screen py-4 bg-gray-100 items-top dark:bg-gray-900 sm:items-center sm:pt-0">
    @if (Route::has('login'))
        <div class="fixed top-0 right-0 hidden px-6 py-4 sm:block">
            @auth
                <a href="{{ url('/dashboard') }}" class="font-black text-gray-700 underline dark:text-gray-500">
                    DashBoard</a>
            @else
                <a href="{{ route('login') }}" class="font-black text-gray-700 underline dark:text-gray-500">
                    Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 font-black text-gray-700 underline dark:text-gray-500">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <h1 class="w-full bg-sky-500">トップページです。</h1>
</div>
</body>
</html>