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
        <textarea name="body" rows="3" class="w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white mb-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required placeholder="このトピックに対するコメント（※1人1件まで）"></textarea>
        <div class="flex justify-end">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 dark:bg-[#131314] dark:text-gray-200 border border-transparent dark:border-gray-700 dark:hover:bg-gray-800 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors shadow-sm">コメントする</button>
        </div>
    </form>
</div>
@endif

<div class="space-y-3">
    @forelse($comments as $comment)
    <div class="p-4 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-transparent shadow-sm">
        <div class="flex justify-between items-center mb-2">
            <span class="font-bold text-sm text-gray-900 dark:text-gray-100 flex items-center">
                {{ $comment->user->name }}
                @if($comment->user_id === auth()->id())
                    <span class="ml-2 text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-1.5 py-0.5 rounded font-normal">✅ あなたの投稿</span>
                @endif
            </span>
            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        
        <p class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ $comment->body }}</p>
        
        <div class="mt-3 flex items-center justify-end gap-3">
            @if ($comment->user_id === auth()->id())
                <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('本当に削除しますか？\n※削除すると新しく1件投稿できるようになります。');" class="m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                </form>
                <span class="text-gray-300 dark:text-gray-700">|</span>
            @endif

            <form method="POST" action="{{ route('comments.like', $comment) }}" class="m-0 p-0">
                @csrf
                <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 {{ $comment->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $comment->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                    </svg>
                    @if($comment->likes_count > 0)<span class="text-sm">{{ $comment->likes_count }}</span>@endif
                </button>
            </form>
        </div>
    </div>
    @empty
    <p class="text-center text-gray-500 py-6 text-sm">まだコメントはありません。</p>
    @endforelse
</div>