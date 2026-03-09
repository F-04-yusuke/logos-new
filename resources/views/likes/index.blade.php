<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            参考になった一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                
                @if ($likedPosts->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                        まだ「参考になった」を押したエビデンスはありません。<br>
                        トピックの詳細画面から、役に立った情報に👍を押してみましょう！
                    </p>
                @else
                    <div class="space-y-6">
                        @foreach ($likedPosts as $post)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-shadow">
                                
                                <div class="mb-4 text-sm text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 pb-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <div>
                                        <span class="mr-1">📁 トピック:</span>
                                        <a href="{{ route('topics.show', $post->topic_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-bold hover:underline transition-colors">
                                            {{ $post->topic->title }}
                                        </a>
                                    </div>
                                    <span class="text-xs">{{ $post->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                
                                <div class="flex flex-col space-y-3">
                                    <div class="flex items-center">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2">
                                            {{ $post->category }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            投稿者: {{ $post->user->name ?? '不明' }}
                                        </span>
                                    </div>
                                    
                                    <a href="{{ $post->url }}" target="_blank" class="text-blue-500 hover:underline break-all font-medium text-sm sm:text-base">
                                        {{ $post->url }}
                                    </a>
                                    
                                    @if($post->comment)
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 p-3 rounded-md text-sm">
                                            {{ $post->comment }}
                                        </p>
                                    @endif
                                    
                                    <div class="flex items-center justify-end mt-2 pt-2">
                                        <form method="POST" action="{{ route('likes.store', $post) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-1 text-pink-500 hover:text-pink-600 transition-colors group px-2 py-1 rounded hover:bg-pink-50 dark:hover:bg-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="font-bold text-sm">{{ $post->likes_count }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>