<x-app-layout>
    <div class="w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 text-gray-900 dark:text-gray-100">
            
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-2">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $topic->title }}</h2>
                    
                    <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300 mb-3">{{ $topic->content }}</p>

                    <div x-data="{ timelineExpanded: false }" class="mt-1">
                        <h3 class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-1.5 flex items-center">
                            <span class="mr-1">⏳</span> 前提となる時系列
                        </h3>
                        
                        <div class="border-l-[1.5px] border-gray-300 dark:border-gray-700 ml-1.5 pl-3 space-y-1">
                            <div class="relative flex items-start sm:items-center py-0.5">
                                <div class="absolute left-[-16.5px] top-1.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                <div class="w-16 sm:w-20 font-bold text-blue-500 dark:text-blue-400 text-[11px] shrink-0 pt-0.5 sm:pt-0">2014年2月</div>
                                <div class="flex-1 text-gray-700 dark:text-gray-200 text-[11px] font-bold ml-1 leading-snug sm:truncate">マイダン革命（親露政権崩壊）</div>
                                <span class="ml-2 text-[8px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                            </div>
                            <div class="relative flex items-start sm:items-center py-0.5">
                                <div class="absolute left-[-16.5px] top-1.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                <div class="w-16 sm:w-20 font-bold text-blue-500 dark:text-blue-400 text-[11px] shrink-0 pt-0.5 sm:pt-0">2014年3月</div>
                                <div class="flex-1 text-gray-700 dark:text-gray-200 text-[11px] font-bold ml-1 leading-snug sm:truncate">ロシアによるクリミア併合</div>
                                <span class="ml-2 text-[8px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                            </div>
                            <div class="relative flex items-start sm:items-center py-0.5">
                                <div class="absolute left-[-16.5px] top-1.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                                <div class="w-16 sm:w-20 font-bold text-blue-500 dark:text-blue-400 text-[11px] shrink-0 pt-0.5 sm:pt-0">2014年9月</div>
                                <div class="flex-1 text-gray-700 dark:text-gray-200 text-[11px] font-bold ml-1 leading-snug sm:truncate">ミンスク合意（東部紛争停戦・後に破綻）</div>
                            </div>

                            <div x-show="timelineExpanded" x-collapse class="space-y-1 pt-1">
                                <div class="relative flex items-start sm:items-center py-0.5">
                                    <div class="absolute left-[-16.5px] top-1.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                    <div class="w-16 sm:w-20 font-bold text-blue-500 dark:text-blue-400 text-[11px] shrink-0 pt-0.5 sm:pt-0">2021年秋</div>
                                    <div class="flex-1 text-gray-700 dark:text-gray-200 text-[11px] font-bold ml-1 leading-snug sm:truncate">ロシア軍がウクライナ国境に集結開始</div>
                                    <span class="ml-2 text-[8px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                                </div>
                            </div>
                        </div>
                        <button @click="timelineExpanded = !timelineExpanded" class="mt-1 ml-3 text-[10px] font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <span x-text="timelineExpanded ? '▲ 閉じる' : '▼ もっと見る'"></span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col items-end flex-shrink-0 space-y-1">
                    @if($topic->categories->isNotEmpty())
                        <div class="flex flex-wrap gap-1 justify-end mb-1">
                            @foreach($topic->categories as $category)
                                <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="px-2 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right space-y-0.5">
                        <p>作成者: {{ $topic->user->name }}</p>
                        <p>{{ $topic->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    
                    <div class="pt-1">
                        @if ($topic->isSavedBy(auth()->user()))
                            <form method="POST" action="{{ route('bookmarks.destroy', $topic) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>
                                    保存済み
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('bookmarks.store', $topic) }}">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                    保存する
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div x-data="{ 
                    activeTab: sessionStorage.getItem('activeTab_{{ $topic->id }}') || '{{ request()->has('comment_sort') ? 'comments' : 'info' }}',
                    isModalOpen: false,
                    isAnalysisModalOpen: false 
                 }" 
                 x-init="$watch('activeTab', value => sessionStorage.setItem('activeTab_{{ $topic->id }}', value))" 
                 class="mt-4">
                
                <div class="flex border-b border-gray-300 dark:border-gray-800 mb-3 overflow-x-auto">
                    <button @click="activeTab = 'info'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        情報
                    </button>
                    <button @click="activeTab = 'comments'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        コメント
                    </button>
                    <button @click="activeTab = 'analysis'" 
                            :class="{ 'border-yellow-500 text-gray-900 dark:text-white font-bold': activeTab === 'analysis', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'analysis' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        分析・図解 
                        <span class="ml-1.5 text-[9px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                </div>

                <div x-show="activeTab === 'info'" x-cloak>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $posts->count() }}件の投稿</h3>
                        <div class="flex items-center space-x-2">
                            <form method="GET" action="{{ route('topics.show', $topic) }}" class="flex space-x-2">
                                <select name="category" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1">
                                    <option value="">すべてのメディア</option>
                                    <option value="YouTube" {{ request('category') === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="X" {{ request('category') === 'X' ? 'selected' : '' }}>X</option>
                                    <option value="記事" {{ request('category') === '記事' ? 'selected' : '' }}>記事</option>
                                    <option value="知恵袋" {{ request('category') === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                                    <option value="本" {{ request('category') === '本' ? 'selected' : '' }}>本</option>
                                    <option value="その他" {{ request('category') === 'その他' ? 'selected' : '' }}>その他</option>
                                </select>
                                <select name="sort" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1 hidden sm:block">
                                    <option value="popular" {{ request('sort') === 'popular' || !request('sort') ? 'selected' : '' }}>人気順</option>
                                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                                </select>
                            </form>
                            <button @click="isModalOpen = true" class="bg-white border border-gray-300 hover:bg-gray-50 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 font-bold py-1 px-3 sm:py-1.5 sm:px-4 rounded text-xs sm:text-sm transition-colors flex items-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span class="hidden sm:inline">投稿する</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($posts as $post)
                            <div class="p-3 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm flex flex-col md:flex-row gap-3 transition-colors">
                                <div class="md:w-1/4 flex-shrink-0">
                                    <a href="{{ $post->url }}" target="_blank" class="block group">
                                        @if($post->thumbnail_url)
                                            <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                                                <img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @else
                                            <div class="w-full aspect-video bg-gray-100 dark:bg-[#131314] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700 group-hover:border-gray-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                                <span class="text-xs">No Image</span>
                                            </div>
                                        @endif
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-blue-500 dark:group-hover:text-blue-400 line-clamp-2 leading-tight transition-colors">{{ $post->title ?: 'タイトルを取得できませんでした' }}</h4>
                                    </a>
                                </div>
                                <div class="md:w-3/4 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $post->category }}</span>
                                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name }}</span><br>
                                                <span>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                        </div>
                                        @if ($post->comment)
                                            <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1">{{ $post->comment }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex items-center justify-end gap-3">
                                        @if ($post->user_id === auth()->id())
                                            <a href="{{ route('posts.edit', $post) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">編集</a>
                                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                            </form>
                                            <span class="text-gray-300 dark:text-gray-700">|</span>
                                        @endif
                                        <form method="POST" action="{{ route('likes.store', $post) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 {{ $post->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $post->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" /></svg>
                                                @if($post->likes->count() > 0)
                                                    <span class="text-sm">{{ $post->likes->count() }}</span>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="activeTab === 'comments'" x-cloak>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $comments->count() }}件のコメント</h3>
                        <form method="GET" action="{{ route('topics.show', $topic) }}">
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            <select name="comment_sort" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1">
                                <option value="popular" {{ request('comment_sort') === 'popular' || !request('comment_sort') ? 'selected' : '' }}>人気順</option>
                                <option value="newest" {{ request('comment_sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                <option value="oldest" {{ request('comment_sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                            </select>
                        </form>
                    </div>

                    @if(!$userComment)
                        <div class="p-4 bg-gray-50 dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent mb-6">
                            <form method="POST" action="{{ route('comments.store', $topic) }}">
                                @csrf
                                <textarea name="body" rows="3" class="w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white mb-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required placeholder="このトピックに対する見解を投稿（※1人1件まで・編集は3回まで）"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 dark:bg-[#131314] dark:text-gray-200 border dark:border-gray-700 dark:hover:bg-gray-800 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors">見解を投稿する</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div x-data="{ editing: false }" class="p-4 bg-gray-100 dark:bg-[#1e1f20] border border-gray-200 dark:border-transparent rounded-lg mb-6">
                            <div x-show="!editing">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">✅ あなたの投稿</p>
                                    <span class="text-xs text-gray-500">残り編集: {{ 3 - $userComment->edit_count }}回</span>
                                </div>
                                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap text-sm mb-3">{{ $userComment->body }}</p>
                                <div class="flex justify-end space-x-4 items-center">
                                    <form method="POST" action="{{ route('comments.destroy', $userComment) }}" onsubmit="return confirm('本当に削除しますか？\n※削除すると新しく1件投稿できるようになります。');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition-colors">削除する</button>
                                    </form>
                                    @if($userComment->edit_count < 3)
                                        <button @click="editing = true" class="text-xs font-bold text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">編集する</button>
                                    @endif
                                </div>
                            </div>
                            <div x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('comments.update', $userComment) }}">
                                    @csrf @method('PATCH')
                                    <textarea name="body" rows="3" class="w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white mb-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>{{ $userComment->body }}</textarea>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" @click="editing = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-1 text-xs transition-colors">キャンセル</button>
                                        <button type="submit" class="bg-gray-800 hover:bg-gray-900 dark:bg-[#131314] dark:text-gray-200 border dark:border-gray-700 dark:hover:bg-gray-800 text-white font-bold py-1 px-3 rounded text-xs transition-colors">更新する</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @forelse($comments as $comment)
                            <div class="p-4 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }} @if($comment->edit_count > 0)<span class="ml-1 text-gray-400">(編集済)</span>@endif</span>
                                </div>
                                <p class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ $comment->body }}</p>
                                <div class="mt-3 flex items-center justify-end">
                                    <form method="POST" action="{{ route('comments.like', $comment) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 {{ $comment->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $comment->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" /></svg>
                                            @if($comment->likes_count > 0)<span class="text-sm">{{ $comment->likes_count }}</span>@endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-6 text-sm">まだコメントはありません。</p>
                        @endforelse
                    </div>
                </div>

                <div x-show="activeTab === 'analysis'" x-cloak>
                    @php
                        // このトピックに公開されている分析を取得
                        $topicAnalyses = \App\Models\Analysis::where('topic_id', $topic->id)->where('is_published', true)->latest()->get();
                        // 自分が作成した未公開（下書き）の分析を取得
                        $myAvailableAnalyses = \App\Models\Analysis::where('user_id', auth()->id())->where('is_published', false)->latest()->get();
                    @endphp

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $topicAnalyses->count() }}件の分析・図解</h3>
                        
                        <button @click="isAnalysisModalOpen = true" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 sm:py-1.5 sm:px-4 rounded text-xs sm:text-sm transition-colors flex items-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span class="hidden sm:inline">マイページから投稿</span>
                        </button>
                    </div>

                    @if($topicAnalyses->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-gray-300 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-[#131314]/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mb-1">まだ分析・図解は投稿されていません</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-sm">
                                プレミアムプランに登録すると、マイページで作成した「ロジックツリー」や「総合評価表」をここに公開して、議論を深めることができます。
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topicAnalyses as $analysis)
                                <div class="p-4 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-colors flex flex-col gap-3">
                                    
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($analysis->type === 'tree') <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-blue-200 text-blue-600 dark:border-blue-800 dark:text-blue-400">ロジックツリー</span>
                                            @elseif($analysis->type === 'matrix') <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-purple-200 text-purple-600 dark:border-purple-800 dark:text-purple-400">総合評価表</span>
                                            @elseif($analysis->type === 'swot')
                                            @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                                            <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-green-200 text-green-600 dark:border-green-800 dark:text-green-400">{{ $isPest ? 'PEST分析' : 'SWOT分析' }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $analysis->user->name }}</span><br>
                                            <span>{{ $analysis->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>

                                    <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#131314] p-4 text-sm overflow-hidden w-full flex-1" style="max-height: 400px; -webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%); mask-image: linear-gradient(to bottom, black 80%, transparent 100%);">
                                        
                                        @php $previewData = $analysis->data; @endphp
                                        
                                        @if($analysis->type === 'swot')
                                            <div class="font-bold text-base text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-800">{{ $analysis->title }}</div>
                                        @endif
                                        
                                        @if($analysis->type === 'tree' && !empty($previewData))
                                            @php 
                                                $nodes = isset($previewData['nodes']) ? $previewData['nodes'] : $previewData;
                                                $meta = $previewData['meta'] ?? null;
                                            @endphp
                                            
                                            @if($meta && (!empty($meta['url']) || !empty($meta['description'])))
                                                <div class="mb-4 p-3 bg-white dark:bg-[#1e1f20] rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                                                    <div class="text-[10px] font-bold text-blue-600 dark:text-blue-400 mb-1 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>事前情報</div>
                                                    @if(!empty($meta['description'])) <p class="text-xs text-gray-800 dark:text-gray-300 mb-1.5">{{ $meta['description'] }}</p> @endif
                                                    @if(!empty($meta['url'])) <a href="{{ $meta['url'] }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate block">{{ $meta['url'] }}</a> @endif
                                                </div>
                                            @endif

                                            <div class="space-y-3">
                                                @foreach(array_slice($nodes, 0, 5) as $node)
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
                                                    @foreach(array_slice($previewData['items'], 0, 5) as $item)
                                                        <li class="truncate">{{ $item['itemTitle'] ?? '' }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @elseif($analysis->type === 'swot')
                                            @php
                                                $isPest = isset($previewData['framework']) && $previewData['framework'] === 'PEST';
                                                // 新データ(box1)と旧データ(strengths)の両方に対応する安全設計
                                                $b1 = $previewData['box1'] ?? $previewData['strengths'] ?? [];
                                                $b2 = $previewData['box2'] ?? $previewData['weaknesses'] ?? [];
                                                $b3 = $previewData['box3'] ?? $previewData['opportunities'] ?? [];
                                                $b4 = $previewData['box4'] ?? $previewData['threats'] ?? [];
                                            @endphp
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <span class="font-bold text-blue-500 mb-1 inline-block">{{ $isPest ? 'P (政治)' : 'S (強み)' }}:</span>
                                                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                                                        @forelse(array_slice($b1, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                                    </ul>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-red-500 mb-1 inline-block">{{ $isPest ? 'E (経済)' : 'W (弱み)' }}:</span>
                                                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                                                        @forelse(array_slice($b2, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                                    </ul>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-green-500 mb-1 inline-block">{{ $isPest ? 'S (社会)' : 'O (機会)' }}:</span>
                                                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                                                        @forelse(array_slice($b3, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                                    </ul>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-yellow-500 mb-1 inline-block">{{ $isPest ? 'T (技術)' : 'T (脅威)' }}:</span>
                                                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                                                        @forelse(array_slice($b4, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-1 flex items-center justify-between">
                                        <a href="{{ route('analyses.show', $analysis) }}" class="text-xs font-bold text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                            もっと見る <span class="ml-1 text-[10px]">▶</span>
                                        </a>

                                        <div class="flex items-center gap-3">
                                            @if ($analysis->user_id === auth()->id())
                                                <button type="button" onclick="alert('削除機能は準備中です')" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                            @endif
                                            
                                            <button type="button" class="flex items-center space-x-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" /></svg>
                                                <span class="text-sm">0</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div x-show="isModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" @click.away="isModalOpen = false" class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">エビデンスを投稿</h3>
                                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors"><span class="text-2xl leading-none">&times;</span></button>
                                </div>
                                <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-white dark:bg-[#131314]">
                                    <form method="POST" action="{{ route('posts.store', $topic) }}" id="post-form">
                                        @csrf
                                        <div class="mb-5">
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">参考URL (必須)</label>
                                            <input type="url" name="url" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required placeholder="https://...">
                                        </div>
                                        <div class="mb-5">
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">メディア分類 (必須)</label>
                                            <select name="category" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                                <option value="">選択してください</option>
                                                <option value="YouTube">YouTube</option>
                                                <option value="X">X</option>
                                                <option value="記事">記事</option>
                                                <option value="知恵袋">知恵袋</option>
                                                <option value="本">本</option>
                                                <option value="その他">その他</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">補足・コメント (任意)</label>
                                            <textarea name="comment" rows="4" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="URLに対する補足や、どの部分が参考になるかなどを記入"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3 bg-gray-50 dark:bg-[#1e1f20]">
                                    <button @click="isModalOpen = false" type="button" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-2 px-4 rounded-md text-sm transition-colors">キャンセル</button>
                                    <button type="submit" form="post-form" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">投稿する</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="isAnalysisModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isAnalysisModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isAnalysisModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" @click.away="isAnalysisModalOpen = false" class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        マイページから投稿
                                    </h3>
                                    <button @click="isAnalysisModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors"><span class="text-2xl leading-none">&times;</span></button>
                                </div>

                                <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-white dark:bg-[#131314]">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">保存済みで、まだ他のトピックに公開していない分析・図解の一覧です。</p>
                                    
                                    @if(isset($myAvailableAnalyses) && $myAvailableAnalyses->isEmpty())
                                        <div class="text-center py-6 border border-gray-200 dark:border-gray-800 rounded-lg">
                                            <p class="text-sm text-gray-500 mb-2">公開できる分析・図解がありません。</p>
                                            <a href="{{ route('tools.tree') }}" class="text-xs text-blue-500 hover:underline">ツールを使って新しく作成する</a>
                                        </div>
                                    @else
                                        <div class="space-y-3">
                                            @foreach($myAvailableAnalyses as $analysis)
                                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-[#1e1f20] flex justify-between items-center">
                                                    <div>
                                                        <div class="mb-1">
                                                            @if($analysis->type === 'tree') <span class="text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 px-1.5 py-0.5 rounded">ロジックツリー</span>
                                                            @elseif($analysis->type === 'matrix') <span class="text-[10px] bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 px-1.5 py-0.5 rounded">総合評価表</span>
                                                            @elseif($analysis->type === 'swot') <span class="text-[10px] bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 px-1.5 py-0.5 rounded">SWOT分析</span>
                                                            @endif
                                                        </div>
                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $analysis->title }}</h4>
                                                        <span class="text-[10px] text-gray-400">作成日: {{ $analysis->created_at->format('Y-m-d') }}</span>
                                                    </div>
                                                    
                                                    <form method="POST" action="{{ route('tools.publish', $analysis) }}">
                                                        @csrf
                                                        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                                                            このトピックに投稿
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-4">
                <a href="{{ route('topics.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm transition-colors">
                    &larr; 一覧に戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>