<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'LOGOS') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- 【修正】bodyタグに背景色を追加し、スクロール時の白い余白を防止 --}}
    <body class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-[#131314]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            
            <div class="mb-4">
                <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                    {{-- 【修正】スクリーンリーダー用にテキストを追加し、装飾は隠す --}}
                    <span class="sr-only">LOGOSトップページへ戻る</span>
                    <x-application-logo class="w-10 h-10 fill-current text-blue-600 dark:text-blue-500" aria-hidden="true" />
                    <span class="text-4xl font-black text-blue-900 dark:text-blue-400 tracking-widest" aria-hidden="true">LOGOS</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-[#1e1f20] shadow-md overflow-hidden sm:rounded-2xl border border-gray-200 dark:border-gray-800">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>