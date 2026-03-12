<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1e1f20] shadow sm:rounded-lg border border-gray-200 dark:border-gray-800">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            自分が作成したトピック
                        </h2>
                    </header>
                    <div class="mt-6 space-y-3">
                        @if ($myTopics->isEmpty())
                        <div class="text-center py-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">まだ作成したトピックはありません。</p>
                        </div>
                        @else
                        @foreach ($myTopics as $topic)
                        <div class="p-4 border border-gray-200 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-[#131314] flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-colors">
                            <div>
                                <a href="{{ route('topics.show', $topic) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline font-bold text-base transition-colors">{{ $topic->title }}</a>
                                <p class="text-[10px] text-gray-500 dark:text-gray-500 mt-1">作成日: {{ $topic->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                            <div class="flex space-x-3 shrink-0">
                                <a href="{{ route('topics.edit', $topic) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">編集</a>
                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                <form method="POST" action="{{ route('topics.destroy', $topic) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1e1f20] shadow sm:rounded-lg border border-gray-200 dark:border-gray-800">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            自分が投稿したエビデンス
                        </h2>
                    </header>
                    <div class="mt-6 space-y-3">
                        @if ($myPosts->isEmpty())
                        <div class="text-center py-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">まだ投稿したエビデンスはありません。</p>
                        </div>
                        @else
                        @foreach ($myPosts as $post)
                        <div class="p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col transition-colors">
                            
                            <div class="flex justify-between items-start mb-2">
                                <span class="inline-block px-2 py-0.5 text-xs rounded border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">{{ $post->category }}</span>
                                <div class="text-right text-[10px] text-gray-500 dark:text-gray-500">
                                    <span>{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                            
                            <a href="{{ $post->url }}" target="_blank" class="block font-bold text-sm text-gray-900 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 mb-2 truncate transition-colors">{{ $post->url }}</a>
                            
                            @if ($post->comment)
                            <div class="text-gray-800 dark:text-gray-300 text-sm whitespace-pre-wrap mt-1 bg-white dark:bg-[#1e1f20] p-3 rounded border border-gray-200 dark:border-gray-800">{{ $post->comment }}</div>
                            @endif

                            <div class="mt-3 text-[10px] text-gray-500 dark:text-gray-400">
                                連携先トピック: <a href="{{ route('topics.show', $post->topic) }}" class="text-blue-500 hover:text-blue-400 hover:underline transition-colors">{{ $post->topic->title }}</a>
                            </div>
                            
                            <div class="mt-3 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-3">
                                <a href="{{ route('posts.edit', $post) }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">編集</a>
                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('削除しますか？');" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1e1f20] shadow sm:rounded-lg border border-gray-200 dark:border-gray-800">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            作成した分析・図解
                            <span class="ml-2 text-[10px] bg-yellow-500 text-white dark:bg-yellow-500/20 dark:text-yellow-500 px-1.5 py-0.5 rounded font-black tracking-wider border border-transparent dark:border-yellow-500/30">PRO</span>
                        </h2>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                            ツールで保存したデータ一覧です。トピック詳細画面の「分析・図解」タブから連携（公開）できます。
                        </p>
                    </header>

                    @php
                    $myAnalyses = \App\Models\Analysis::where('user_id', auth()->id())->latest()->get();
                    @endphp

                    <div class="mt-6 space-y-4">
                        @if ($myAnalyses->isEmpty())
                        <div class="text-center py-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">まだ保存された分析・図解はありません。</p>
                            <a href="{{ route('tools.tree') }}" class="inline-block mt-2 text-xs text-blue-500 hover:text-blue-600 font-bold transition-colors">＋ ツールを使ってみる</a>
                        </div>
                        @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($myAnalyses as $analysis)
                            <div class="p-4 border border-gray-200 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-[#131314] flex flex-col justify-between hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            @if($analysis->type === 'tree') <span class="text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 px-2 py-0.5 rounded border border-transparent dark:border-blue-800/50">ロジックツリー</span>
                                            @elseif($analysis->type === 'matrix') <span class="text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 px-2 py-0.5 rounded border border-transparent dark:border-purple-800/50">総合評価表</span>
                                            @elseif($analysis->type === 'swot')
                                            @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                                            <span class="text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 px-2 py-0.5 rounded border border-transparent dark:border-green-800/50">{{ $isPest ? 'PEST分析' : 'SWOT分析' }}</span>
                                            @endif
                                        </div>

                                        @if($analysis->is_published)
                                        <span class="text-[10px] border border-green-500 text-green-600 dark:text-green-400 px-1.5 py-0.5 rounded whitespace-nowrap">公開中</span>
                                        @else
                                        <span class="text-[10px] border border-gray-400 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 rounded whitespace-nowrap">下書き</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('analyses.show', $analysis) }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm sm:text-base line-clamp-2 transition-colors hover:underline">
                                        {{ $analysis->title }}
                                    </a>

                                    @if($analysis->topic_id)
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1.5 truncate">
                                        連携先: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:text-blue-400 hover:underline transition-colors">{{ $analysis->topic->title }}</a>
                                    </p>
                                    @endif
                                </div>

                                <div class="mt-4 flex justify-between items-center border-t border-gray-200 dark:border-gray-800 pt-3">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $analysis->created_at->format('Y-m-d H:i') }}</span>

                                    <div class="flex space-x-3">
                                        <button type="button" onclick="alert('分析・図解の再編集機能は現在開発中です。\n※将来的にはここからツール画面に戻ってAIと続きができるようになります。')" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                            編集
                                        </button>
                                        <span class="text-gray-300 dark:text-gray-700">|</span>
                                        <form method="POST" action="{{ route('analyses.destroy', $analysis) }}" onsubmit="return confirm('本当に削除しますか？');" class="m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>