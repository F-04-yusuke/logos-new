<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">通知</h2>

            {{-- 未読がある時だけ「全て既読」ボタンを表示 --}}
            @if($notifications->whereNull('read_at')->count())
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs text-blue-500 hover:text-blue-400 font-bold transition-colors">
                        すべて既読にする
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">

            {{-- フラッシュメッセージ --}}
            @if(session('status'))
                <div class="mb-4 text-sm text-green-600 dark:text-green-400 font-bold">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-[#1e1f20] rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-transparent divide-y divide-gray-100 dark:divide-gray-800">

                @forelse($notifications as $notification)
                    {{--
                        既読済みは薄く、未読は背景をわずかに明るくして強調する。
                        クリックすると既読処理 → 関連ページへリダイレクト。
                    --}}
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full text-left flex items-start gap-3 px-4 py-4 transition-colors
                            {{ $notification->isUnread()
                                ? 'bg-blue-50/60 dark:bg-blue-950/20 hover:bg-blue-50 dark:hover:bg-blue-950/30'
                                : 'hover:bg-gray-50 dark:hover:bg-[#131314]' }}">

                            {{-- 左: アクターのアバター + 通知種別アイコンバッジ --}}
                            <div class="relative shrink-0 mt-0.5">
                                @if($notification->actor?->avatar)
                                    <img class="h-9 w-9 rounded-full object-cover"
                                         src="{{ asset('storage/' . $notification->actor->avatar) }}"
                                         alt="{{ $notification->actor->name }}" />
                                @elseif($notification->actor)
                                    <div class="h-9 w-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        <svg aria-hidden="true" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @else
                                    {{-- system通知はLOGOSロゴアイコン --}}
                                    <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center">
                                        <span class="text-white text-[11px] font-black tracking-tight">L</span>
                                    </div>
                                @endif

                                {{-- 通知種別を示す小さなバッジ --}}
                                <span class="absolute -bottom-0.5 -right-0.5 flex items-center justify-center h-4 w-4 rounded-full border-2 border-white dark:border-[#1e1f20]
                                    @if($notification->type === 'new_post')     bg-blue-500
                                    @elseif($notification->type === 'comment_reply') bg-green-500
                                    @elseif($notification->type === 'post_like')     bg-red-500
                                    @else                                            bg-yellow-500
                                    @endif">
                                    @if($notification->type === 'new_post')
                                        <svg aria-hidden="true" class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                                    @elseif($notification->type === 'comment_reply')
                                        <svg aria-hidden="true" class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    @elseif($notification->type === 'post_like')
                                        <svg aria-hidden="true" class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" /></svg>
                                    @else
                                        <svg aria-hidden="true" class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                    @endif
                                </span>
                            </div>

                            {{-- 中央: テキストと時刻 --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] leading-snug
                                    {{ $notification->isUnread()
                                        ? 'text-gray-900 dark:text-gray-100 font-medium'
                                        : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $notification->text }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-gray-400 dark:text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- 右: 未読ドット --}}
                            <div class="shrink-0 flex items-center self-center pl-2">
                                @if($notification->isUnread())
                                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                @else
                                    <span class="h-2 w-2"></span>
                                @endif
                            </div>

                        </button>
                    </form>
                @empty
                    <div class="py-16 text-center">
                        <svg aria-hidden="true" class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">通知はありません</p>
                    </div>
                @endforelse

            </div>

            {{-- ページネーション --}}
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
