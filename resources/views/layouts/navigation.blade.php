{{-- 【修正】Alpine.jsのデータに searchOpen を追加し、検索窓の開閉状態を管理できるようにしました --}}
<nav x-data="{ open: false, searchOpen: false }" class="bg-white dark:bg-[#131314] border-b border-gray-100 dark:border-transparent">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- 1. 左側：ロゴ ＆ スマホ用サイドバー開閉ボタン --}}
            <div class="flex items-center shrink-0 gap-2">
                <button @click="sidebarOpen = true" class="md:hidden inline-flex items-center justify-center p-1.5 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-[#222222] dark:hover:text-gray-300 focus:outline-none transition">
                    <span class="sr-only">メニューを開く</span>
                    <svg aria-hidden="true" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('topics.index') }}" class="flex items-center">
                    <x-application-logo class="block h-8 w-auto object-contain" />
                </a>
            </div>

            {{-- 2. 中央：PC用検索バー（スマホでは非表示になるよう hidden sm:flex に変更） --}}
            <div class="hidden sm:flex flex-1 items-center justify-center px-4 sm:px-6 lg:px-12">
                <div class="w-full max-w-2xl flex justify-center">
                    <form method="GET" action="{{ route('topics.index') }}" class="flex w-full">
                        <input type="search" name="search" placeholder="トピックを検索..." value="{{ request('search') }}"
                            class="w-full bg-white dark:bg-[#121212] border border-gray-300 dark:border-gray-700 rounded-l-full px-5 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:text-white sm:text-sm shadow-inner dark:shadow-none transition-colors">
                        <button type="submit" class="bg-gray-100 dark:bg-[#222222] border border-l-0 border-gray-300 dark:border-gray-700 rounded-r-full px-5 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-[#303030] transition-colors flex items-center justify-center">
                            <span class="sr-only">検索する</span>
                            <svg aria-hidden="true" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- 3. 右側：PC用マイページ --}}
            <div class="hidden sm:flex sm:items-center gap-2">
                <div class="hidden space-x-8 sm:-my-px sm:mr-2 sm:flex">
                    @if(auth()->id() === 1)
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        カテゴリ管理
                    </x-nav-link>
                    @endif
                </div>

                {{-- PC用：通知ベルアイコン --}}
                @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="通知">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white leading-none">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center justify-center rounded-full focus:outline-none transition-transform hover:scale-105 border-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 p-0.5">
                            @if(Auth::user()->avatar)
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}のアイコン" />
                            @else
                            <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                <svg aria-hidden="true" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('history.index')">
                            閲覧履歴
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- 4. 右側：スマホ用アイコン群（検索ボタン ＋ 通知ベル ＋ ハンバーガー） --}}
            <div class="flex items-center gap-1 sm:hidden">
                {{-- スマホ用：通知ベルアイコン --}}
                @php $unreadCount = $unreadCount ?? auth()->user()->unreadNotificationsCount(); @endphp
                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-[#222222] focus:outline-none transition" aria-label="通知">
                    <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white leading-none">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- 【追加】スマホ用検索トグルボタン（虫眼鏡） --}}
                <button @click="searchOpen = !searchOpen" class="p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-[#222222] focus:outline-none transition">
                    <span class="sr-only">検索を開く</span>
                    <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                {{-- マイページ用ハンバーガー --}}
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-[#222222] focus:outline-none focus:bg-gray-100 dark:focus:bg-[#222222] transition duration-150 ease-in-out">
                    <span class="sr-only">アカウントメニューを開く</span>
                    <svg aria-hidden="true" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 【追加】スマホ用：スライドダウンするフル幅検索バー --}}
    <div x-show="searchOpen" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        {{-- 【修正】上部の白い線（border-t...）を削除してノイズを消しました --}}
        class="sm:hidden px-4 pb-3 pt-3 bg-white dark:bg-[#131314] shadow-sm">
        <form method="GET" action="{{ route('topics.index') }}" class="flex w-full">
            <input type="search" name="search" placeholder="トピックを検索..." value="{{ request('search') }}"
                class="w-full bg-gray-50 dark:bg-[#121212] border border-gray-300 dark:border-gray-700 rounded-l-full px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:text-white text-sm transition-colors">
            <button type="submit" class="bg-gray-200 dark:bg-[#222222] border border-l-0 border-gray-300 dark:border-gray-700 rounded-r-full px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-[#303030] transition-colors flex items-center justify-center">
                <span class="sr-only">検索する</span>
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </form>
    </div>

    {{-- 既存：スマホ用マイページメニュー --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->id() === 1)
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                カテゴリ管理
            </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 flex items-center">
                <div class="shrink-0 mr-3">
                    @if(Auth::user()->avatar)
                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}のアイコン" />
                    @else
                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                        <svg aria-hidden="true" class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('history.index')">
                    閲覧履歴
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>