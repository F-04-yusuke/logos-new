<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            エビデンスの編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('posts.update', $post) }}">
                        @csrf
                        @method('PATCH')

                        <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                                <option value="YouTube" {{ old('category', $post->category) === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                <option value="X" {{ old('category', $post->category) === 'X' ? 'selected' : '' }}>X</option>
                                <option value="記事" {{ old('category', $post->category) === '記事' ? 'selected' : '' }}>記事</option>
                                <option value="知恵袋" {{ old('category', $post->category) === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                                <option value="本" {{ old('category', $post->category) === '本' ? 'selected' : '' }}>本</option>
                                <option value="その他" {{ old('category', $post->category) === 'その他' ? 'selected' : '' }}>その他</option>
                            </select>

                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">参考URL（エビデンス）</label>
                            <input type="url" name="url" id="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" value="{{ old('url', $post->url) }}" required>
                        </div>

                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">コメント・引用部分の抜粋</label>
                            <textarea name="comment" id="comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">{{ old('comment', $post->comment) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('dashboard') }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 py-2 px-4 rounded transition-colors">
                                キャンセル
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                更新する
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>