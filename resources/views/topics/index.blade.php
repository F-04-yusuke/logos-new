<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight flex items-center gap-2">
                情報共有プラットフォーム <span class="text-xs font-normal text-gray-500 ml-2 mt-1">政治・経済・エンタメ・スポーツ・その他</span>
            </h2>
            <a href="{{ route('topics.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors shadow-sm">
                ＋ 新規トピック作成
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">

            <div class="w-full lg:w-2/3 space-y-8">

                @if(request('search'))
                <div class="bg-blue-50 dark:bg-[#1e1f20] p-4 rounded-lg border border-blue-100 dark:border-transparent mb-4">
                    <p class="text-blue-800 dark:text-blue-300 font-bold flex justify-between items-center">
                        <span>🔍 「{{ request('search') }}」の検索結果: {{ $topics->total() }}件</span>
                        <a href="{{ route('topics.index') }}" class="text-sm text-blue-500 hover:text-blue-700">&times; クリア</a>
                    </p>
                </div>
                @endif

                @if(isset($selectedCategory))
                <div class="mb-4 flex items-center justify-between bg-indigo-50 dark:bg-[#1e1f20] p-4 rounded-lg border border-indigo-100 dark:border-transparent shadow-sm">
                    <p class="text-indigo-800 dark:text-indigo-200 font-bold flex items-center">
                        <span class="mr-2">📁</span> カテゴリ「{{ $selectedCategory->name }}」のトピック: {{ $topics->total() }}件
                    </p>
                    <a href="{{ route('topics.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                        すべてのトピックを表示 &times;
                    </a>
                </div>
                @endif

                @if(!request('search') && !request('category') && $tabCategories->isNotEmpty())
                <div class="bg-white dark:bg-[#1e1f20] rounded-lg shadow-sm border border-gray-200 dark:border-transparent overflow-hidden"
                    x-data="{ activeTab: {{ $tabCategories->first()->id }} }">

                    <div class="flex overflow-x-auto border-b border-gray-200 dark:border-gray-800 scrollbar-hide">
                        @foreach($tabCategories as $category)
                        <button @click="activeTab = {{ $category->id }}"
                            :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeTab === {{ $category->id }}, 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== {{ $category->id }} }"
                            class="flex-1 min-w-[100px] py-3 px-4 text-center text-sm border-b-2 transition-colors whitespace-nowrap">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>

                    <div class="p-0">
                        @foreach($tabCategories as $category)
                        <div x-show="activeTab === {{ $category->id }}" x-cloak>
                            @if($category->latest_topics->isEmpty())
                            <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">このカテゴリにはまだトピックがありません。</p>
                            @else
                            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($category->latest_topics as $topic)
                                <li>
                                    <a href="{{ route('topics.show', $topic) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-[#131314] transition-colors group">
                                        <h4 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1 mb-1">
                                            {{ $topic->title }}
                                        </h4>
                                        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-500">
                                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                                            <span class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-gray-600 dark:text-gray-400">
                                                💬 {{ $topic->posts()->count() }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="bg-gray-50 dark:bg-[#131314] p-2 text-center border-t border-gray-100 dark:border-gray-800">
                                <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="text-xs text-blue-500 hover:underline">
                                    {{ $category->name }}のトピックをもっと見る &rarr;
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="mr-2">🆕</span> トピック一覧
                        </h3>

                        <form method="GET" action="{{ route('topics.index') }}" class="flex items-center">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

                            <select name="sort" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-[#131314] dark:border-gray-700 dark:text-white transition-colors py-1">
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>エビデンスが多い順</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                            </select>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @forelse ($topics as $topic)
                        <div class="bg-white dark:bg-[#1e1f20] rounded-lg shadow-sm border border-gray-200 dark:border-transparent p-5 hover:border-blue-300 dark:hover:border-gray-700 transition-colors">
                            <a href="{{ route('topics.show', $topic) }}" class="block group">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($topic->categories as $category)
                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded bg-indigo-50 text-indigo-700 dark:bg-[#131314] dark:text-indigo-300 border border-indigo-100 dark:border-gray-800">
                                        {{ $category->name }}
                                    </span>
                                    @endforeach
                                </div>

                                <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $topic->title }}
                                </h4>

                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $topic->content }}
                                </p>

                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-500">
                                    <span>作成: {{ $topic->user->name }}</span>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-bold flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            エビデンス {{ $topic->posts_count }}件
                                        </span>
                                        <span>{{ $topic->created_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="p-6 text-center text-gray-500 bg-white dark:bg-[#1e1f20] rounded-lg border border-gray-100 dark:border-transparent">
                            トピックがありません。
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $topics->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>

            <div class="w-full lg:w-1/3 space-y-6">

                <div class="bg-white dark:bg-[#1e1f20] rounded-lg shadow-sm border border-gray-200 dark:border-transparent p-5 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-100 dark:border-gray-800 flex items-center">
                        <span class="mr-2 text-xl">🔥</span> 総合人気トピック
                    </h3>

                    @if($popularTopics->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">まだトピックがありません。</p>
                    @else
                    <ul class="space-y-4">
                        @foreach($popularTopics as $index => $topic)
                        <li>
                            <a href="{{ route('topics.show', $topic) }}" class="flex items-start group">
                                <div class="flex-shrink-0 w-6 h-6 rounded flex items-center justify-center text-xs font-bold text-white mr-3 mt-0.5
                                            {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : ($index === 2 ? 'bg-amber-600' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400')) }}">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-tight mb-1">
                                        {{ $topic->title }}
                                    </h4>
                                    <span class="text-xs text-gray-500 font-medium">
                                        エビデンス: {{ $topic->posts_count }}件
                                    </span>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>