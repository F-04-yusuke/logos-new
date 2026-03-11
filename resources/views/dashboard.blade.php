<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">自分が作成したトピック</h2>
                    </header>
                    <div class="mt-6 space-y-4">
                        @if ($myTopics->isEmpty())
                            <p class="text-sm text-gray-500">まだ作成したトピックはありません。</p>
                        @else
                            @foreach ($myTopics as $topic)
                                <div class="p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('topics.show', $topic) }}" class="text-blue-500 hover:underline font-bold text-lg">{{ $topic->title }}</a>
                                        <p class="text-xs text-gray-500 mt-2">作成日: {{ $topic->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('topics.edit', $topic) }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">編集</a>
                                        <form method="POST" action="{{ route('topics.destroy', $topic) }}" onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">削除</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">自分が投稿したエビデンス</h2>
                    </header>
                    <div class="mt-6 space-y-4">
                        @if ($myPosts->isEmpty())
                            <p class="text-sm text-gray-500">まだ投稿したエビデンスはありません。</p>
                        @else
                            @foreach ($myPosts as $post)
                                <div class="p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 mb-2">
                                            投稿先: <a href="{{ route('topics.show', $post->topic) }}" class="text-blue-400 hover:underline">{{ $post->topic->title }}</a>
                                        </div>
                                        <div class="mb-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $post->category }}</span>
                                        </div>
                                        <a href="{{ $post->url }}" target="_blank" class="block text-blue-500 hover:underline break-all text-sm mb-2">{{ $post->url }}</a>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap mt-2">{{ $post->comment }}</p>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <a href="{{ route('posts.edit', $post) }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">編集</a>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">削除</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1e1f20] shadow sm:rounded-lg dark:border dark:border-gray-800">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            作成した分析・図解
                            <span class="ml-2 text-[10px] bg-yellow-500 text-white px-1.5 py-0.5 rounded font-black tracking-wider">PRO</span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            ツールで保存したロジックツリーや評価表の一覧です。トピック詳細画面の「分析・図解」タブから連携（公開）できます。
                        </p>
                    </header>
                    
                    @php
                        // コントローラーを修正する手間を省き、直接自分のデータを取得します
                        $myAnalyses = \App\Models\Analysis::where('user_id', auth()->id())->latest()->get();
                    @endphp

                    <div class="mt-6 space-y-4">
                        @if ($myAnalyses->isEmpty())
                            <div class="text-center py-6 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                                <p class="text-sm text-gray-500">まだ保存された分析・図解はありません。</p>
                                <a href="{{ route('tools.tree') }}" class="inline-block mt-3 text-xs text-blue-500 hover:text-blue-600 font-bold">＋ ツールを使ってみる</a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($myAnalyses as $analysis)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-[#131314] flex flex-col justify-between hover:border-blue-500 transition-colors">
                                        <div>
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center gap-2">
                                                    @if($analysis->type === 'tree')
                                                        <span class="text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 px-2 py-0.5 rounded">ロジックツリー</span>
                                                    @elseif($analysis->type === 'matrix')
                                                        <span class="text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 px-2 py-0.5 rounded">総合評価表</span>
                                                    @elseif($analysis->type === 'swot')
                                                        <span class="text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 px-2 py-0.5 rounded">SWOT分析</span>
                                                    @endif
                                                </div>
                                                
                                                @if($analysis->is_published)
                                                    <span class="text-[10px] border border-green-500 text-green-600 dark:text-green-400 px-1 py-0.5 rounded whitespace-nowrap">公開中</span>
                                                @else
                                                    <span class="text-[10px] border border-gray-400 text-gray-500 dark:text-gray-400 px-1 py-0.5 rounded whitespace-nowrap">非公開 (下書き)</span>
                                                @endif
                                            </div>
                                            
                                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base line-clamp-2" title="{{ $analysis->title }}">
                                                {{ $analysis->title }}
                                            </h3>
                                            
                                            @if($analysis->topic_id)
                                                <p class="text-[10px] text-gray-500 mt-1 truncate">
                                                    連携先: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:underline">{{ $analysis->topic->title }}</a>
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4 flex justify-between items-center border-t border-gray-200 dark:border-gray-800 pt-3">
                                            <span class="text-[10px] text-gray-400">{{ $analysis->created_at->format('Y-m-d H:i') }}</span>
                                            
                                            <div class="flex space-x-3">
                                                <form method="POST" action="#" onsubmit="return confirm('本当に削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
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