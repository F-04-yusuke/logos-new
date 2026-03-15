<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e1f20] rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">分析・図解の編集</h3>
                </div>

                <div class="p-4 sm:p-6 bg-white dark:bg-[#131314]">
                    <form method="POST" action="{{ route('analyses.update', $analysis) }}" id="edit-analysis-form">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">図解・ツールのタイトル</label>
                            <input type="text" name="title" value="{{ old('title', $analysis->title) }}" class="w-full rounded-md bg-gray-50 border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm transition-shadow" required>
                        </div>

                        <div class="mb-2 p-4 bg-gray-50 dark:bg-[#1e1f20] rounded border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-2 flex items-center">
                                <span aria-hidden="true" class="mr-1">💡</span> ツール種別: <span class="text-blue-500 ml-1">{{ strtoupper($analysis->type) }}</span>
                            </p>
                            <p class="text-xs text-gray-400 leading-relaxed">
                                ※MVP版では、データ破損を防ぐため「タイトル」のみ変更可能です。<br>
                                ツールの中身（ツリーや表のデータ）を再編集することはできません。内容を修正したい場合は、お手数ですがダッシュボードから新しくツールを作成してください。
                            </p>
                        </div>
                    </form>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e1f20]">
                    <button type="button" onclick="history.back()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-2 px-3 sm:px-4 rounded-md text-sm transition-colors">キャンセル</button>
                    <button type="submit" form="edit-analysis-form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors shadow-sm">更新する</button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>