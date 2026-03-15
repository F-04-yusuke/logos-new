<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-900 dark:text-gray-100 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
            </svg>
            参考になった一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: 'info' }" class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">

                <div class="flex border-b border-gray-200 dark:border-gray-800 overflow-x-auto scrollbar-hide">
                    <button @click="activeTab = 'info'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        情報 ({{ $likedPosts->count() }})
                    </button>
                    <button @click="activeTab = 'comments'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        コメント ({{ $likedComments->count() }})
                    </button>
                    <button @click="activeTab = 'analysis'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'analysis', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'analysis' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        分析・図解 ({{ $likedAnalyses->count() }})
                    </button>
                </div>

                <div class="p-4 sm:p-6">
                    
                    {{-- 情報タブ --}}
                    <div x-show="activeTab === 'info'" x-cloak class="space-y-6">
                        @forelse ($likedPosts as $post)
                            <div class="flex flex-col gap-1.5">
                                <x-post-card :post="$post" />
                                <div class="text-right px-2">
                                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                        🔗 投稿先トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $post->topic->title }}</a>
                                    </span>
                                </div>
                            </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねした情報はありません。</p>
                        @endforelse
                    </div>

                    {{-- コメントタブ --}}
                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-6">
                        @forelse ($likedComments as $comment)
                            <div class="flex flex-col gap-1.5">
                                <div class="bg-white dark:bg-[#1e1f20] px-4 rounded-lg border border-gray-200 dark:border-transparent shadow-sm">
                                    <x-comment-card :comment="$comment" />
                                </div>
                                <div class="text-right px-2">
                                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                        🔗 投稿先トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $comment->topic->title }}</a>
                                    </span>
                                </div>
                            </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねしたコメントはありません。</p>
                        @endforelse
                    </div>

                    {{-- 分析・図解タブ --}}
                    <div x-show="activeTab === 'analysis'" x-cloak class="space-y-6">
                        @forelse ($likedAnalyses as $analysis)
                            <div class="flex flex-col gap-1.5">
                                <x-analysis-card :analysis="$analysis" />
                                <div class="text-right px-2">
                                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                        🔗 公開先トピック: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $analysis->topic->title }}</a>
                                    </span>
                                </div>
                            </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねした分析・図解はありません。</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>