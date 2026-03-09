<aside :class="sidebarOpen ? 'w-64' : 'w-16'" 
        class="flex-shrink-0 bg-white dark:bg-[#1e1f20] border-r border-gray-200 dark:border-transparent flex flex-col h-full transition-all duration-300 ease-in-out overflow-hidden">

    <div class="h-16 flex items-center px-4 shrink-0 border-b border-transparent">
        <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-gray-300 focus:outline-none transition">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden">
        <div x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="py-4 px-3 space-y-6 w-64">
            
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('topics.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <span class="text-xl">🏠</span>
                        <span class="ms-3 font-bold">ホーム</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.list') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <span class="text-xl">📁</span>
                        <span class="ms-3 font-bold">カテゴリ一覧</span>
                    </a>
                </li>
            </ul>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="px-2 text-sm font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 mb-2">
                    保存トピック
                </h3>
                <ul class="space-y-1">
                    @forelse(auth()->user()->savedTopics as $savedTopic)
                        <li>
                            <a href="{{ route('topics.show', $savedTopic) }}" class="flex items-center p-2 text-gray-700 rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <span class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold mr-2 shrink-0">
                                    {{ mb_substr($savedTopic->title, 0, 1) }}
                                </span>
                                <span class="text-sm truncate">{{ $savedTopic->title }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-2 text-xs text-gray-400">まだ保存したトピックはありません</li>
                    @endforelse
                </ul>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="px-2 text-sm font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 mb-2">
                    マイページ
                </h3>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <span class="text-lg">👤</span><span class="ms-3">ダッシュボード</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <span class="text-lg">🕒</span><span class="ms-3">閲覧履歴</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('likes.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <span class="text-lg">👍</span><span class="ms-3">参考になった</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('topics.create') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <span class="text-lg">✏️</span><span class="ms-3">トピックの作成</span>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <ul class="space-y-1 text-sm mt-auto">
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <span class="text-lg">⚙️</span><span class="ms-3">設定</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <span class="text-lg">❓</span><span class="ms-3">ヘルプ</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <span class="text-lg">🗣️</span><span class="ms-3">フィードバック</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</aside>