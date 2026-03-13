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
                                            <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800"><img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"></div>
                                        @else
                                            <div class="w-full aspect-video bg-gray-100 dark:bg-[#1e1f20] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700"><span class="text-xs">No Image</span></div>
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
                                        <p class="text-xs text-gray-400 mb-1">トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:underline">{{ $post->topic->title }}</a></p>
                                        @if ($post->comment)
                                            <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1 bg-gray-50 dark:bg-[#1e1f20] p-2 rounded">{{ $post->comment }}</div>
                                        @endif
                                        
                                        @if($post->supplement)
                                            <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800/50 text-sm">
                                                <span class="font-bold text-blue-600 dark:text-blue-400 text-[10px] block mb-1">✅ 投稿者からの補足</span>
                                                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $post->supplement }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                            <span class="text-xs font-bold">{{ $post->likes()->count() ?? 0 }}</span>
                                        </div>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('削除しますか？');" class="m-0 p-0">@csrf @method('DELETE')<button type="submit" class="text-xs text-red-400 hover:text-red-600">削除</button></form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">まだ投稿した情報はありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-4">
                        @forelse(auth()->user()->comments()->whereNull('parent_id')->with(['replies' => function($q) { $q->oldest(); }])->latest()->get() as $comment)
                            <div class="p-4 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
                                
                                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
                                    <p class="text-xs text-gray-500">トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $comment->topic->title }}</a></p>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] bg-gray-100 text-gray-800 dark:bg-[#1e1f20] dark:text-gray-300 px-1.5 py-0.5 rounded font-bold">あなたの投稿</span>
                                    </div>
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $comment->body }}</p>
                                    
                                    <div class="mt-2 flex items-center text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                        <span class="text-xs font-bold">{{ $comment->likes()->count() ?? 0 }}</span>
                                    </div>
                                </div>

                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 space-y-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4 ml-2">
                                        @foreach($comment->replies as $reply)
                                            <div class="relative">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-1.5 py-0.5 rounded font-bold">投稿者（補足）</span>
                                                    <span class="text-[10px] text-gray-500">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $reply->body }}</p>
                                                
                                                <div class="mt-1 flex items-center text-gray-500 dark:text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 mr-1"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                                    <span class="text-[10px] font-bold">{{ $reply->likes()->count() ?? 0 }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
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