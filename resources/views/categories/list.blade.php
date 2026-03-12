<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリ一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">
                    興味のあるカテゴリを選択すると、関連するトピックを絞り込んで表示します。
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($categories as $parent)
                    <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center">
                                <span class="text-2xl mr-2">📁</span>
                                <a href="{{ route('topics.index', ['category' => $parent->id]) }}" class="hover:text-blue-500 transition-colors">
                                    {{ $parent->name }}
                                </a>
                            </h3>
                            
                            @if($parent->children->isNotEmpty())
                                <ul class="space-y-3 pl-2">
                                    @foreach($parent->children as $child)
                                        <li>
                                            <a href="{{ route('topics.index', ['category' => $child->id]) }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 flex items-center transition-colors group">
                                                <span class="text-gray-400 group-hover:text-blue-500 mr-2">└ 📄</span> 
                                                <span class="group-hover:underline">{{ $child->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic pl-2">中分類はありません</p>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            @if($categories->isEmpty())
                <div class="p-6 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm text-center text-gray-500 dark:text-gray-400">
                    現在、登録されているカテゴリはありません。
                </div>
            @endif

        </div>
    </div>
</x-app-layout>