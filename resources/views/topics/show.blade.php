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
                    
                    <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300 mb-3">{{ $topic->content }}</p>

                    <div x-data="{ timelineExpanded: false }" class="mt-1 mb-1">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1.5 flex items-center">
                            <span class="mr-1">⏳</span> 前提となる時系列
                        </h3>
                        
                        <div class="border-l-[1.5px] border-gray-300 dark:border-gray-700 ml-1.5 pl-3">
                            <div class="relative flex items-start sm:items-center">
                                <div class="absolute left-[-16.5px] top-2 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">2014年2月</div>
                                <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">マイダン革命（親露政権崩壊）</div>
                                <span class="ml-2 text-[10px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                            </div>
                            <div class="relative flex items-start sm:items-center">
                                <div class="absolute left-[-16.5px] top-2 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">2014年3月</div>
                                <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">ロシアによるクリミア併合</div>
                                <span class="ml-2 text-[10px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                            </div>
                            <div class="relative flex items-start sm:items-center">
                                <div class="absolute left-[-16.5px] top-2 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                                <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">2014年9月</div>
                                <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">ミンスク合意（東部紛争停戦・後に破綻）</div>
                            </div>

                            <div x-show="timelineExpanded" x-collapse>
                                <div class="relative flex items-start sm:items-center">
                                    <div class="absolute left-[-16.5px] top-2 sm:top-1/2 sm:-translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                                    <div class="w-20 sm:w-24 text-sm text-gray-700 dark:text-gray-300 shrink-0">2021年秋</div>
                                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-300 sm:truncate">ロシア軍がウクライナ国境に集結開始</div>
                                    <span class="ml-2 text-[10px] bg-gray-100 dark:bg-[#1e1f20] text-gray-400 px-1 py-0.5 rounded whitespace-nowrap shrink-0 border border-gray-200 dark:border-gray-800">AI生成</span>
                                </div>
                            </div>
                        </div>
                        <button @click="timelineExpanded = !timelineExpanded" class="mt-1 ml-3 text-xs font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <span x-text="timelineExpanded ? '▲ 閉じる' : '▼ もっと見る'"></span>
                        </button>
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
                    isModalOpen: false,
                    isAnalysisModalOpen: false 
                 }" 
                 x-init="$watch('activeTab', value => sessionStorage.setItem('activeTab_{{ $topic->id }}', value))" 
                 class="mt-1">
                
                <div class="flex border-b border-gray-300 dark:border-gray-800 mb-3 overflow-x-auto">
                    <button @click="activeTab = 'info'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'info' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        情報
                    </button>
                    <button @click="activeTab = 'comments'" 
                            :class="{ 'border-gray-900 text-gray-900 dark:border-gray-200 dark:text-white font-bold': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'comments' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        コメント
                    </button>
                    <button @click="activeTab = 'analysis'" 
                            :class="{ 'border-yellow-500 text-gray-900 dark:text-white font-bold': activeTab === 'analysis', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-300': activeTab !== 'analysis' }" 
                            class="py-2 px-6 border-b-2 text-sm transition-colors focus:outline-none whitespace-nowrap flex items-center">
                        分析・図解 
                        <span class="ml-1.5 text-[9px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1 py-0.5 rounded font-black tracking-wider">PRO</span>
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

                <div x-show="isModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" @click.away="isModalOpen = false" class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">エビデンスを投稿</h3>
                                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors"><span class="text-2xl leading-none">&times;</span></button>
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

                <div x-show="isAnalysisModalOpen" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div x-show="isAnalysisModalOpen" x-transition.opacity class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
                            <div x-show="isAnalysisModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" @click.away="isAnalysisModalOpen = false" class="relative transform overflow-hidden bg-white dark:bg-[#18191a] rounded-t-2xl sm:rounded-xl border-t sm:border border-gray-200 dark:border-gray-800 text-left shadow-2xl transition-all w-full h-[85vh] sm:h-auto sm:max-w-xl flex flex-col">
                                
                                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        マイページから投稿
                                    </h3>
                                    <button @click="isAnalysisModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none transition-colors"><span class="text-2xl leading-none">&times;</span></button>
                                </div>

                                <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-white dark:bg-[#131314]">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">保存済みで、まだ他のトピックに公開していない分析・図解の一覧です。</p>
                                    
                                    @if(isset($myAvailableAnalyses) && $myAvailableAnalyses->isEmpty())
                                        <div class="text-center py-6 border border-gray-200 dark:border-gray-800 rounded-lg">
                                            <p class="text-sm text-gray-500 mb-2">公開できる分析・図解がありません。</p>
                                            <a href="{{ route('tools.tree') }}" class="text-xs text-blue-500 hover:underline">ツールを使って新しく作成する</a>
                                        </div>
                                    @else
                                        <div class="space-y-3">
                                            @foreach($myAvailableAnalyses as $analysis)
                                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-[#1e1f20] flex justify-between items-center">
                                                    <div>
                                                        <div class="mb-1">
                                                            @if($analysis->type === 'tree') <span class="text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 px-1.5 py-0.5 rounded">ロジックツリー</span>
                                                            @elseif($analysis->type === 'matrix') <span class="text-[10px] bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 px-1.5 py-0.5 rounded">総合評価表</span>
                                                            @elseif($analysis->type === 'swot') <span class="text-[10px] bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 px-1.5 py-0.5 rounded">SWOT分析</span>
                                                            @endif
                                                        </div>
                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $analysis->title }}</h4>
                                                        <span class="text-[10px] text-gray-400">作成日: {{ $analysis->created_at->format('Y-m-d') }}</span>
                                                    </div>
                                                    
                                                    <form method="POST" action="{{ route('tools.publish', $analysis) }}">
                                                        @csrf
                                                        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                                                            このトピックに投稿
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
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