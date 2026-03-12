<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-pink-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
            参考になった一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: 'info' }" class="p-4 sm:p-8 bg-white dark:bg-[#1e1f20] shadow sm:rounded-lg border border-gray-200 dark:border-gray-800">
                
                <div class="flex border-b border-gray-300 dark:border-gray-800 mb-6 overflow-x-auto">
                    <button @click="activeTab = 'info'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }" class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        情報 ({{ $likedPosts->count() }})
                    </button>
                    <button @click="activeTab = 'comments'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }" class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        コメント ({{ $likedComments->count() }})
                    </button>
                    <button @click="activeTab = 'analysis'" :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'analysis', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'analysis' }" class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        分析・図解 ({{ $likedAnalyses->count() }})
                    </button>
                </div>

                <div x-show="activeTab === 'info'" x-cloak class="space-y-4">
                    @forelse ($likedPosts as $post)
                    <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <span class="inline-block px-2 py-0.5 text-xs rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $post->category }}</span>
                            <div class="text-right text-[10px] text-gray-500 dark:text-gray-500">
                                <span class="font-bold">{{ $post->user->name }}</span> | <span>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                        <a href="{{ $post->url }}" target="_blank" class="block font-bold text-sm text-gray-900 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 mb-2 truncate transition-colors">{{ $post->title ?: $post->url }}</a>
                        @if ($post->comment)
                        <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1 bg-white dark:bg-[#1e1f20] p-3 rounded border border-gray-200 dark:border-gray-800">{{ $post->comment }}</div>
                        @endif
                        <div class="mt-3 text-[10px] text-gray-500 dark:text-gray-400">
                            連携先トピック: <a href="{{ route('topics.show', $post->topic) }}" class="text-blue-500 hover:text-blue-400 hover:underline transition-colors">{{ $post->topic->title }}</a>
                        </div>
                        <div class="mt-3 flex items-center justify-end border-t border-gray-200 dark:border-gray-800 pt-3">
                            <span class="flex items-center space-x-1 text-pink-500 font-bold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg><span class="text-sm">{{ $post->likes_count }}</span></span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-6 text-sm">いいねした情報はありません。</p>
                    @endforelse
                </div>

                <div x-show="activeTab === 'comments'" x-cloak class="space-y-4">
                    @forelse ($likedComments as $comment)
                    <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col transition-colors">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                            <span class="text-[10px] text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mb-3">{{ $comment->body }}</p>
                        <div class="mt-auto text-[10px] text-gray-500 dark:text-gray-400">
                            連携先トピック: <a href="{{ route('topics.show', $comment->topic) }}" class="text-blue-500 hover:text-blue-400 hover:underline transition-colors">{{ $comment->topic->title }}</a>
                        </div>
                        <div class="mt-3 flex items-center justify-end border-t border-gray-200 dark:border-gray-800 pt-3">
                            <span class="flex items-center space-x-1 text-pink-500 font-bold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg><span class="text-sm">{{ $comment->likes_count }}</span></span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-6 text-sm">いいねしたコメントはありません。</p>
                    @endforelse
                </div>

                <div x-show="activeTab === 'analysis'" x-cloak class="space-y-4">
                    @forelse ($likedAnalyses as $analysis)
                    <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                @if($analysis->type === 'tree') <span class="text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 px-2 py-0.5 rounded border border-transparent dark:border-blue-800/50">ロジックツリー</span>
                                @elseif($analysis->type === 'matrix') <span class="text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 px-2 py-0.5 rounded border border-transparent dark:border-purple-800/50">総合評価表</span>
                                @elseif($analysis->type === 'swot')
                                @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                                <span class="text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 px-2 py-0.5 rounded border border-transparent dark:border-green-800/50">{{ $isPest ? 'PEST分析' : 'SWOT分析' }}</span>
                                @endif
                            </div>
                            <div class="text-right text-[10px] text-gray-500 dark:text-gray-500">
                                <span class="font-bold">{{ $analysis->user->name }}</span> | <span>{{ $analysis->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('analyses.show', $analysis) }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm sm:text-base line-clamp-2 transition-colors hover:underline mb-2">
                            {{ $analysis->title }}
                        </a>
                        <div class="mt-auto text-[10px] text-gray-500 dark:text-gray-400">
                            連携先トピック: <a href="{{ route('topics.show', $analysis->topic) }}" class="text-blue-500 hover:text-blue-400 hover:underline transition-colors">{{ $analysis->topic->title }}</a>
                        </div>
                        <div class="mt-3 flex items-center justify-end border-t border-gray-200 dark:border-gray-800 pt-3">
                            <span class="flex items-center space-x-1 text-gray-900 dark:text-white font-bold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" /></svg><span class="text-sm">{{ $analysis->likes_count }}</span></span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-6 text-sm">いいねした分析・図解はありません。</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>