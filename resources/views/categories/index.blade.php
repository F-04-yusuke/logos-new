<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリ管理（大分類・中分類）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">新しいカテゴリの追加</h2>
                </header>

                <form method="POST" action="{{ route('categories.store') }}" class="mt-6 space-y-6 max-w-xl">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">カテゴリ名</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">表示順（数字が小さいほど上に表示されます）</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">親カテゴリ（中分類にする場合のみ選択）</label>
                        <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            <option value="">-- なし（新しい大分類を作成する） --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            追加する
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header class="mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">現在のカテゴリ一覧（ドラッグ不要・数字で並び替え）</h2>
                </header>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    @if ($categories->isEmpty())
                        <p class="text-sm text-gray-500">まだカテゴリが登録されていません。</p>
                    @else
                        <ul class="space-y-4">
                            @foreach ($categories as $category)
                                <li class="p-4 bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700" x-data="{ editing: false }">
                                    
                                    <div x-show="!editing" class="flex justify-between items-center">
                                        <div class="font-bold text-lg text-blue-600 dark:text-blue-400">
                                            📁 {{ $category->name }} <span class="text-xs text-gray-400 font-normal ml-2">（順序: {{ $category->sort_order }}）</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="editing = true" class="text-sm bg-gray-100 hover:bg-gray-200 border text-gray-600 py-1 px-3 rounded">編集</button>
                                            <form method=\"POST\" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('本当に削除しますか？紐づく中分類もすべて消えます！');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-100 hover:bg-red-200 border text-red-600 py-1 px-3 rounded">削除</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div x-show="editing" x-cloak class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                                        <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center space-x-3">
                                            @csrf @method('PATCH')
                                            <input type="text" name="name" value="{{ $category->name }}" class="rounded border-gray-300 text-sm" required>
                                            <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-20 rounded border-gray-300 text-sm" required>
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-3 rounded">保存</button>
                                            <button type="button" @click="editing = false" class="text-sm text-gray-500 hover:underline">キャンセル</button>
                                        </form>
                                    </div>
                                    
                                    @if ($category->children->isNotEmpty())
                                        <ul class="mt-3 ml-6 space-y-2 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                                            @foreach ($category->children as $child)
                                                <li x-data="{ editingChild: false }" class="flex flex-col justify-center">
                                                    
                                                    <div x-show="!editingChild" class="flex justify-between items-center w-full">
                                                        <div class="text-sm text-gray-700 dark:text-gray-300 flex items-center">
                                                            <span class="mr-2 text-gray-400">└</span> 📄 {{ $child->name }} <span class="text-xs text-gray-400 font-normal ml-2">（順序: {{ $child->sort_order }}）</span>
                                                        </div>
                                                        <div class="flex space-x-2">
                                                            <button @click="editingChild = true" class="text-xs bg-gray-100 hover:bg-gray-200 border text-gray-600 py-1 px-2 rounded">編集</button>
                                                            <form method=\"POST\" action="{{ route('categories.destroy', $child) }}" onsubmit="return confirm('削除しますか？');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 border text-red-600 py-1 px-2 rounded">削除</button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <div x-show="editingChild" x-cloak class="mt-1 flex items-center space-x-3">
                                                        <form method="POST" action="{{ route('categories.update', $child) }}" class="flex items-center space-x-3 w-full">
                                                            @csrf @method('PATCH')
                                                            <span class="text-gray-400">└</span>
                                                            <input type="text" name="name" value="{{ $child->name }}" class="rounded border-gray-300 text-sm py-1" required>
                                                            <input type="number" name="sort_order" value="{{ $child->sort_order }}" class="w-16 rounded border-gray-300 text-sm py-1" required>
                                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-2 rounded">保存</button>
                                                            <button type="button" @click="editingChild = false" class="text-xs text-gray-500 hover:underline">キャンセル</button>
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