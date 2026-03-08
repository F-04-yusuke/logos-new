<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                トピック一覧
            </h2>
            <a href="{{ route('topics.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                ＋ 新規トピック作成
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(request('search'))
                <p class="mb-4 text-gray-600 dark:text-gray-400 font-bold">
                    「{{ request('search') }}」の検索結果: {{ $topics->count() }}件
                </p>
            @endif

            @if(isset($selectedCategory))
                <div class="mb-4 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg border border-indigo-100 dark:border-indigo-800">
                    <p class="text-indigo-800 dark:text-indigo-200 font-bold">
                        📁 カテゴリ「{{ $selectedCategory->name }}」のトピック一覧: {{ $topics->count() }}件
                    </p>
                    <a href="{{ route('topics.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        絞り込みを解除する ✕
                    </a>
                </div>
            @endif

            <div class="space-y-4">
                @foreach ($topics as $topic)
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow">
                        
                        <a href="{{ route('topics.show', $topic) }}" class="block">
                            <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400 hover:underline mb-2">
                                {{ $topic->title }}
                            </h3>
                            
                            @if($topic->categories->isNotEmpty())
                                <div class="mb-3 flex flex-wrap gap-2">
                                    @foreach($topic->categories as $category)
                                        <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="inline-block px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 border border-indigo-200 dark:border-indigo-700 transition-colors">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-4">
                                {{ $topic->content }}
                            </p>
                            
                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <span>作成者: {{ $topic->user->name }}</span>
                                <div class="space-x-4">
                                    <span class="font-bold text-indigo-500">エビデンス数: {{ $topic->posts->count() }}件</span>
                                    <span>{{ $topic->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach

                @if($topics->isEmpty())
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-center text-gray-500 dark:text-gray-400">
                        該当するトピックが見つかりませんでした。
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>