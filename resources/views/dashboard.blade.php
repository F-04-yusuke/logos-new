<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            マイページ（ダッシュボード）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">自分が作成したトピック</h2>
                    </header>
                    <div class="mt-6 space-y-4">
                        @if ($myTopics->isEmpty())
                            <p class="text-sm text-gray-500">まだ作成したトピックはありません。</p>
                        @else
                            @foreach ($myTopics as $topic)
                                <div class="p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('topics.show', $topic) }}" class="text-blue-500 hover:underline font-bold text-lg">{{ $topic->title }}</a>
                                        <p class="text-xs text-gray-500 mt-2">作成日: {{ $topic->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('topics.edit', $topic) }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">編集</a>
                                        <form method="POST" action="{{ route('topics.destroy', $topic) }}" onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">削除</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">自分が投稿したエビデンス</h2>
                    </header>
                    <div class="mt-6 space-y-4">
                        @if ($myPosts->isEmpty())
                            <p class="text-sm text-gray-500">まだ投稿したエビデンスはありません。</p>
                        @else
                            @foreach ($myPosts as $post)
                                <div class="p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 mb-2">
                                            投稿先: <a href="{{ route('topics.show', $post->topic) }}" class="text-blue-400 hover:underline">{{ $post->topic->title }}</a>
                                        </div>
                                        <div class="mb-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $post->category }}</span>
                                        </div>
                                        <a href="{{ $post->url }}" target="_blank" class="block text-blue-500 hover:underline break-all text-sm mb-2">{{ $post->url }}</a>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap mt-2">{{ $post->comment }}</p>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <a href="{{ route('posts.edit', $post) }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">編集</a>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 py-1 px-3 rounded">削除</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>