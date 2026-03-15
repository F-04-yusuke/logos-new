<div class="flex items-center justify-between mb-3">
    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $comments->count() }}件のコメント</h3>
    <form method="GET" action="{{ route('topics.show', $topic) }}">
        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
        {{-- 【修正】スマホでのタップ領域を広げるため py-1.5 sm:py-1 に変更 --}}
        <select name="comment_sort" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1.5 sm:py-1">
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
            {{-- 【修正】スマホでのタップ領域拡大(py-2 sm:py-1.5) --}}
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 dark:bg-[#131314] dark:text-gray-200 border border-transparent dark:border-gray-700 dark:hover:bg-gray-800 text-white font-bold py-2 px-4 sm:py-1.5 rounded text-sm transition-colors shadow-sm">コメントする</button>
        </div>
    </form>
</div>
@endif

<div class="space-y-2 mt-4">
    @forelse($comments as $comment)
        {{-- 【修正】先ほど作成した共通コンポーネントを呼び出すだけ --}}
        <x-comment-card :comment="$comment" />
    @empty
    <p class="text-center text-gray-500 py-10 text-sm">まだコメントはありません。最初のコメントを投稿しましょう！</p>
    @endforelse
</div>