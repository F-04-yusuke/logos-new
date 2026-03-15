<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $topicAnalyses->count() }}件の分析・図解</h3>
        <form method="GET" action="{{ route('topics.show', $topic) }}" class="flex m-0 p-0">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('comment_sort')) <input type="hidden" name="comment_sort" value="{{ request('comment_sort') }}"> @endif
            {{-- 【修正】スマホでのタップ領域を広げるため py-1.5 sm:py-1 に変更 --}}
            <select name="analysis_sort" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1.5 sm:py-1">
                <option value="popular" {{ request('analysis_sort') === 'popular' || !request('analysis_sort') ? 'selected' : '' }}>人気順</option>
                <option value="newest" {{ request('analysis_sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                <option value="oldest" {{ request('analysis_sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
            </select>
        </form>
    </div>

    {{-- 【修正】スマホでのタップ領域拡大と、アクセシビリティ対応（sr-only）を追加 --}}
    <button @click="isAnalysisModalOpen = true" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 px-3 sm:py-1.5 sm:px-4 rounded text-xs sm:text-sm transition-colors flex items-center shrink-0">
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="sr-only">分析・図解を投稿する</span>
        <span class="hidden sm:inline" aria-hidden="true">分析・図解を投稿</span>
    </button>
</div>

@if($topicAnalyses->isEmpty())
<div class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-gray-300 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-[#131314]/50">
    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mb-1">まだ分析・図解は投稿されていません</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-sm">
        プレミアムプランに登録すると、オリジナル図解をアップロードしたり、「ロジックツリー」や「総合評価表」を作成してここに公開することができます。
    </p>
</div>
@else
<div class="space-y-4">
    @foreach($topicAnalyses as $analysis)
        {{-- 【修正】先ほど作成した共通コンポーネントを呼び出すだけ --}}
        <x-analysis-card :analysis="$analysis" />
    @endforeach
</div>
@endif