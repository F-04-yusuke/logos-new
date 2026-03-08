<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            トピックの編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('topics.update', $topic) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">トピックのタイトル</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" value="{{ old('title', $topic->title) }}" required>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-md border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                カテゴリを選択してください（最大2つまで）
                            </label>
                            
                            @error('category_ids')
                                <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
                            @enderror

                            @php
                                // 現在このトピックに紐づいているカテゴリIDの配列を取得（チェックを維持するため）
                                $currentCategoryIds = old('category_ids', $topic->categories->pluck('id')->toArray());
                            @endphp

                            <div class="space-y-6">
                                @foreach ($categories as $parent)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700">
                                        
                                        <div class="font-semibold text-blue-600 dark:text-blue-400 border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                                            <label class="inline-flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                                                <input type="checkbox" name="category_ids[]" value="{{ $parent->id }}" class="category-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($parent->id, $currentCategoryIds) ? 'checked' : '' }}>
                                                <span class="ml-2">📁 {{ $parent->name }} <span class="text-xs text-gray-400 font-normal">（大分類として選択）</span></span>
                                            </label>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 pl-4">
                                            @foreach ($parent->children as $child)
                                                <label class="inline-flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                                                    <input type="checkbox" name="category_ids[]" value="{{ $child->id }}" class="category-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($child->id, $currentCategoryIds) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">📄 {{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">議論の内容・背景</label>
                            <textarea name="content" id="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>{{ old('content', $topic->content) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('topics.show', $topic) }}" class="text-sm bg-gray-100 border border-gray-300 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 py-2 px-4 rounded transition-colors">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.category-checkbox');
            
            const updateCheckboxes = () => {
                const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
                if (checkedCount >= 2) {
                    checkboxes.forEach(cb => { if (!cb.checked) cb.disabled = true; });
                } else {
                    checkboxes.forEach(cb => { cb.disabled = false; });
                }
            };

            // 画面を開いた瞬間に、すでに2つ選ばれていたら残りをグレーアウトさせる
            updateCheckboxes();
            
            checkboxes.forEach(box => {
                box.addEventListener('change', updateCheckboxes);
            });
        });
    </script>
</x-app-layout>