<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">エビデンスの編集</h3>
                </div>

                <div class="p-6 bg-white dark:bg-[#131314]">
                    <form method="POST" action="{{ route('posts.update', $post) }}" id="edit-post-form">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">参考URL (エビデンス)</label>
                            <input type="url" name="url" value="{{ old('url', $post->url) }}" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">メディア分類</label>
                            <select name="category" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                <option value="YouTube" {{ $post->category === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                                <option value="X" {{ $post->category === 'X' ? 'selected' : '' }}>X</option>
                                <option value="記事" {{ $post->category === '記事' ? 'selected' : '' }}>記事</option>
                                <option value="知恵袋" {{ $post->category === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                                <option value="本" {{ $post->category === '本' ? 'selected' : '' }}>本</option>
                                <option value="その他" {{ $post->category === 'その他' ? 'selected' : '' }}>その他</option>
                            </select>
                        </div>
                        
                        <div class="mb-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">コメント・引用部分の抜粋</label>
                            <textarea name="comment" rows="4" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('comment', $post->comment) }}</textarea>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                    <button type="button" onclick="history.back()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-2 px-4 rounded-md text-sm transition-colors">キャンセル</button>
                    <button type="submit" form="edit-post-form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">更新する</button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>