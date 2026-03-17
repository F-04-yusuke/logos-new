<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: '{{ session('draft_saved') ? 'drafts' : 'posts' }}' }" class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">

                <div class="flex border-b border-gray-200 dark:border-gray-800 overflow-x-auto scrollbar-hide">
                    <button @click="activeTab = 'posts'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'posts' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">投稿した情報</button>
                    {{-- 下書きタブ：件数バッジ付き --}}
                    @php $draftCount = auth()->user()->posts()->where('is_published', false)->count(); @endphp
                    <button @click="activeTab = 'drafts'" :class="{ 'border-yellow-500 text-yellow-600 dark:text-yellow-400 font-bold': activeTab === 'drafts', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'drafts' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center gap-1.5">
                        下書き
                        @if($draftCount > 0)
                        <span class="text-[10px] font-black bg-yellow-500 text-white px-1.5 py-0.5 rounded-full leading-none">{{ $draftCount }}</span>
                        @endif
                    </button>
                    <button @click="activeTab = 'comments'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'comments' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">自分のコメント</button>
                    <button @click="activeTab = 'analyses'" :class="{ 'border-yellow-500 text-yellow-600 dark:text-yellow-500 font-bold': activeTab === 'analyses', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'analyses' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        作成した分析・図解
                        <span class="ml-1 text-[9px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                    <button @click="activeTab = 'topics'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'topics', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'topics' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        作成したトピック
                        <span class="ml-1 text-[9px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                </div>

                <div class="p-4 sm:p-6">
                    
                    {{-- 投稿した情報（公開済みのみ） --}}
                    <div x-show="activeTab === 'posts'" x-cloak class="space-y-6">
                        @forelse(auth()->user()->posts()->where('is_published', true)->latest()->get() as $post)
                            <div class="flex flex-col gap-1.5">
                                <x-post-card :post="$post" />
                                <div class="text-right px-2">
                                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                        🔗 投稿先トピック: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $post->topic->title }}</a>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-10 text-sm">まだ投稿した情報はありません。</p>
                        @endforelse
                    </div>

                    {{-- 下書き一覧 --}}
                    <div x-show="activeTab === 'drafts'" x-cloak class="space-y-4">
                        @forelse(auth()->user()->posts()->where('is_published', false)->with('topic')->latest()->get() as $post)
                            <div class="p-4 bg-white dark:bg-[#131314] rounded-lg border border-yellow-200 dark:border-yellow-900/40 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        {{-- 下書きバッジ + URL --}}
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-[10px] font-black bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-0.5 rounded shrink-0">下書き</span>
                                            <a href="{{ $post->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-500 hover:underline truncate">{{ $post->url }}</a>
                                        </div>
                                        {{-- メディア分類 --}}
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">分類：{{ $post->category }}</span>
                                            <span class="text-[11px] text-gray-400">・</span>
                                            <span class="text-[11px] text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        {{-- コメント --}}
                                        @if($post->comment)
                                        <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2">{{ $post->comment }}</p>
                                        @endif
                                        {{-- 投稿先トピック --}}
                                        <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                            🔗 投稿先: <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 transition-colors">{{ $post->topic->title }}</a>
                                        </div>
                                    </div>
                                    {{-- アクションボタン --}}
                                    <div class="flex items-center gap-3 shrink-0 self-end sm:self-auto">
                                        <a href="{{ route('posts.edit', $post) }}"
                                           class="text-xs font-bold text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 border border-yellow-300 dark:border-yellow-700 hover:border-yellow-400 py-1.5 px-3 rounded-md transition-colors flex items-center gap-1">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            編集・本投稿
                                        </a>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('下書きを削除しますか？');" class="m-0 p-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-bold transition-colors py-1.5 px-2">削除</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-gray-500 text-sm mb-2">下書きはありません。</p>
                                <p class="text-xs text-gray-400">エビデンス投稿時に「下書き保存」を選ぶと、ここに一覧表示されます。</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- 自分のコメント（完全に共通化・連動） --}}
                    <div x-show="activeTab === 'comments'" x-cloak class="space-y-6">
                        @forelse(auth()->user()->comments()->whereNull('parent_id')->with(['replies' => function($q) { $q->oldest(); }])->latest()->get() as $comment)
                            <div class="flex flex-col gap-1.5">
                                {{-- トピック画面と全く同じコンポーネントを呼び出し（背景付きのカードで包む） --}}
                                <div class="bg-white dark:bg-[#1e1f20] px-4 rounded-lg border border-gray-200 dark:border-transparent shadow-sm">
                                    <x-comment-card :comment="$comment" />
                                </div>
                                
                                {{-- トピックリンクを外出ししてカードの下に配置 --}}
                                <div class="text-right px-2">
                                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                        🔗 投稿先トピック: <a href="{{ route('topics.show', $comment->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $comment->topic->title }}</a>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-10 text-sm">まだコメントしていません。</p>
                        @endforelse
                    </div>

                    {{-- 作成した分析・図解 --}}
                    <div x-show="activeTab === 'analyses'" x-cloak class="space-y-6">
                        @forelse(auth()->user()->analyses()->latest()->get() as $analysis)
                            <div class="flex flex-col gap-1.5">
                                {{-- トピック画面と全く同じコンポーネントを呼び出し --}}
                                <x-analysis-card :analysis="$analysis" />
                                
                                {{-- トピックリンクと下書き用の編集ボタンをカードの下に配置 --}}
                                <div class="flex justify-between items-center px-2">
                                    <div>
                                        @if(!$analysis->topic_id)
                                            <a href="{{ route('analyses.edit', $analysis) }}" class="text-xs font-bold bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-500 dark:hover:bg-yellow-900/50 py-1 px-3 rounded transition-colors">
                                                下書きを編集する
                                            </a>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($analysis->topic_id)
                                            <span class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-gray-400">
                                                🔗 公開先トピック: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">{{ $analysis->topic->title }}</a>
                                            </span>
                                        @else
                                            <span class="text-[11px] sm:text-xs font-bold text-yellow-600 dark:text-yellow-500">
                                                未公開（下書き）
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-10 text-sm">まだ作成した分析・図解はありません。</p>
                        @endforelse
                    </div>

                    {{-- 作成したトピック --}}
                    <div x-show="activeTab === 'topics'" x-cloak class="space-y-3">
                        @forelse(auth()->user()->topics()->latest()->get() as $topic)
                            <div class="p-4 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        @if(auth()->user()->avatar)
                                            <img class="h-6 w-6 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" />
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            </div>
                                        @endif
                                        <div class="flex items-baseline gap-2">
                                            <span class="font-bold text-[13px] text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $topic->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('topics.show', $topic) }}" class="font-bold text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $topic->title }}</a>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <a href="{{ route('topics.edit', $topic) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold transition-colors">編集</a>
                                    <span class="text-gray-300 dark:text-gray-700">|</span>
                                    <form method="POST" action="{{ route('topics.destroy', $topic) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-bold transition-colors">削除</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-10 text-sm">まだ作成したトピックはありません。</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>