{{-- 
   【新規追加】スマホメニュー用の半透明オーバーレイ背景
   サイドバーが開いている時だけスマホ画面全体を暗くし、タップするとサイドバーが閉じます。
--}}
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition.opacity 
     class="fixed inset-0 z-40 bg-black/50 md:hidden" x-cloak></div>

{{-- 
   【修正】スマホ対応：
   - PC(md以上): 相対配置(relative)で、開閉幅(w-64 / w-16)を切り替え。
   - スマホ: 絶対配置(absolute)で画面上に浮かせ、閉じた時は幅0(-translate-x-full)で完全に隠す。
--}}
<aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 md:translate-x-0 md:w-16'" 
        class="absolute md:relative z-50 flex-shrink-0 bg-white dark:bg-[#1e1f20] border-r border-gray-200 dark:border-gray-800 flex flex-col h-full transform transition-all duration-300 ease-in-out overflow-hidden">

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
                    <a href="{{ route('topics.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span class="ms-3 font-bold">ホーム</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.list') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" /></svg>
                        <span class="ms-3 font-bold">カテゴリ一覧</span>
                    </a>
                </li>
            </ul>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="px-2 text-sm font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 mb-2">保存トピック</h3>
                <ul class="space-y-1">
                    @forelse(auth()->user()->savedTopics as $savedTopic)
                        <li>
                            <a href="{{ route('topics.show', $savedTopic) }}" class="flex items-center p-2 text-gray-700 rounded-lg dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 group">
                                <span class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 flex items-center justify-center text-xs font-bold mr-2 shrink-0 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors">
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
                <h3 class="px-2 text-sm font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 mb-2">マイページ</h3>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span class="ms-3">ダッシュボード</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('likes.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                            </svg>
                            <span class="ms-3">参考になった</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('history.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="ms-3">閲覧履歴</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('topics.create') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                            <span class="ms-3">トピックの作成</span>
                            <span class="ml-2 text-[9px] bg-yellow-500 text-white px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="px-2 text-sm font-semibold text-yellow-500 uppercase tracking-wider mb-2 flex items-center">
                    分析ツール 
                    <span class="ml-1.5 text-[9px] bg-yellow-500 text-white px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                </h3>
                <ul class="space-y-1 text-sm font-bold">
                    <li>
                        <a href="{{ route('tools.tree') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-900 dark:text-white transition-colors group">
                            <svg class="w-5 h-5 text-yellow-500 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            <span class="ms-3">ロジックツリー作成</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tools.matrix') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-900 dark:text-white transition-colors group">
                            <svg class="w-5 h-5 text-purple-500 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            <span class="ms-3">総合評価表作成</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tools.swot') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-900 dark:text-white transition-colors group">
                            <svg class="w-5 h-5 text-green-500 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            <span class="ms-3">SWOT分析作成</span>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <ul class="space-y-1 text-sm mt-auto">
                <li><a href="{{ route('profile.edit') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 group transition-colors"><span class="ms-3">設定</span></a></li>
            </ul>

        </div>
    </div>
</aside>