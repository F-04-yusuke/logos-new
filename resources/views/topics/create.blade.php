<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            新規トピック作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e1f20] overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="POST" action="{{ route('topics.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">トピックのタイトル</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 dark:bg-[#131314] rounded-md border border-gray-200 dark:border-gray-800">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">カテゴリを選択してください（最大2つまで）</label>
                            
                            @error('category_ids')
                                <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
                            @enderror

                            <div class="space-y-4">
                                @foreach ($categories as $parent)
                                    <div class="bg-white dark:bg-[#1e1f20] p-3 rounded border border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-blue-600 dark:text-blue-400 border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="category_ids[]" value="{{ $parent->id }}" class="category-checkbox rounded border-gray-300 dark:bg-[#131314] dark:border-gray-600 text-blue-600 focus:ring-blue-500" {{ in_array($parent->id, old('category_ids', [])) ? 'checked' : '' }}>
                                                <span class="ml-2">📁 {{ $parent->name }}</span>
                                            </label>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 pl-4">
                                            @foreach ($parent->children as $child)
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="category_ids[]" value="{{ $child->id }}" class="category-checkbox rounded border-gray-300 dark:bg-[#131314] dark:border-gray-600 text-blue-600 focus:ring-blue-500" {{ in_array($child->id, old('category_ids', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">📄 {{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">議論の内容・背景（概要）</label>
                            {{-- 【修正】デザインはそのまま、自動拡張機能のみ追加 --}}
                            <textarea name="content" rows="6" 
                                x-data x-init="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 overflow-hidden resize-none" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-6" x-data="{
                            items: ({{ old('timeline') ? json_encode(old('timeline')) : '[]' }}).map(i => {
                                return { ...i, is_ai: i.is_ai === false ? false : true };
                            }),
                            addItem() { this.items.push({ date: '', event: '', is_ai: false }); },
                            removeItem(index) { this.items.splice(index, 1); },
                            markAsEdited(item) { item.is_ai = false; }
                        }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">前提となる時系列</label>
                            <p class="text-xs text-gray-500 mb-3">※作成後にトピック詳細画面でAIに自動生成させることも可能です。</p>
                            
                            <div class="space-y-2 border-l-2 border-gray-200 dark:border-gray-700 pl-3 ml-2">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2 relative">
                                        <div class="hidden sm:block absolute left-[-17.5px] top-3 w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                                        
                                        <input type="text" :name="'timeline_date['+index+']'" x-model="item.date" @input="markAsEdited(item)" placeholder="202X年X月" class="w-full sm:w-1/4 rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500 py-1.5">
                                        
                                        {{-- 【修正】デザインはそのまま、自動拡張機能のみ追加 --}}
                                        <textarea :name="'timeline_event['+index+']'" x-model="item.event" @input="markAsEdited(item); $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' })" placeholder="出来事の要約" rows="1" class="w-full sm:flex-1 rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500 py-1.5 overflow-hidden resize-none"></textarea>
                                        
                                        <input type="hidden" :name="'timeline_is_ai['+index+']'" :value="item.is_ai ? 1 : 0">
                                        
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 px-2 py-1.5 text-sm shrink-0">削除</button>
                                    </div>
                                </template>
                            </div>
                            
                            <button type="button" @click="addItem()" class="mt-3 text-xs font-bold text-blue-500 hover:text-blue-700 flex items-center">
                                ＋ 新しい行を追加する
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-4 border-t border-gray-200 dark:border-gray-800 pt-4">
                            <a href="{{ route('topics.index') }}" class="mr-4 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">キャンセル</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md transition-colors">
                                トピックを作成する
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
            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
                    if (checkedCount >= 2) {
                        checkboxes.forEach(cb => { if (!cb.checked) cb.disabled = true; });
                    } else {
                        checkboxes.forEach(cb => { cb.disabled = false; });
                    }
                });
            });
        });
    </script>
</x-app-layout>