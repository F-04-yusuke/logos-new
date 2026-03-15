@props(['comment'])

<div x-data="{ openReply: false, openReplies: false }" class="flex gap-4 items-start py-4 border-b border-gray-100 dark:border-gray-800/60">

    <div class="shrink-0 mt-1">
        @if($comment->user->avatar)
        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}のアイコン" />
        @else
        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
            <svg aria-hidden="true" class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2 mb-0.5">
            <span class="font-bold text-[13px] text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
            <span class="text-[11px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
        </div>

        <p class="text-[14px] text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $comment->body }}</p>

        <div class="mt-2 flex items-center gap-4">
            <form method="POST" action="{{ route('comments.like', $comment) }}" class="m-0 p-0">
                @csrf
                <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 py-1 pr-2 {{ $comment->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    <span class="sr-only">いいね</span>
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="{{ $comment->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                    </svg>
                    @if($comment->likes_count > 0)<span class="text-xs sm:text-sm" aria-hidden="true">{{ $comment->likes_count }}</span>@endif
                </button>
            </form>

            <button @click="openReply = !openReply" type="button" class="text-[12px] font-bold text-gray-500 hover:text-gray-900 dark:hover:text-gray-200 transition-colors py-1 px-2 -ml-2">
                {{ $comment->user_id === auth()->id() ? '補足を追加する' : '返信する' }}
            </button>

            @if ($comment->user_id === auth()->id())
            <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('本当に削除しますか？\n※返信がついている場合、返信もすべて削除されます。');" class="m-0 p-0">
                @csrf @method('DELETE')
                <button type="submit" class="text-[12px] text-red-400 hover:text-red-600 transition-colors py-1 px-2">削除</button>
            </form>
            @endif
        </div>

        <form x-show="openReply" x-cloak method="POST" action="{{ route('comments.reply', $comment) }}" class="mt-3">
            @csrf
            <div class="flex flex-col items-end gap-2">
                <textarea name="body" rows="1" class="w-full text-[13px] border-0 border-b border-gray-300 dark:border-gray-600 bg-transparent dark:text-white focus:ring-0 focus:border-blue-500 resize-none overflow-hidden py-1" required placeholder="{{ $comment->user_id === auth()->id() ? '追加の補足を記入...' : '返信を追加...' }}" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                <div class="flex gap-2 mt-1">
                    <button type="button" @click="openReply = false" class="text-xs text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 font-bold px-3 py-2 sm:py-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">キャンセル</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 sm:py-1.5 px-4 rounded-full transition-colors">投稿</button>
                </div>
            </div>
        </form>

        @if($comment->replies->isNotEmpty())
        <div class="mt-1">
            <button @click="openReplies = !openReplies" class="flex items-center gap-2 text-[13px] font-bold text-[#3ea6ff] hover:bg-blue-50 dark:hover:bg-[#263850] px-3 py-2 sm:py-1.5 -ml-3 rounded-full transition-colors">
                <svg aria-hidden="true" class="w-4 h-4 transition-transform duration-200" :class="openReplies ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
                <span x-text="openReplies ? '返信を隠す' : '{{ $comment->replies->count() }}件の返信'"></span>
            </button>

            <div x-show="openReplies" x-cloak class="mt-3 space-y-4">
                @foreach($comment->replies as $reply)
                <div class="flex gap-3 items-start">
                    <div class="shrink-0 mt-0.5">
                        @if($reply->user->avatar)
                        <img class="h-7 w-7 rounded-full object-cover" src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}のアイコン" />
                        @else
                        <div class="h-7 w-7 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                            <svg aria-hidden="true" class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2">
                            <span class="font-bold text-[12px] text-gray-900 dark:text-gray-100">{{ $reply->user->name }}</span>
                            <span class="text-[11px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[13px] text-gray-800 dark:text-gray-200 mt-0.5 whitespace-pre-wrap leading-relaxed">{{ $reply->body }}</p>

                        <div class="mt-1 flex items-center gap-3">
                            <form method="POST" action="{{ route('comments.like', $reply) }}" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="flex items-center space-x-1 transition-colors duration-200 py-1 pr-2 {{ $reply->isLikedBy(auth()->user()) ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                                    <span class="sr-only">いいね</span>
                                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="{{ $reply->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                                    </svg>
                                    @if($reply->likes_count > 0)<span class="text-[11px] sm:text-xs" aria-hidden="true">{{ $reply->likes_count }}</span>@endif
                                </button>
                            </form>

                            @if ($reply->user_id === auth()->id())
                            <form method="POST" action="{{ route('comments.destroy', $reply) }}" onsubmit="return confirm('削除しますか？');" class="m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] text-red-400 hover:text-red-600 transition-colors py-1 px-2">削除</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>