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

                <div class="flex border-b border-gray-200 dark:border-gray-800 overflow-x-auto">
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

                <div class="p-6">
                    <div x-show="activeTab === 'info'" x-cloak class="space-y-4">
                        @forelse ($likedPosts as $post)
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
                                        <div class="text-right text-[10px] text-gray-500"><span class="font-bold">{{ $post->user->name }}</span> | <span>{{ $post->created_at->format('Y-m-d H:i') }}</span></div>
                                    </div>
                                    <p class="text-xs text-gray-400 mb-1">トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:underline">{{ $post->topic->title }}</a></p>
                                    @if ($post->comment)
                                    <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1 bg-gray-50 dark:bg-[#1e1f20] p-2 rounded">{{ $post->comment }}</div>
                                    @endif
                                    @if ($post->supplement)
                                    <div class="mt-2 text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap bg-blue-50 dark:bg-blue-900/20 p-3 rounded border border-blue-200 dark:border-blue-800/50">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 text-[10px] block mb-1">✅ 投稿者からの補足</span>
                                        {{ $post->supplement }}
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-2 flex items-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                                        <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" />
                                        <path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" />
                                    </svg>
                                    <span class="text-xs font-bold">{{ $post->likes()->count() ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねした情報はありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-4">
                        @forelse ($likedComments as $comment)
                        <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-500">トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $comment->topic->title }}</a></p>
                            </div>
                            @if($comment->parent_id)
                            <div class="text-[10px] text-gray-500 mb-1.5 flex items-center bg-gray-200 dark:bg-gray-700 w-fit px-2 py-0.5 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 transform rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                {{ $comment->parent->user->name ?? '誰か' }} への返信
                            </div>
                            @endif
                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $comment->body }}</p>
                            <div class="mt-2 flex items-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                                    <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" />
                                    <path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" />
                                </svg>
                                <span class="text-xs font-bold">{{ $comment->likes()->count() ?? 0 }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねしたコメントはありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'analysis'" x-cloak class="space-y-4">
                        @forelse ($likedAnalyses as $analysis)
                        <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400">{{ $analysis->type }}</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $analysis->title }}</span>
                                    </div>
                                    <div class="text-right text-[10px] text-gray-500"><span class="font-bold">{{ $analysis->user->name }}</span> | <span>{{ $analysis->created_at->format('Y-m-d H:i') }}</span></div>
                                </div>
                                <p class="text-xs text-gray-500 mb-1">トピック: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $analysis->topic->title }}</a></p>

                                @if($analysis->supplement)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-[#1e1f20] p-2 rounded border border-gray-200 dark:border-gray-700">
                                    <span class="font-bold text-yellow-600 dark:text-yellow-500">補足:</span> {{ Str::limit($analysis->supplement, 50) }}
                                </div>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <div class="flex items-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                                        <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" />
                                        <path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" />
                                    </svg>
                                    <span class="text-xs font-bold">{{ $analysis->likes()->count() ?? 0 }}</span>
                                </div>
                                <a href="{{ route('analyses.show', $analysis) }}" class="text-xs font-bold text-blue-500 hover:text-blue-700 mt-1">もっと見る ▶</a>
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