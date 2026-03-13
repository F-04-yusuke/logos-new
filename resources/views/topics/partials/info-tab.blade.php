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
        <div class="md:w-1/4 flex-shrink-0">
            <a href="{{ $post->url }}" target="_blank" class="block group">
                @if($post->thumbnail_url)
                <div class="w-full aspect-video rounded-md overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                    <img src="{{ $post->thumbnail_url }}" alt="サムネイル" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="w-full aspect-video bg-gray-100 dark:bg-[#131314] rounded-md mb-2 flex flex-col items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700 group-hover:border-gray-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
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

                @if ($post->supplement)
                <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800/50 text-sm">
                    <span class="font-bold text-blue-600 dark:text-blue-400 text-[10px] block mb-1">✅ 投稿者からの補足</span>
                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $post->supplement }}</p>
                </div>
            @elseif ($post->user_id === auth()->id())
                <div x-data="{ openSupplement: false }" class="mt-2">
                    <button @click="openSupplement = !openSupplement" x-show="!openSupplement" type="button" class="text-[11px] text-blue-500 hover:text-blue-700 font-bold transition-colors">
                        ＋ 補足を追加する（※1回のみ）
                    </button>
                    <form x-show="openSupplement" x-cloak method="POST" action="{{ route('posts.supplement', $post) }}" class="mt-2 p-3 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
                        @csrf
                        <textarea name="supplement" rows="2" class="w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-[#1e1f20] dark:text-white mb-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required placeholder="URLに対する追加の補足や、時間の経過による状況の変化などを入力してください（※後から編集はできません）"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="openSupplement = false" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold">キャンセル</button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1.5 px-4 rounded transition-colors">補足を投稿</button>
                        </div>
                    </form>
                </div>
            @endif

            </div>
            <div class="mt-3 flex items-center justify-end gap-3">
                @if ($post->user_id === auth()->id())
                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                </form>
                <span class="text-gray-300 dark:text-gray-700">|</span>
                @endif
                <form method="POST" action="{{ route('likes.store', $post) }}" class="m-0 p-0">
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