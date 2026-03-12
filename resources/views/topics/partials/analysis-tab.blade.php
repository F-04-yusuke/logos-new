<div class="flex items-center justify-between mb-4">
    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $topicAnalyses->count() }}件の分析・図解</h3>

    <button @click="isAnalysisModalOpen = true" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 sm:py-1.5 sm:px-4 rounded text-xs sm:text-sm transition-colors flex items-center shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="hidden sm:inline">マイページから投稿</span>
    </button>
</div>

@if($topicAnalyses->isEmpty())
<div class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-gray-300 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-[#131314]/50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mb-1">まだ分析・図解は投稿されていません</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-sm">
        プレミアムプランに登録すると、マイページで作成した「ロジックツリー」や「総合評価表」をここに公開して、議論を深めることができます。
    </p>
</div>
@else
<div class="space-y-3">
    @foreach($topicAnalyses as $analysis)
    <div class="p-4 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-colors flex flex-col gap-3">

        <div class="flex justify-between items-start mb-1">
            <div class="flex items-center gap-2 mt-1">
                @if($analysis->type === 'tree') <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-blue-200 text-blue-600 dark:border-blue-800 dark:text-blue-400">ロジックツリー</span>
                @elseif($analysis->type === 'matrix') <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-purple-200 text-purple-600 dark:border-purple-800 dark:text-purple-400">総合評価表</span>
                @elseif($analysis->type === 'swot')
                @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                <span class="inline-block px-2 py-0.5 text-xs font-bold rounded border border-green-200 text-green-600 dark:border-green-800 dark:text-green-400">{{ $isPest ? 'PEST分析' : 'SWOT分析' }}</span>
                @endif
            </div>
            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $analysis->user->name }}</span><br>
                <span>{{ $analysis->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#131314] p-4 text-sm overflow-hidden w-full flex-1" style="max-height: 400px; -webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%); mask-image: linear-gradient(to bottom, black 80%, transparent 100%);">

            @php $previewData = $analysis->data; @endphp

            @if($analysis->type === 'swot')
            <div class="font-bold text-base text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-800">{{ $analysis->title }}</div>
            @endif

            @if($analysis->type === 'tree' && !empty($previewData))
            @php
            $nodes = isset($previewData['nodes']) ? $previewData['nodes'] : $previewData;
            $meta = $previewData['meta'] ?? null;
            @endphp

            @if($meta && (!empty($meta['url']) || !empty($meta['description'])))
            <div class="mb-4 p-3 bg-white dark:bg-[#1e1f20] rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="text-[10px] font-bold text-blue-600 dark:text-blue-400 mb-1 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>事前情報</div>
                @if(!empty($meta['description'])) <p class="text-xs text-gray-800 dark:text-gray-300 mb-1.5">{{ $meta['description'] }}</p> @endif
                @if(!empty($meta['url'])) <a href="{{ $meta['url'] }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate block">{{ $meta['url'] }}</a> @endif
            </div>
            @endif

            <div class="space-y-3">
                @foreach(array_slice($nodes, 0, 5) as $node)
                <div class="flex gap-2">
                    <span class="font-bold text-blue-500 shrink-0">{{ $node['speaker'] ?? '' }}:</span>
                    <span class="text-gray-700 dark:text-gray-300 truncate">{{ $node['text'] ?? '' }}</span>
                </div>
                @if(!empty($node['children']))
                @foreach(array_slice($node['children'], 0, 1) as $child)
                <div class="ml-4 flex gap-2 border-l-2 border-gray-300 dark:border-gray-700 pl-2">
                    <span class="font-bold text-gray-500 shrink-0">↳ {{ $child['speaker'] ?? '' }}:</span>
                    <span class="text-gray-600 dark:text-gray-400 truncate">{{ $child['text'] ?? '' }}</span>
                </div>
                @endforeach
                @endif
                @endforeach
            </div>
            @elseif($analysis->type === 'matrix' && isset($previewData['items']))
            <div>
                <div class="font-bold text-gray-500 mb-2">【評価項目一覧】</div>
                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2 ml-1">
                    @foreach(array_slice($previewData['items'], 0, 5) as $item)
                    <li class="truncate">{{ $item['itemTitle'] ?? '' }}</li>
                    @endforeach
                </ul>
            </div>
            @elseif($analysis->type === 'swot')
            @php
            $isPest = isset($previewData['framework']) && $previewData['framework'] === 'PEST';
            // 新データ(box1)と旧データ(strengths)の両方に対応する安全設計
            $b1 = $previewData['box1'] ?? $previewData['strengths'] ?? [];
            $b2 = $previewData['box2'] ?? $previewData['weaknesses'] ?? [];
            $b3 = $previewData['box3'] ?? $previewData['opportunities'] ?? [];
            $b4 = $previewData['box4'] ?? $previewData['threats'] ?? [];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="font-bold text-blue-500 mb-1 inline-block">{{ $isPest ? 'P (政治)' : 'S (強み)' }}:</span>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        @forelse(array_slice($b1, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                    </ul>
                </div>
                <div>
                    <span class="font-bold text-red-500 mb-1 inline-block">{{ $isPest ? 'E (経済)' : 'W (弱み)' }}:</span>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        @forelse(array_slice($b2, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                    </ul>
                </div>
                <div>
                    <span class="font-bold text-green-500 mb-1 inline-block">{{ $isPest ? 'S (社会)' : 'O (機会)' }}:</span>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        @forelse(array_slice($b3, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                    </ul>
                </div>
                <div>
                    <span class="font-bold text-yellow-500 mb-1 inline-block">{{ $isPest ? 'T (技術)' : 'T (脅威)' }}:</span>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                        @forelse(array_slice($b4, 0, 3) as $txt) <li class="truncate">{{ $txt }}</li> @empty <li class="text-gray-500">記載なし</li> @endforelse
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-1 flex items-center justify-between">
            <a href="{{ route('analyses.show', $analysis) }}" class="text-xs font-bold text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                もっと見る <span class="ml-1 text-[10px]">▶</span>
            </a>

            <div class="flex items-center gap-3">
                @if ($analysis->user_id === auth()->id())
                <button type="button" onclick="alert('削除機能は準備中です')" class="text-xs text-red-400 hover:text-red-600 transition-colors">削除</button>
                <span class="text-gray-300 dark:text-gray-700">|</span>
                @endif

                <button type="button" class="flex items-center space-x-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 1.5.58c.36.31.6.76.68 1.25.04.24.06.49.06.75 0 .76-.23 1.48-.63 2.08-.2.31-.05.73.3.88l3.126.33a2.25 2.25 0 0 1 1.954 2.65l-1.42 6.75c-.24 1.14-1.28 1.96-2.45 1.96H13.5a5.5 5.5 0 0 1-2.5-.6l-3.11-1.42a4.5 4.5 0 0 0-1.43-.24H5.9c-.83 0-1.5-.67-1.5-1.5V11.75c0-.83.67-1.5 1.5-1.5h.733Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.25h1.5v9h-1.5v-9Z" />
                    </svg>
                    <span class="text-sm">0</span>
                </button>
            </div>
        </div>

    </div>
    @endforeach
</div>
@endif