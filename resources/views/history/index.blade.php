<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-900 dark:text-gray-100 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            閲覧履歴
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                <div class="p-4 sm:p-8">
                    @if($viewedTopics->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 px-4">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-bold">まだ閲覧履歴はありません。</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">トピックを見ると、ここに履歴が残ります。</p>
                        </div>
                    @else
                        @php
                            $currentLabel = '';
                            \Carbon\Carbon::setLocale('ja'); // 日本語の曜日を出力するために設定
                        @endphp

                        <div class="space-y-1.5">
                            @foreach($viewedTopics as $topic)
                                @php
                                    // 閲覧時間を判定し、YouTube風のラベルを生成
                                    $viewedAt = \Carbon\Carbon::parse($topic->pivot->last_viewed_at);
                                    $label = '';
                                    if ($viewedAt->isToday()) {
                                        $label = '今日';
                                    } elseif ($viewedAt->isYesterday()) {
                                        $label = '昨日';
                                    } elseif ($viewedAt->isBetween(now()->subDays(6), now()->subDays(1)->endOfDay())) {
                                        $label = $viewedAt->isoFormat('dddd'); // 「月曜日」などの曜日出力
                                    } else {
                                        $label = $viewedAt->format('Y年n月j日'); // それ以前は年月日
                                    }
                                @endphp

                                {{-- ラベルが変わったタイミングで新しい見出しを出力 --}}
                                @if($currentLabel !== $label)
                                    <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-gray-100 mt-8 mb-3 px-2 border-b border-gray-100 dark:border-gray-800/60 pb-2">
                                        {{ $label }}
                                    </h3>
                                    @php $currentLabel = $label; @endphp
                                @endif

                                {{-- トピック名とカテゴリのみの洗練されたリスト --}}
                                <div class="p-3 sm:p-4 bg-white dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800 flex justify-between items-center shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('topics.show', $topic) }}" class="font-bold text-sm sm:text-base text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors line-clamp-1 block mb-1.5">
                                            {{ $topic->title }}
                                        </a>
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($topic->categories && $topic->categories->isNotEmpty())
                                                @foreach($topic->categories as $category)
                                                    <span class="text-[10px] sm:text-[11px] font-bold text-gray-500 bg-gray-100 dark:bg-[#1e1f20] px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-[10px] sm:text-[11px] font-bold text-gray-500 bg-gray-100 dark:bg-[#1e1f20] px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                                    {{ $topic->category ?? '未分類' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $viewedTopics->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>