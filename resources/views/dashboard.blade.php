<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: 'posts' }" class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">
                
                <div class="flex border-b border-gray-200 dark:border-gray-800 overflow-x-auto">
                    <button @click="activeTab = 'posts'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'posts' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">投稿した情報</button>
                    <button @click="activeTab = 'comments'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'comments' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">自分のコメント</button>
                    <button @click="activeTab = 'topics'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'topics', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'topics' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        作成したトピック
                        <span class="ml-1 text-[9px] bg-yellow-500 text-white px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                    <button @click="activeTab = 'analyses'" :class="{ 'border-yellow-500 text-yellow-600 dark:text-yellow-500 font-bold': activeTab === 'analyses', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'analyses' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        作成した分析・図解
                        <span class="ml-1 text-[9px] bg-yellow-500 text-white px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                </div>

                <div class="p-6">
                    
                    <div x-show="activeTab === 'posts'" x-cloak class="space-y-4">
                        @forelse(auth()->user()->posts()->latest()->get() as $post)
                            <div class="p-3 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col md:flex-row gap-3">
                                <div class="md:w-1/4 flex-shrink-0">
                                    <a href="{{ $post->url }}" target="_blank" class="block group">
                                        @if($post->thumbnail_url)
                                            <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                                                <img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @else
                                            <div class="w-full aspect-video bg-gray-100 dark:bg-[#1e1f20] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700">
                                                <span class="text-xs">No Image</span>
                                            </div>
                                        @endif
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-blue-500 line-clamp-2">{{ $post->title ?: 'タイトルなし' }}</h4>
                                    </a>
                                </div>
                                <div class="md:w-3/4 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $post->category }}</span>
                                            <div class="text-right text-xs text-gray-500"><span>{{ $post->created_at->format('Y-m-d H:i') }}</span></div>
                                        </div>
                                        <p class="text-xs text-gray-400 mb-1">連携先トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:underline">{{ $post->topic->title }}</a></p>
                                        @if ($post->comment)
                                            <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1 bg-gray-50 dark:bg-[#1e1f20] p-2 rounded">{{ $post->comment }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex items-center justify-end gap-3">
                                        <a href="{{ route('posts.edit', $post) }}" class="text-xs text-blue-500 hover:text-blue-700">編集</a>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600">削除</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">まだ投稿した情報はありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-3">
                        @forelse(auth()->user()->comments()->latest()->get() as $comment)
                            <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs text-gray-500">
                                        トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $comment->topic->title }}</a>
                                    </p>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                @if($comment->parent_id)
                                    <div class="text-[10px] text-gray-400 mb-1 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 transform rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                        {{ $comment->parent->user->name ?? '誰か' }} への返信
                                    </div>
                                @endif
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">まだコメントしていません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'topics'" x-cloak class="space-y-3">
                        @forelse(auth()->user()->topics()->latest()->get() as $topic)
                            <div class="p-4 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('topics.show', $topic) }}" class="font-bold text-blue-500 hover:underline">{{ $topic->title }}</a>
                                    <p class="text-xs text-gray-500 mt-1">作成日: {{ $topic->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <a href="{{ route('topics.edit', $topic) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">編集</a>
                                    <span class="text-gray-300 dark:text-gray-700">|</span>
                                    <form method="POST" action="{{ route('topics.destroy', $topic) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">削除</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">まだ作成したトピックはありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'analyses'" x-cloak class="space-y-3">
                        @forelse(auth()->user()->analyses()->latest()->get() as $analysis)
                            <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400">{{ $analysis->type }}</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $analysis->title }}</span>
                                    </div>
                                    @if($analysis->topic_id)
                                        <p class="text-xs text-gray-500 mb-1">公開先トピック: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:underline">{{ $analysis->topic->title }}</a></p>
                                    @else
                                        <p class="text-xs text-yellow-600 dark:text-yellow-500 font-bold mb-1">未公開（下書き）</p>
                                    @endif
                                    
                                    @if($analysis->supplement)
                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-[#1e1f20] p-2 rounded border border-gray-200 dark:border-gray-700">
                                            <span class="font-bold text-yellow-600 dark:text-yellow-500">補足:</span> {{ Str::limit($analysis->supplement, 50) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($analysis->topic_id)
                                        <a href="{{ route('analyses.show', $analysis) }}" class="text-xs font-bold text-blue-500 hover:text-blue-700">見る</a>
                                    @else
                                        <a href="{{ route('analyses.edit', $analysis) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">編集</a>
                                    @endif
                                    <span class="text-gray-300 dark:text-gray-700">|</span>
                                    <form method="POST" action="{{ route('analyses.destroy', $analysis) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">削除</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">まだ作成した分析・図解はありません。</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>