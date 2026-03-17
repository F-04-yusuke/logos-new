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

            {{-- 【修正】PCで縦に潰れるバグを修正。骨組みを一番最初の完璧だった状態に戻しました --}}
            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-2">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $topic->title }}</h2>

                    <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300 mb-3">{{ $topic->content }}</p>

                    <div x-data="{ timelineExpanded: false }" class="mt-1 mb-1">
                        {{-- 【修正】スマホでボタンが近すぎる問題を解消するため、gap-2とflex-wrapを追加 --}}
                        <div class="flex flex-wrap items-center justify-between gap-6 mb-2">
                            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 flex items-center shrink-0">
                                <span class="mr-1" aria-hidden="true">⏳</span> 前提となる時系列
                            </h3>

                            @if($topic->user_id === auth()->id())
                                @if(!$topic->timeline)
                                    <form method="POST" action="{{ route('topics.timeline', $topic) }}" class="m-0 p-0" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '生成中...';">
                                        @csrf
                                        <button type="submit" class="text-[10px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 font-bold py-0.5 px-2 rounded transition-colors flex items-center">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            AIで自動生成する
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('topics.timeline_update', $topic) }}" class="m-0 p-0" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '更新中...';">
                                        @csrf
                                        <button type="submit" class="text-[10px] bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/50 font-bold py-0.5 px-2 rounded transition-colors flex items-center" title="投稿されたエビデンスを元に時系列を最新化します">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            最新投稿からAI更新
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        @if($topic->timeline)
                        <div class="border-l-[1.5px] border-gray-300 dark:border-gray-700 ml-1.5 pl-3">
                            @foreach(array_slice($topic->timeline, 0, 3) as $index => $item)
                            <div class="relative flex items-start sm:items-center py-0.5 sm:py-1">
                                <div class="absolute left-[-16.5px] top-2.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                                <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">{{ $item['date'] ?? '' }}</div>
                                <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">{{ $item['event'] ?? '' }}</div>
                                @if(!isset($item['is_ai']) || filter_var($item['is_ai'], FILTER_VALIDATE_BOOLEAN))
                                <span class="ml-2 text-[9px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                                @endif
                            </div>
                            @endforeach

                            @if(count($topic->timeline) > 3)
                            <div x-show="timelineExpanded" x-cloak x-collapse>
                                @foreach(array_slice($topic->timeline, 3) as $item)
                                <div class="relative flex items-start sm:items-center py-0.5 sm:py-1">
                                    <div class="absolute left-[-16.5px] top-2.5 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                                    <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">{{ $item['date'] ?? '' }}</div>
                                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">{{ $item['event'] ?? '' }}</div>
                                    @if(!isset($item['is_ai']) || filter_var($item['is_ai'], FILTER_VALIDATE_BOOLEAN))
                                    <span class="ml-2 text-[9px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @if(count($topic->timeline) > 3)
                        <button @click="timelineExpanded = !timelineExpanded" class="mt-1 ml-3 text-xs font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <span x-text="timelineExpanded ? '▲ 閉じる' : '▼ もっと見る'"></span>
                        </button>
                        @endif
                        @elseif($topic->user_id !== auth()->id())
                        @endif
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

                    <div class="pt-1 flex items-center justify-end gap-3">
                        @if ($topic->user_id === auth()->id())
                        <a href="{{ route('topics.edit', $topic) }}" class="text-xs font-bold text-gray-400 hover:text-blue-500 transition-colors flex items-center">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            編集する
                        </a>
                        <span class="text-gray-300 dark:text-gray-700">|</span>
                        @endif

                        @if ($topic->isSavedBy(auth()->user()))
                        <form method="POST" action="{{ route('bookmarks.destroy', $topic) }}" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors flex items-center">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                </svg>
                                保存済み
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('bookmarks.store', $topic) }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors flex items-center">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                保存する
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            {{-- （ここまで） --}}

            <div x-data="{
                    activeTab: sessionStorage.getItem('activeTab_{{ $topic->id }}') || '{{ request()->has('comment_sort') ? 'comments' : 'info' }}',
                    isModalOpen: false,
                    isAnalysisModalOpen: false,
                    isDraft: false
                 }"
                x-init="
                    $watch('activeTab', value => sessionStorage.setItem('activeTab_{{ $topic->id }}', value));
                    $watch('isModalOpen', val => { if (!val) $nextTick(() => { const f = document.getElementById('post-form'); if (f) f.reset(); }) })
                "
                class="mt-4">

                <div class="flex border-b border-gray-300 dark:border-gray-800 mb-4 overflow-x-auto scrollbar-hide">
                    <button @click="activeTab = 'info'"
                        :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }"
                        class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        情報
                    </button>
                    <button @click="activeTab = 'comments'"
                        :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }"
                        class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        コメント
                    </button>
                    <button @click="activeTab = 'analysis'"
                        :class="{ 'border-yellow-500 text-gray-900 dark:text-white font-bold': activeTab === 'analysis', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'analysis' }"
                        class="py-3 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        分析・図解
                        <span class="ml-1.5 text-[9px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1.5 py-0.5 rounded font-black tracking-wider">PRO</span>
                    </button>
                </div>

                <div class="mt-4">

                    <div x-show="activeTab === 'info'" x-cloak>
                        @include('topics.partials.info-tab')
                    </div>

                    <div x-show="activeTab === 'comments'" x-cloak>
                        @include('topics.partials.comments-tab')
                    </div>

                    <div x-show="activeTab === 'analysis'" x-cloak>
                        @include('topics.partials.analysis-tab')
                    </div>

                </div>

                {{-- エビデンス投稿モーダル --}}
                <div x-show="isModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" @click.away="isModalOpen = false" class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">エビデンスを投稿</h3>
                                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 focus:outline-none transition-colors">
                                        <span class="sr-only">閉じる</span>
                                        <span class="text-2xl leading-none" aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-white dark:bg-[#131314]">
                                    <form method="POST" action="{{ route('posts.store', $topic) }}" id="post-form">
                                        @csrf
                                        {{-- 下書き(0) or 公開(1) を Alpine.js で動的に切り替える --}}
                                        <input type="hidden" name="is_published" :value="isDraft ? '0' : '1'">
                                        <div class="mb-5">
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">参考URL (必須)</label>
                                            <input type="url" name="url" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-base sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-3 sm:py-2" required placeholder="https://...">
                                        </div>
                                        <div class="mb-5">
                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">メディア分類 (必須)</label>
                                            <select name="category" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-base sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-3 sm:py-2" required>
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
                                            <textarea name="comment" rows="4" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-base sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-3 sm:py-2" placeholder="URLに対する補足や、どの部分が参考になるかなどを記入"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center gap-3 bg-gray-50 dark:bg-[#1e1f20]">
                                    <button @click="isModalOpen = false" type="button" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-3 px-4 sm:py-2 rounded-md text-sm transition-colors">キャンセル</button>
                                    <div class="flex items-center gap-2">
                                        {{-- 下書きとして保存ボタン --}}
                                        <button type="button"
                                            @click="isDraft = true; $nextTick(() => document.getElementById('post-form').submit())"
                                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 font-bold py-3 px-4 sm:py-2 rounded-md text-sm transition-colors flex items-center gap-1">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            </svg>
                                            下書き保存
                                        </button>
                                        {{-- 本投稿ボタン --}}
                                        <button type="button"
                                            @click="isDraft = false; $nextTick(() => document.getElementById('post-form').submit())"
                                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-3 px-6 sm:py-2 rounded-md text-sm transition-colors">
                                            投稿する
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 分析投稿モーダル --}}
                <div x-show="isAnalysisModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isAnalysisModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            
                            <div x-show="isAnalysisModalOpen" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" 
                                 @click.away="isAnalysisModalOpen = false" 
                                 class="relative transform overflow-hidden bg-white dark:bg-[#1e1f20] rounded-t-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700 text-left shadow-2xl transition-all w-full sm:my-8 sm:w-full sm:max-w-2xl p-6">
                                
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">分析・図解の投稿</h3>
                                    <button @click="isAnalysisModalOpen = false" class="text-gray-400 hover:text-gray-500 p-2 focus:outline-none">
                                        <span class="sr-only">閉じる</span>
                                        <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div x-data="{ uploadTab: 'select' }">
                                    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4 overflow-x-auto scrollbar-hide">
                                        <button @click="uploadTab = 'select'" :class="uploadTab === 'select' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400'" class="py-3 px-4 text-sm transition-colors whitespace-nowrap">作成済みツールから選択</button>
                                        <button @click="uploadTab = 'upload'" :class="uploadTab === 'upload' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400'" class="py-3 px-4 text-sm transition-colors flex items-center whitespace-nowrap">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                            オリジナル画像のアップロード
                                        </button>
                                    </div>

                                    <div x-show="uploadTab === 'select'">
                                        @if(auth()->user()->analyses()->whereNull('topic_id')->count() === 0)
                                            <div class="text-center py-8">
                                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">マイページに公開可能な分析データがありません。</p>
                                                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline text-sm font-bold py-2 px-4 rounded-md bg-blue-50 dark:bg-blue-900/20 inline-block">マイページで新しく作成する</a>
                                            </div>
                                        @else
                                            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                                @foreach(auth()->user()->analyses()->whereNull('topic_id')->get() as $analysis)
                                                    <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 bg-gray-50 dark:bg-[#131314]">
                                                        <div class="flex items-center">
                                                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 rounded mr-2 shrink-0">{{ $analysis->type === 'tree' ? 'ロジックツリー' : ($analysis->type === 'matrix' ? '評価表' : 'SWOT') }}</span>
                                                            <span class="font-bold text-gray-800 dark:text-gray-200 text-sm line-clamp-1">{{ $analysis->title }}</span>
                                                        </div>
                                                        <form method="POST" action="{{ route('tools.publish', $analysis) }}" class="m-0 p-0 sm:shrink-0 w-full sm:w-auto">
                                                            @csrf
                                                            <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                                                            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 sm:py-1.5 px-4 rounded shadow-sm transition-colors">
                                                                このトピックに投稿
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div x-show="uploadTab === 'upload'" x-cloak>
                                        <form method="POST" action="{{ route('topics.analyses.image', $topic) }}" enctype="multipart/form-data" class="bg-gray-50 dark:bg-[#131314] p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">図解のタイトル</label>
                                                <input type="text" name="title" required class="w-full text-base sm:text-sm rounded border-gray-300 dark:border-gray-700 dark:bg-[#1e1f20] dark:text-white py-3 sm:py-2" placeholder="例：〇〇問題のステークホルダーマップ">
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">画像ファイルを選択 (JPG, PNG)</label>
                                                <input type="file" name="image" accept="image/*" required class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 dark:file:bg-[#1e1f20] dark:file:text-blue-400 dark:hover:file:bg-gray-800 cursor-pointer">
                                                <p class="text-[11px] sm:text-xs text-gray-400 mt-2">※ファイルサイズは最大5MBまで。オリジナルで作成した図解やグラフのみアップロード可能です。</p>
                                            </div>
                                            <div class="flex justify-end pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3 sm:py-2 px-6 rounded transition-colors">アップロードして公開</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-6 pb-4">
                <a href="{{ route('topics.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold text-sm transition-colors py-2 px-4 -ml-4 rounded-md hover:bg-gray-100 dark:hover:bg-[#1e1f20]">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    一覧に戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>