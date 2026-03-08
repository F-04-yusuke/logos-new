<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $topic->title }}
            </h2>
            
            @if ($topic->isSavedBy(auth()->user()))
                <form method="POST" action="{{ route('bookmarks.destroy', $topic) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded text-sm flex items-center transition-colors">
                        <span class="mr-1">🔖</span> 保存済み（解除）
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('bookmarks.store', $topic) }}">
                    @csrf
                    <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-800 border border-blue-300 font-bold py-2 px-4 rounded text-sm flex items-center transition-colors">
                        <span class="mr-1">🔖</span> トピックを保存
                    </button>
                </form>
            @endif
            </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($topic->categories->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach($topic->categories as $category)
                                <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="inline-block px-3 py-1 text-sm font-semibold rounded bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 border border-indigo-200 dark:border-indigo-700 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <p class="whitespace-pre-wrap">{{ $topic->content }}</p>

                    <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400 flex justify-between">
                        <span>作成者: {{ $topic->user->name }}</span>
                        <span>作成日時: {{ $topic->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $posts->count() }}件の投稿</h3>
                            
                            <form method="GET" action="{{ route('topics.show', $topic) }}" class="flex space-x-2">
                                <select name="category" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                    <option value="">すべてのメディア</option>
                                    <option value="YouTube" {{ request('category') === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="X" {{ request('category') === 'X' ? 'selected' : '' }}>X</option>
                                    <option value="記事" {{ request('category') === '記事' ? 'selected' : '' }}>記事</option>
                                    <option value="知恵袋" {{ request('category') === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                                    <option value="本" {{ request('category') === '本' ? 'selected' : '' }}>本</option>
                                    <option value="その他" {{ request('category') === 'その他' ? 'selected' : '' }}>その他</option>
                                </select>

                                <select name="sort" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
                                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>人気順</option>
                                </select>
                            </form>
                            </div>

                        <div class="space-y-4">
                            @foreach ($posts as $post)
                                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            投稿者: {{ $post->user->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $post->created_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $post->category }}
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <a href="{{ $post->url }}" target="_blank" class="text-blue-500 hover:text-blue-700 hover:underline break-all text-sm">
                                            {{ $post->url }}
                                        </a>
                                    </div>

                                    @if ($post->comment)
                                        <p class="text-gray-800 dark:text-gray-200 text-sm mt-2 whitespace-pre-wrap">{{ $post->comment }}</p>
                                    @endif
                                    <div class="mt-4 flex items-center justify-between">
                                        
                                        <form method="POST" action="{{ route('likes.store', $post) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-1 text-sm {{ $post->isLikedBy(auth()->user()) ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-500' }}">
                                                <span>👍 参考になった</span>
                                                <span>({{ $post->likes->count() }})</span>
                                            </button>
                                        </form>

                                        @if ($post->user_id === auth()->id())
                                            <div class="flex space-x-2">
                                                <a href="{{ route('posts.edit', $post) }}" class="text-xs bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-2 rounded">
                                                    編集
                                                </a>
                                                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-2 rounded">
                                                        削除
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">新規投稿を追加</h3>

                        <form method="POST" action="{{ route('posts.store', $topic) }}">
                            @csrf

                            <div class="mb-4">
                                <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">参考URL（必須）</label>
                                <input type="url" name="url" id="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required placeholder="https://...">
                            </div>

                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">分類（必須）</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                                    <option value="">選択してください</option>
                                    <option value="YouTube">YouTube</option>
                                    <option value="X">X</option>
                                    <option value="記事">記事</option>
                                    <option value="知恵袋">知恵袋</option>
                                    <option value="本">本</option>
                                    <option value="その他">その他</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">コメント（任意）</label>
                                <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="URLに対するあなたの意見や補足を記入"></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    投稿する
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('topics.index') }}" class="text-blue-500 hover:text-blue-700 underline">
                            &larr; トピック一覧に戻る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>