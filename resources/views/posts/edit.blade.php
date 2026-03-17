<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">{{ session('status') }}</div>
            @endif

            {{-- Alpine.js で「下書き保存」「本投稿」の 2 ボタンを切り替え --}}
            <div x-data="{ isPublishing: false }"
                 class="bg-white dark:bg-[#1e1f20] rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-800">

                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">下書きを編集</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">下書き保存中は他のユーザーには見えません。</p>
                    </div>
                    <span class="text-[10px] font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-0.5 rounded">下書き</span>
                </div>

                <div class="p-4 sm:p-6 bg-white dark:bg-[#131314]">
                    <form method="POST" action="{{ route('posts.update', $post) }}" id="edit-post-form">
                        @csrf
                        @method('PATCH')
                        {{-- 本投稿(1) or 下書き保存(0) を Alpine.js で動的にセット --}}
                        <input type="hidden" name="is_published" :value="isPublishing ? '1' : '0'">

                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">参考URL (エビデンス)</label>
                            <input type="url" name="url" value="{{ old('url', $post->url) }}" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow" required>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">メディア分類</label>
                            <select name="category" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow cursor-pointer" required>
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
                            <textarea name="comment" rows="4" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow">{{ old('comment', $post->comment) }}</textarea>
                        </div>
                    </form>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center gap-3 bg-gray-50 dark:bg-[#1e1f20]">
                    <button type="button" onclick="history.back()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-2 px-3 sm:px-4 rounded-md text-sm transition-colors">キャンセル</button>
                    <div class="flex items-center gap-2">
                        {{-- 下書きとして保存（is_published = 0） --}}
                        <button type="button"
                            @click="isPublishing = false; $nextTick(() => document.getElementById('edit-post-form').submit())"
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 font-bold py-2 px-4 rounded-md text-sm transition-colors flex items-center gap-1">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            下書き保存
                        </button>
                        {{-- 本投稿する（is_published = 1） --}}
                        <button type="button"
                            @click="isPublishing = true; $nextTick(() => document.getElementById('edit-post-form').submit())"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors shadow-sm flex items-center gap-1.5">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            本投稿する
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>