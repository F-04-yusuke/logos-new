<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-[#131314]">
            
            <div class="mb-4">
                <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <x-application-logo class="w-10 h-10 fill-current text-blue-600 dark:text-blue-500" />
                    <span class="text-4xl font-black text-blue-900 dark:text-blue-400 tracking-widest">LOGOS</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-[#1e1f20] shadow-xl overflow-hidden sm:rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
                {{ $slot }}
            </div>
            
        </div>
    </body>
</html>