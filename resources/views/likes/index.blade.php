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
                        <div class="p-3 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm flex flex-col md:flex-row gap-3 transition-colors">
                            <div class="md:w-1/4 flex-shrink-0">
                                <a href="{{ $post->url }}" target="_blank" class="block group">
                                    @if($post->thumbnail_url)
                                    <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800"><img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"></div>
                                    @else
                                    <div class="w-full aspect-video bg-gray-100 dark:bg-[#131314] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700"><span class="text-xs">No Image</span></div>
                                    @endif
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-blue-500 line-clamp-2">{{ $post->title ?: 'タイトルなし' }}</h4>
                                </a>
                            </div>
                            <div class="md:w-3/4 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        @if($post->user->avatar)
                                            <img class="h-6 w-6 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . $post->user->avatar) }}" alt="Avatar" />
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            </div>
                                        @endif
                                        <div class="flex items-baseline gap-2">
                                            <span class="font-bold text-[13px] text-gray-900 dark:text-gray-100">{{ $post->user->name }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="ml-2 inline-block px-2 py-0.5 text-[10px] font-bold rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $post->category }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mb-1">トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $post->topic->title }}</a></p>
                                    
                                    @if ($post->comment)
                                    <div class="text-[13px] text-gray-800 dark:text-gray-300 whitespace-pre-wrap mt-1 leading-relaxed">{{ trim($post->comment) }}</div>
                                    @endif
                                    
                                    @if ($post->supplement)
                                    <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800/50 text-sm">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 text-[10px] block mb-0.5">✅ 投稿者からの補足</span>
                                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap text-[13px]">{{ trim($post->supplement) }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-3 flex items-center justify-end gap-3 text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 pt-2">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1 text-blue-500"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                        <span class="text-xs font-bold">{{ $post->likes()->count() ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねした情報はありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-2 mt-2">
                        @forelse ($likedComments as $comment)
                        <div class="flex gap-4 items-start py-4 border-b border-gray-100 dark:border-gray-800/60">
                            <div class="shrink-0 mt-1">
                                @if($comment->user->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . $comment->user->avatar) }}" alt="Avatar" />
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 mb-0.5">
                                    <span class="font-bold text-[13px] text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] text-gray-500">トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ Str::limit($comment->topic->title, 20) }}</a></span>
                                    @if($comment->parent_id)
                                        <span class="text-[10px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-1.5 py-0.5 rounded font-bold border border-gray-200 dark:border-gray-700">{{ $comment->parent->user->name ?? '誰か' }} への返信</span>
                                    @endif
                                </div>
                                
                                <p class="text-[14px] text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ trim($comment->body) }}</p>
                                
                                <div class="mt-2 flex items-center justify-end gap-3 text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1 text-blue-500"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                        <span class="text-xs font-bold">{{ $comment->likes()->count() ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-6 text-sm">いいねしたコメントはありません。</p>
                        @endforelse
                    </div>

                    <div x-show="activeTab === 'analysis'" x-cloak class="space-y-4">
                        @forelse ($likedAnalyses as $analysis)
                        <div class="p-4 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-colors flex flex-col gap-3">
                            
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 mt-0.5">
                                        @if($analysis->user->avatar)
                                            <img class="h-8 w-8 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . $analysis->user->avatar) }}" alt="Avatar" />
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-baseline gap-2">
                                            <span class="font-bold text-[14px] text-gray-900 dark:text-gray-100">{{ $analysis->user->name }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $analysis->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mt-0.5">
                                            @if($analysis->type === 'tree') <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border border-blue-200 text-blue-600 dark:border-blue-800 dark:text-blue-400">ロジックツリー</span>
                                            @elseif($analysis->type === 'matrix') <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border border-purple-200 text-purple-600 dark:border-purple-800 dark:text-purple-400">総合評価表</span>
                                            @elseif($analysis->type === 'swot')
                                            @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border border-green-200 text-green-600 dark:border-green-800 dark:text-green-400">{{ $isPest ? 'PEST分析' : 'SWOT分析' }}</span>
                                            @elseif($analysis->type === 'image')
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border border-orange-200 text-orange-600 dark:border-orange-800 dark:text-orange-400">オリジナル図解</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-gray-500">トピック: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:underline font-bold">{{ $analysis->topic->title }}</a></span>
                                </div>
                            </div>

                            <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#131314] p-4 text-sm overflow-hidden w-full relative" style="max-height: 250px;">
                                <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-gray-50 to-transparent dark:from-[#131314] dark:to-transparent pointer-events-none"></div>

                                @php $previewData = is_string($analysis->data) ? json_decode($analysis->data, true) : $analysis->data; @endphp

                                @if($analysis->type === 'swot')
                                <div class="font-bold text-base text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-800">{{ $analysis->title }}</div>
                                @endif

                                @if($analysis->type === 'tree' && !empty($previewData))
                                    @php $nodes = isset($previewData['nodes']) ? $previewData['nodes'] : $previewData; @endphp
                                    <div class="space-y-3">
                                        @foreach(array_slice($nodes, 0, 3) as $node)
                                        <div class="flex gap-2">
                                            <span class="font-bold text-blue-500 shrink-0">{{ $node['speaker'] ?? '' }}:</span>
                                            <span class="text-gray-700 dark:text-gray-300 truncate">{{ $node['text'] ?? '' }}</span>
                                        </div>
                                        @if(!empty($node['children']))
                                        @foreach(array_slice($node['children'], 0, 1) as $child)
                                        <div class="ml-4 flex gap-2 border-l-2 border-gray-300 dark:border-gray-700 pl-2">
                                            <span class="font-bold text-gray-500 shrink-0">↳ {{ $child['speaker'] ?? '' }}:</span>
                                            <span class="text-gray-600 dark:text-gray-400 truncate">{{ $child['text'] ?? '' }}</span>
                                        </div>
                                        @endforeach
                                        @endif
                                        @endforeach
                                    </div>
                                @elseif($analysis->type === 'matrix' && isset($previewData['items']))
                                    <div>
                                        <div class="font-bold text-gray-500 mb-2">【評価項目一覧】</div>
                                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2 ml-1">
                                            @foreach(array_slice($previewData['items'], 0, 3) as $item)
                                            <li class="truncate">{{ $item['itemTitle'] ?? '' }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @elseif($analysis->type === 'swot')
                                    @php
                                    $isPest = isset($previewData['framework']) && $previewData['framework'] === 'PEST';
                                    $b1 = $previewData['box1'] ?? $previewData['strengths'] ?? [];
                                    $b2 = $previewData['box2'] ?? $previewData['weaknesses'] ?? [];
                                    @endphp
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="font-bold text-blue-500 mb-1 inline-block">{{ $isPest ? 'P (政治)' : 'S (強み)' }}:</span>
                                            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1 text-xs">
                                                @forelse(array_slice($b1, 0, 2) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                            </ul>
                                        </div>
                                        <div>
                                            <span class="font-bold text-red-500 mb-1 inline-block">{{ $isPest ? 'E (経済)' : 'W (弱み)' }}:</span>
                                            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1 text-xs">
                                                @forelse(array_slice($b2, 0, 2) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                @elseif($analysis->type === 'image' && isset($previewData['image_path']))
                                    <div class="font-bold text-base text-gray-900 dark:text-gray-100 mb-3">{{ $analysis->title }}</div>
                                    <div class="w-full flex justify-center bg-white dark:bg-[#1e1f20] rounded p-2">
                                        <img src="{{ asset('storage/' . $previewData['image_path']) }}" alt="{{ $analysis->title }}" class="max-w-full max-h-[150px] object-contain rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                                    </div>
                                @endif
                            </div>

                            @if($analysis->supplement)
                            <div class="mt-1 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800/50 text-sm">
                                <span class="font-bold text-yellow-600 dark:text-yellow-500 text-[10px] block mb-0.5">✅ 投稿者からの補足</span>
                                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap text-[13px]">{{ trim($analysis->supplement) }}</p>
                            </div>
                            @endif

                            <div class="mt-1 flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-3 text-gray-500 dark:text-gray-400">
                                <a href="{{ route('analyses.show', $analysis) }}" class="text-xs font-bold text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    もっと見る <span class="ml-1 text-[10px]">▶</span>
                                </a>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1 text-blue-500"><path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.067.85.067 1.285a11.2 11.2 0 0 1-2.649 7.324C18.784 19.38 17.06 20 15.25 20h-4.735a7.22 7.22 0 0 1-3.022-.662Z" /><path d="M1.5 8.625c0-1.036.84-1.875 1.875-1.875h1.5A1.875 1.875 0 0 1 6.75 8.625v10.5a1.875 1.875 0 0 1-1.875 1.875h-1.5A1.875 1.875 0 0 1 1.5 19.125v-10.5Z" /></svg>
                                        <span class="text-xs font-bold">{{ $analysis->likes()->count() ?? 0 }}</span>
                                    </div>
                                </div>
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