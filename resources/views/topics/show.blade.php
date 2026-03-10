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
                    <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $topic->content }}</p>
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
                    isModalOpen: false 
                 }" 
                 x-init="$watch('activeTab', value => sessionStorage.setItem('activeTab_{{ $topic->id }}', value))" 
                 class="mt-4">
                
                <div class="flex border-b border-gray-300 dark:border-gray-800 mb-3">
                    <button @click="activeTab = 'info'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none">
                        情報
                    </button>
                    <button @click="activeTab = 'comments'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none">
                        コメント
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden sm:inline">投稿する</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($posts as $post)
                            <div class="p-3 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm flex flex-col md:flex-row gap-3 transition-colors">
                                
                                <div class="md:w-1/3 flex-shrink-0">
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
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-blue-500 dark:group-hover:text-blue-400 line-clamp-2 leading-tight transition-colors">
                                            {{ $post->title ?: 'タイトルを取得できませんでした' }}
                                        </h4>
                                    </a>
                                </div>

                                <div class="md:w-2/3 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                                {{ $post->category }}
                                            </span>
                                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name }}</span><br>
                                                <span>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                        </div>

                                        @if ($post->comment)
                                            <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1">
                                                {{ $post->comment }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-end gap-3">
                                        @if ($post->user_id === auth()->id())
                                            <a href="{{ route('posts.edit', $post) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">編集</a>
                                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                            </form>
                                            <span class="text-gray-300 dark:text-gray-700">|</span>
                                        @endif

                                        <form method="POST" action="{{ route('likes.store', $post) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 {{ $post->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $post->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                                                </svg>
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
                                    <span class="text-xs text-gray-500">
                                        残り編集: {{ 3 - $userComment->edit_count }}回
                                    </span>
                                </div>
                                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap text-sm mb-3">{{ $userComment->body }}</p>
                                
                                <div class="flex justify-end space-x-4 items-center">
                                    <form method="POST" action="{{ route('comments.destroy', $userComment) }}" onsubmit="return confirm('本当に削除しますか？\n※削除すると新しく1件投稿できるようになります。');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition-colors">削除する</button>
                                    </form>
                                    
                                    @if($userComment->edit_count < 3)
                                        <button @click="editing = true" class="text-xs font-bold text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                            編集する
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('comments.update', $userComment) }}">
                                    @csrf
                                    @method('PATCH')
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
                                    <span class="text-xs text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                        @if($comment->edit_count > 0)
                                            <span class="ml-1 text-gray-400">(編集済)</span>
                                        @endif
                                    </span>
                                </div>
                                <p class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ $comment->body }}</p>
                                
                                <div class="mt-3 flex items-center justify-end">
                                    <form method="POST" action="{{ route('comments.like', $comment) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 {{ $comment->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $comment->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                                            </svg>
                                            @if($comment->likes_count > 0)
                                                <span class="text-sm">{{ $comment->likes_count }}</span>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-6 text-sm">まだコメントはありません。</p>
                        @endforelse
                    </div>
                </div>

                <div x-show="isModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isModalOpen" 
                         x-transition.opacity 
                         class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>

                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isModalOpen"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                                 @click.away="isModalOpen = false"
                                 class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">エビデンスを投稿</h3>
                                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors">
                                        <span class="text-2xl leading-none">&times;</span>
                                    </button>
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

            </div>
            
            <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-4">
                <a href="{{ route('topics.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm transition-colors">
                    &larr; 一覧に戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>