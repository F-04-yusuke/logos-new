<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                @if($analysis->type === 'tree') <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg> ロジックツリー分析
                @elseif($analysis->type === 'matrix') <svg class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg> 総合評価表
                @elseif($analysis->type === 'swot')
                    @php $isPest = isset($analysis->data['framework']) && $analysis->data['framework'] === 'PEST'; @endphp
                    <svg class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    {{ $isPest ? 'PEST分析' : 'SWOT分析' }}
                @endif
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                &larr; 戻る
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-[#1e1f20] overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-800">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $analysis->title }}</h1>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 gap-4">
                        <span>作成者: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $analysis->user->name }}</span></span>
                        <span>作成日: {{ $analysis->created_at->format('Y-m-d') }}</span>
                        @if($analysis->topic_id)
                            <span>連携先: <a href="{{ route('topics.show', $analysis->topic_id) }}" class="text-blue-500 hover:underline">{{ $analysis->topic->title }}</a></span>
                        @endif
                    </div>
                </div>
            </div>

            @if($analysis->type === 'tree')
                @php
                    $treeData = $analysis->data;
                    $nodes = $treeData['nodes'] ?? $treeData; // 新構造ならnodes、旧構造ならそのまま
                    $meta = $treeData['meta'] ?? null;
                @endphp

                @if($meta && (!empty($meta['url']) || !empty($meta['description'])))
                    <div class="bg-white dark:bg-[#1e1f20] p-6 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-800 mb-6">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            事前情報
                        </h3>
                        @if(!empty($meta['description']))
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2 whitespace-pre-wrap">{{ $meta['description'] }}</p>
                        @endif
                        @if(!empty($meta['url']))
                            <a href="{{ $meta['url'] }}" target="_blank" class="text-sm text-blue-500 hover:underline flex items-center break-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                {{ $meta['url'] }}
                            </a>
                        @endif
                    </div>
                @endif

                <div class="bg-white dark:bg-[#1e1f20] p-6 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-800 overflow-x-auto">
                    <style>
                        .tree-line::before { content: ''; position: absolute; top: 0; bottom: 0; left: -1rem; width: 2px; background-color: #374151; border-radius: 2px; }
                        .tree-line::after { content: ''; position: absolute; top: 1.5rem; left: -1rem; width: 1rem; height: 2px; background-color: #374151; border-radius: 2px; }
                    </style>
                    <div id="tree-container" class="pl-4 pb-8"></div>
                </div>

                <script>
                    const treeData = @json($nodes);
                    
                    function getStanceStyle(stance) {
                        if(stance === '反論') return 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
                        if(stance === '賛成・補足') return 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
                        if(stance === '疑問') return 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800';
                        return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
                    }

                    function renderNode(node, container) {
                        const el = document.createElement('div');
                        el.className = "mt-4 relative tree-line ml-8";
                        
                        const isSelf = node.speaker.includes('自');
                        const speakerColor = isSelf ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300';
                        
                        el.innerHTML = `
                            <div class="bg-gray-50 dark:bg-[#131314] p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm inline-block min-w-[250px] max-w-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm font-bold ${speakerColor}">${node.speaker}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded border font-bold ${getStanceStyle(node.stance)}">${node.stance}</span>
                                </div>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">${node.text}</p>
                            </div>
                            <div class="replies-container"></div>
                        `;
                        container.appendChild(el);
                        
                        if (node.children && node.children.length > 0) {
                            const repliesContainer = el.querySelector('.replies-container');
                            node.children.forEach(child => renderNode(child, repliesContainer));
                        }
                    }

                    const rootContainer = document.getElementById('tree-container');
                    if (treeData && treeData.length > 0) {
                        treeData.forEach(node => renderNode(node, rootContainer));
                    } else {
                        rootContainer.innerHTML = '<p class="text-gray-500">ツリーデータがありません。</p>';
                    }
                </script>

            @elseif($analysis->type === 'matrix')
                @php
                    $patterns = $analysis->data['patterns'] ?? [];
                    $items = $analysis->data['items'] ?? [];
                    // 総合評価の計算
                    $totals = array_fill(0, count($patterns), 0);
                    $isCalculated = array_fill(0, count($patterns), false);
                    foreach($items as $item) {
                        foreach($item['scores'] ?? [] as $index => $scoreObj) {
                            $val = (int)($scoreObj['score'] ?? -1);
                            if($val !== -1) {
                                $totals[$index] += $val;
                                $isCalculated[$index] = true;
                            }
                        }
                    }
                @endphp
                <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-800 overflow-x-auto custom-scroll p-4">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr>
                                <th class="p-3 border-b border-r border-gray-200 dark:border-gray-700 w-48 bg-gray-50 dark:bg-[#131314] align-bottom text-xs font-bold text-gray-500 dark:text-gray-400">評価項目 \ 比較パターン</th>
                                @foreach($patterns as $pattern)
                                    <th class="p-4 border-b border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-50 dark:bg-[#131314] align-top">
                                        <div class="font-bold text-blue-600 dark:text-blue-400 mb-1 text-base">{{ $pattern['title'] }}</div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-wrap font-normal">{{ $pattern['description'] }}</p>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#131314] font-bold text-sm text-gray-900 dark:text-gray-200">
                                        {{ $item['itemTitle'] }}
                                    </td>
                                    @foreach($item['scores'] ?? [] as $scoreObj)
                                        @php
                                            $val = (int)($scoreObj['score'] ?? -1);
                                            $badgeInfo = ['text' => '--', 'color' => 'bg-gray-100 text-gray-500'];
                                            if($val === 3) $badgeInfo = ['text' => '◎ 最適 (3pt)', 'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'];
                                            elseif($val === 2) $badgeInfo = ['text' => '〇 良い (2pt)', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'];
                                            elseif($val === 1) $badgeInfo = ['text' => '△ 懸念 (1pt)', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'];
                                            elseif($val === 0) $badgeInfo = ['text' => '× 不可 (0pt)', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'];
                                        @endphp
                                        <td class="p-4 border-b border-r border-gray-200 dark:border-gray-700 align-top">
                                            @if($val !== -1)
                                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded mb-2 {{ $badgeInfo['color'] }}">{{ $badgeInfo['text'] }}</span>
                                            @endif
                                            <p class="text-xs text-gray-800 dark:text-gray-300 whitespace-pre-wrap">{{ $scoreObj['reason'] ?? '' }}</p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-[#131314]">
                            <tr>
                                <td class="p-3 border-r border-gray-200 dark:border-gray-700 text-right text-xs font-bold text-gray-500">総合評価</td>
                                @foreach($totals as $index => $total)
                                    <td class="p-3 border-r border-gray-200 dark:border-gray-700 text-center">
                                        @if($isCalculated[$index])
                                            <span class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $total }}</span>
                                            <span class="text-xs text-gray-500 ml-1">pt</span>
                                        @else
                                            <span class="text-sm text-gray-400">未評価</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @elseif($analysis->type === 'swot')
                @php
                    $swotData = $analysis->data;
                    $isPest = isset($swotData['framework']) && $swotData['framework'] === 'PEST';
                    $b1 = $swotData['box1'] ?? $swotData['strengths'] ?? [];
                    $b2 = $swotData['box2'] ?? $swotData['weaknesses'] ?? [];
                    $b3 = $swotData['box3'] ?? $swotData['opportunities'] ?? [];
                    $b4 = $swotData['box4'] ?? $swotData['threats'] ?? [];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-blue-500 rounded-lg p-5 shadow-sm border-x border-b dark:border-transparent border-gray-200">
                        <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center">
                            @if($isPest) <span class="text-2xl mr-2">P</span>olitics <span class="text-xs text-gray-500 ml-2 font-normal">政治</span>
                            @else <span class="text-2xl mr-2">S</span>trengths <span class="text-xs text-gray-500 ml-2 font-normal">強み</span> @endif
                        </h2>
                        <ul class="space-y-2 pl-1">
                            @foreach($b1 as $item) <li class="text-sm text-gray-800 dark:text-gray-200 flex items-start"><span class="text-blue-500 mr-2 mt-0.5">•</span> <span>{{ $item }}</span></li> @endforeach
                        </ul>
                    </div>
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-red-500 rounded-lg p-5 shadow-sm border-x border-b dark:border-transparent border-gray-200">
                        <h2 class="text-lg font-bold text-red-600 dark:text-red-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center">
                            @if($isPest) <span class="text-2xl mr-2">E</span>conomy <span class="text-xs text-gray-500 ml-2 font-normal">経済</span>
                            @else <span class="text-2xl mr-2">W</span>eaknesses <span class="text-xs text-gray-500 ml-2 font-normal">弱み</span> @endif
                        </h2>
                        <ul class="space-y-2 pl-1">
                            @foreach($b2 as $item) <li class="text-sm text-gray-800 dark:text-gray-200 flex items-start"><span class="text-red-500 mr-2 mt-0.5">•</span> <span>{{ $item }}</span></li> @endforeach
                        </ul>
                    </div>
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-green-500 rounded-lg p-5 shadow-sm border-x border-b dark:border-transparent border-gray-200">
                        <h2 class="text-lg font-bold text-green-600 dark:text-green-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center">
                            @if($isPest) <span class="text-2xl mr-2">S</span>ociety <span class="text-xs text-gray-500 ml-2 font-normal">社会</span>
                            @else <span class="text-2xl mr-2">O</span>pportunities <span class="text-xs text-gray-500 ml-2 font-normal">機会</span> @endif
                        </h2>
                        <ul class="space-y-2 pl-1">
                            @foreach($b3 as $item) <li class="text-sm text-gray-800 dark:text-gray-200 flex items-start"><span class="text-green-500 mr-2 mt-0.5">•</span> <span>{{ $item }}</span></li> @endforeach
                        </ul>
                    </div>
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-yellow-500 rounded-lg p-5 shadow-sm border-x border-b dark:border-transparent border-gray-200">
                        <h2 class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center">
                            @if($isPest) <span class="text-2xl mr-2">T</span>echnology <span class="text-xs text-gray-500 ml-2 font-normal">技術</span>
                            @else <span class="text-2xl mr-2">T</span>hreats <span class="text-xs text-gray-500 ml-2 font-normal">脅威</span> @endif
                        </h2>
                        <ul class="space-y-2 pl-1">
                            @foreach($b4 as $item) <li class="text-sm text-gray-800 dark:text-gray-200 flex items-start"><span class="text-yellow-500 mr-2 mt-0.5">•</span> <span>{{ $item }}</span></li> @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>