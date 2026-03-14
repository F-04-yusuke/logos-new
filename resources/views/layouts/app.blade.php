<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LOGOS - 政治・経済情報ランキング</title>
    <style>
        .text-brand {
            color: #4F46E5;
        }

        /* インディゴブルー */
        .dark .text-brand {
            color: #818CF8;
        }
    </style>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-gray-100 dark:bg-[#131314] flex h-screen overflow-hidden">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0">

            @include('layouts.navigation')

            @isset($header)
            <header class="bg-white dark:bg-[#1e1f20] border-b dark:border-transparent flex-shrink-0">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>

        </div>

    </div>
</body>

</html>