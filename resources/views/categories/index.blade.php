<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリ管理（大分類・中分類）
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

            @if (session('status'))
                <div class="p-4 mb-4 text-sm font-bold text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- 1. 新しいカテゴリの追加カード --}}
            <div class="p-6 sm:p-8 bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-800">
                <header>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-800 pb-3">新しいカテゴリの追加</h2>
                </header>

                <form method="POST" action="{{ route('categories.store') }}" class="mt-6 space-y-6 max-w-xl">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">カテゴリ名</label>
                        <input type="text" name="name" id="name" class="block w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">表示順（数字が小さいほど上に表示されます）</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" class="block w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm" required>
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">親カテゴリ（中分類にする場合のみ選択）</label>
                        <select name="parent_id" id="parent_id" class="block w-full rounded-md border-gray-300 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            <option value="">-- なし（新しい大分類を作成する） --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-md transition-colors shadow-sm">
                            追加する
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. 現在のカテゴリ一覧カード --}}
            <div class="p-6 sm:p-8 bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-800">
                <header class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-800 pb-3">現在のカテゴリ一覧（数字で並び替え）</h2>
                </header>

                <div class="bg-gray-50 dark:bg-[#131314] rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-800">
                    @if ($categories->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">まだカテゴリが登録されていません。</p>
                    @else
                        <ul class="space-y-4">
                            @foreach ($categories as $category)
                                <li class="p-4 sm:p-5 bg-white dark:bg-[#1e1f20] rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors" x-data="{ editing: false }">
                                    
                                    {{-- 大分類の表示 --}}
                                    <div x-show="!editing" class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                        <div class="font-bold text-base sm:text-lg text-blue-600 dark:text-blue-400 flex items-center">
                                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                            {{ $category->name }}
                                            {{-- 【修正】順序バッジを視認性の高いエメラルド色に変更 --}}
                                            <span class="text-[11px] sm:text-xs font-medium ml-3 px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50 shrink-0">順序: {{ $category->sort_order }}</span>
                                        </div>
                                        {{-- 【修正】ボタンのサイズと余白を以前のゆとりある状態に戻す --}}
                                        <div class="flex space-x-2">
                                            <button @click="editing = true" class="text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 py-1 px-3 rounded transition-colors">編集</button>
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('本当に削除しますか？紐づく中分類もすべて消えます！');" class="m-0 p-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/60 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 py-1 px-3 rounded transition-colors">削除</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- 大分類の編集フォーム --}}
                                    <div x-show="editing" x-cloak class="mt-3 p-4 bg-gray-50 dark:bg-[#131314] rounded-lg border border-gray-200 dark:border-gray-800">
                                        <form method="POST" action="{{ route('categories.update', $category) }}" class="flex flex-wrap items-center gap-3">
                                            @csrf @method('PATCH')
                                            <input type="text" name="name" value="{{ $category->name }}" class="rounded border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                                            <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-20 rounded border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-sm py-1.5 focus:border-blue-500 focus:ring-blue-500" required title="表示順">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-1.5 px-4 rounded transition-colors">保存</button>
                                            <button type="button" @click="editing = false" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2 py-1.5">キャンセル</button>
                                        </form>
                                    </div>
                                    
                                    {{-- 中分類のリスト --}}
                                    @if ($category->children->isNotEmpty())
                                        <ul class="mt-4 sm:ml-7 space-y-2 border-l-2 border-gray-200 dark:border-gray-800 pl-3 sm:pl-4">
                                            @foreach ($category->children as $child)
                                                <li x-data="{ editingChild: false }" class="flex flex-col py-1">
                                                    
                                                    <div x-show="!editingChild" class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 w-full">
                                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center">
                                                            <svg aria-hidden="true" class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                            {{ $child->name }}
                                                            {{-- 【修正】順序バッジを視認性の高いエメラルド色に変更 --}}
                                                            <span class="text-[11px] font-medium ml-3 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50 shrink-0">順序: {{ $child->sort_order }}</span>
                                                        </div>
                                                        {{-- 【修正】ボタンのサイズと余白を以前のゆとりある状態に戻す --}}
                                                        <div class="flex space-x-2">
                                                            <button @click="editingChild = true" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 py-1 px-2 rounded transition-colors">編集</button>
                                                            <form method="POST" action="{{ route('categories.destroy', $child) }}" onsubmit="return confirm('削除しますか？');" class="m-0 p-0">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/60 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 py-1 px-2 rounded transition-colors">削除</button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    {{-- 中分類の編集フォーム --}}
                                                    <div x-show="editingChild" x-cloak class="mt-2 p-3 bg-gray-50 dark:bg-[#131314] rounded border border-gray-200 dark:border-gray-800">
                                                        <form method="POST" action="{{ route('categories.update', $child) }}" class="flex flex-wrap items-center gap-2 w-full">
                                                            @csrf @method('PATCH')
                                                            <input type="text" name="name" value="{{ $child->name }}" class="rounded border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                                                            <input type="number" name="sort_order" value="{{ $child->sort_order }}" class="w-16 rounded border-gray-300 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500" required title="表示順">
                                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold py-1.5 px-3 rounded transition-colors">保存</button>
                                                            <button type="button" @click="editingChild = false" class="text-[11px] font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2 py-1.5">キャンセル</button>
                                                        </form>
                                                    </div>

                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>