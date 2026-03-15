<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ activeTab: 'posts' }" class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">
                
                <div class="flex border-b border-gray-200 dark:border-gray-800 overflow-x-auto scrollbar-hide">
                    <button @click="activeTab = 'posts'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'posts' }" class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">投稿した情報</button>
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
                    
                    {{-- 投稿した情報 --}}
                    <div x-show="activeTab === 'posts'" x-cloak class="space-y-6">
                        @forelse(auth()->user()->posts()->latest()->get() as $post)
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