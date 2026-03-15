<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリ一覧
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6 sm:mb-8">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    興味のあるカテゴリを選択すると、関連するトピックを絞り込んで表示します。
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($categories as $parent)
                    {{-- カード全体：少し丸みを帯びたモダンな枠 --}}
                    <div class="bg-white dark:bg-[#1e1f20] shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        
                        {{-- 大分類ヘッダー：タップしやすいようにpaddingを確保し、背景色で区切る --}}
                        <div class="bg-gray-50 dark:bg-[#131314] border-b border-gray-200 dark:border-gray-800 px-4 sm:px-5 py-4 sm:py-4">
                            <a href="{{ route('topics.index', ['category' => $parent->id]) }}" class="flex items-center group">
                                {{-- 絵文字をやめ、洗練されたSVGフォルダアイコンに変更 --}}
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 mr-3 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-gray-100 group-hover:text-blue-500 transition-colors line-clamp-1">
                                    {{ $parent->name }}
                                </h3>
                            </a>
                        </div>
                        
                        {{-- 中分類リスト --}}
                        <div class="p-2 sm:p-3">
                            @if($parent->children->isNotEmpty())
                                <ul class="space-y-1">
                                    @foreach($parent->children as $child)
                                        <li>
                                            {{-- スマホ対応：横幅いっぱいのブロックリンクにし、タップ領域を最大化 --}}
                                            <a href="{{ route('topics.index', ['category' => $child->id]) }}" class="flex items-center px-3 py-2.5 sm:py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#222222] hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
                                                {{-- 矢印アイコンで階層を表現 --}}
                                                <svg aria-hidden="true" class="h-4 w-4 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 mr-2 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                                <span class="truncate">{{ $child->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-500 italic px-3 py-2">中分類はありません</p>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            @if($categories->isEmpty())
                {{-- 空の状態もモダンな破線スタイルに変更 --}}
                <div class="flex flex-col items-center justify-center py-16 px-4 border-2 border-dashed border-gray-300 dark:border-gray-800 rounded-xl bg-gray-50 dark:bg-[#131314]/50 text-center">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mb-1">カテゴリがありません</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">現在、登録されているカテゴリはまだありません。</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>