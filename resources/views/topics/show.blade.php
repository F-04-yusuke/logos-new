<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $topic->title }}
            </h2>
            
            @if ($topic->isSavedBy(auth()->user()))
                <form method="POST" action="{{ route('bookmarks.destroy', $topic) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded text-sm flex items-center transition-colors">
                        <span class="mr-1">🔖</span> 保存済み（解除）
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('bookmarks.store', $topic) }}">
                    @csrf
                    <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-800 border border-blue-300 font-bold py-2 px-4 rounded text-sm flex items-center transition-colors">
                        <span class="mr-1">🔖</span> トピックを保存
                    </button>
                </form>
            @endif
            </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    
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

                    @if($topic->categories->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach($topic->categories as $category)
                                <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="inline-block px-3 py-1 text-sm font-semibold rounded bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:bg-[#131314] dark:text-indigo-300 dark:hover:bg-gray-800 border border-indigo-200 dark:border-gray-800 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <p class="whitespace-pre-wrap">{{ $topic->content }}</p>

                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400 flex justify-between">
                        <span>作成者: {{ $topic->user->name }}</span>
                        <span>作成日時: {{ $topic->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    <div x-data="{ activeTab: sessionStorage.getItem('activeTab_{{ $topic->id }}') || '{{ request()->has('comment_sort') ? 'comments' : 'info' }}' }" 
                        x-init="$watch('activeTab', value => sessionStorage.setItem('activeTab_{{ $topic->id }}', value))" 
                        class="mt-8">
                        
                        <div class="flex border-b border-gray-200 dark:border-gray-800 mb-6">
                            <button @click="activeTab = 'info'" 
                                    :class="{ 'border-blue-500 text-blue-500 dark:text-blue-400 font-bold': activeTab === 'info', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'info' }" 
                                    class="py-2 px-6 border-b-2 text-sm sm:text-base transition-colors focus:outline-none">
                                📄 情報タブ（{{ $posts->count() }}件）
                            </button>
                            <button @click="activeTab = 'comments'" 
                                    :class="{ 'border-blue-500 text-blue-500 dark:text-blue-400 font-bold': activeTab === 'comments', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'comments' }" 
                                    class="py-2 px-6 border-b-2 text-sm sm:text-base transition-colors focus:outline-none">
                                💬 コメントタブ（{{ $comments->count() }}件）
                            </button>
                        </div>

                        <div x-show="activeTab === 'info'" x-cloak>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">エビデンス一覧</h3>
                                
                                <form method="GET" action="{{ route('topics.show', $topic) }}" class="flex space-x-2">
                                    <select name="category" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#131314] dark:border-gray-800 dark:text-white">
                                        <option value="">すべてのメディア</option>
                                        <option value="YouTube" {{ request('category') === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                        <option value="X" {{ request('category') === 'X' ? 'selected' : '' }}>X</option>
                                        <option value="記事" {{ request('category') === '記事' ? 'selected' : '' }}>記事</option>
                                        <option value="知恵袋" {{ request('category') === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                                        <option value="本" {{ request('category') === '本' ? 'selected' : '' }}>本</option>
                                        <option value="その他" {{ request('category') === 'その他' ? 'selected' : '' }}>その他</option>
                                    </select>

                                    <select name="sort" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#131314] dark:border-gray-800 dark:text-white">
                                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>人気順</option>
                                    </select>
                                </form>
                            </div>

                            <div class="space-y-4">
                                @foreach ($posts as $post)
                                    <div class="p-4 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col md:flex-row gap-5 transition-colors">
                                        
                                        <div class="md:w-1/3 flex-shrink-0">
                                            <a href="{{ $post->url }}" target="_blank" class="block group">
                                                @if($post->thumbnail_url)
                                                    <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                                                        <img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    </div>
                                                @else
                                                    <div class="w-full aspect-video bg-gray-100 dark:bg-[#1e1f20] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700 group-hover:border-blue-500 transition-colors">
                                                        <span class="text-3xl mb-1">🔗</span>
                                                        <span class="text-xs">No Image</span>
                                                    </div>
                                                @endif
                                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-blue-500 dark:group-hover:text-blue-400 line-clamp-2 leading-tight transition-colors">
                                                    {{ $post->title ?: 'タイトルを取得できませんでした' }}
                                                </h4>
                                                <span class="text-xs text-gray-400 mt-1 block truncate">
                                                    {{ parse_url($post->url, PHP_URL_HOST) ?? $post->url }}
                                                </span>
                                            </a>
                                        </div>

                                        <div class="md:w-2/3 flex flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-indigo-50 text-indigo-700 border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800/50">
                                                        {{ $post->category }}
                                                    </span>
                                                    <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name }}</span><br>
                                                        <span>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                </div>

                                                @if ($post->comment)
                                                    <div class="text-gray-800 dark:text-gray-200 text-sm whitespace-pre-wrap bg-gray-50 dark:bg-[#1e1f20] p-3 rounded-md border border-gray-100 dark:border-transparent">
                                                        {{ $post->comment }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-4 flex items-center justify-end gap-3">
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
                                                    <button type="submit" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-full border transition-all duration-200 {{ $post->isLikedBy(auth()->user()) ? 'bg-pink-50 border-pink-200 text-pink-600 dark:bg-pink-900/20 dark:border-pink-800 dark:text-pink-400 font-bold' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:hover:border-gray-600' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : 'fill-none stroke-current' }}" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                        </svg>
                                                        <span class="text-sm">参考になった</span>
                                                        @if($post->likes->count() > 0)
                                                            <span class="text-sm ml-1">({{ $post->likes->count() }})</span>
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 p-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">新規投稿を追加</h3>
                                <form method="POST" action="{{ route('posts.store', $topic) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">参考URL（必須）</label>
                                        <input type="url" name="url" id="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#1e1f20] dark:border-gray-800 dark:text-white" required placeholder="https://...">
                                    </div>
                                    <div class="mb-4">
                                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分類（必須）</label>
                                        <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#1e1f20] dark:border-gray-800 dark:text-white" required>
                                            <option value="">選択してください</option>
                                            <option value="YouTube">YouTube</option>
                                            <option value="X">X</option>
                                            <option value="記事">記事</option>
                                            <option value="知恵袋">知恵袋</option>
                                            <option value="本">本</option>
                                            <option value="その他">その他</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">コメント（任意）</label>
                                        <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#1e1f20] dark:border-gray-800 dark:text-white" placeholder="URLに対するあなたの意見や補足を記入"></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors">投稿する</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div x-show="activeTab === 'comments'" x-cloak>
                            
                            @if(!$userComment)
                                <div class="p-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 mb-8">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">あなたの見解を投稿する</h3>
                                    <p class="text-sm text-red-500 dark:text-red-400 font-bold mb-4">※このトピックに対して見解を投稿できるのは1人1件までです。（編集は3回まで）</p>
                                    
                                    <form method="POST" action="{{ route('comments.store', $topic) }}">
                                        @csrf
                                        <textarea name="body" rows="4" class="w-full rounded-md border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white mb-3" required placeholder="このトピックに関するあなたの考えや見解を書いてください..."></textarea>
                                        <div class="flex justify-end">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition-colors">見解を投稿する</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div x-data="{ editing: false }" class="p-5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg mb-8">
                                    
                                    <div x-show="!editing">
                                        <div class="flex justify-between items-center mb-2">
                                            <p class="text-sm font-bold text-indigo-800 dark:text-indigo-300">✅ あなたの見解は投稿済みです</p>
                                            <span class="text-xs font-bold {{ (3 - $userComment->edit_count) === 0 ? 'text-red-500' : 'text-gray-500' }}">
                                                編集残り回数: {{ 3 - $userComment->edit_count }}回
                                            </span>
                                        </div>
                                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap text-sm mb-4">{{ $userComment->body }}</p>
                                        
                                        <div class="flex justify-end space-x-4 items-center">
                                            <form method="POST" action="{{ route('comments.destroy', $userComment) }}" onsubmit="return confirm('本当に削除しますか？\n※削除すると、また新しく1件投稿できるようになります。');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除する</button>
                                            </form>
                                            
                                            @if($userComment->edit_count < 3)
                                                <button @click="editing = true" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                                    編集する
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div x-show="editing" x-cloak>
                                        <form method="POST" action="{{ route('comments.update', $userComment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <p class="text-sm text-indigo-800 dark:text-indigo-300 font-bold mb-2">見解を編集する（残り{{ 3 - $userComment->edit_count }}回）</p>
                                            <textarea name="body" rows="4" class="w-full rounded-md border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white mb-3" required>{{ $userComment->body }}</textarea>
                                            <div class="flex justify-end space-x-3">
                                                <button type="button" @click="editing = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1.5 px-4 rounded text-sm transition-colors">キャンセル</button>
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors">更新する</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">みんなの見解</h3>
                                
                                <form method="GET" action="{{ route('topics.show', $topic) }}">
                                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                                    
                                    <select name="comment_sort" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#131314] dark:border-gray-800 dark:text-white">
                                        <option value="newest" {{ request('comment_sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                        <option value="oldest" {{ request('comment_sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                                        <option value="popular" {{ request('comment_sort') === 'popular' ? 'selected' : '' }}>人気順</option>
                                    </select>
                                </form>
                            </div>

                            <div class="space-y-4">
                                @forelse($comments as $comment)
                                    <div class="p-5 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500">
                                                {{ $comment->created_at->format('Y-m-d H:i') }}
                                                @if($comment->edit_count > 0)
                                                    <span class="ml-1 text-gray-400">(編集済み)</span>
                                                @endif
                                            </span>
                                        </div>
                                        <p class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ $comment->body }}</p>
                                        
                                        <div class="mt-4 flex items-center justify-end">
                                            <form method="POST" action="{{ route('comments.like', $comment) }}">
                                                @csrf
                                                <button type="submit" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-full border transition-all duration-200 {{ $comment->isLikedBy(auth()->user()) ? 'bg-pink-50 border-pink-200 text-pink-600 dark:bg-pink-900/20 dark:border-pink-800 dark:text-pink-400 font-bold' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:hover:border-gray-600' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $comment->isLikedBy(auth()->user()) ? 'fill-current' : 'fill-none stroke-current' }}" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    <span class="text-sm">参考になった</span>
                                                    @if($comment->likes_count > 0)
                                                        <span class="text-sm ml-1">({{ $comment->likes_count }})</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 py-6">まだコメントはありません。</p>
                                @endforelse
                            </div>

                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('topics.index') }}" class="text-blue-500 hover:text-blue-400 underline transition-colors">
                            &larr; トピック一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>