<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリ管理
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            {{-- 新規カテゴリ追加フォーム --}}
            <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">新規カテゴリ追加</h3>
                <form method="POST" action="{{ route('categories.store') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">カテゴリ名</label>
                        <input type="text" name="name" required class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm dark:bg-[#131314] dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">親カテゴリ（中分類の場合）</label>
                        <select name="parent_id" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm dark:bg-[#131314] dark:text-white">
                            <option value="">なし（大分類）</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">表示順</label>
                        <input type="number" name="sort_order" value="0" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm w-20 dark:bg-[#131314] dark:text-white">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                        追加
                    </button>
                </form>
            </div>

            {{-- カテゴリ一覧 --}}
            <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">カテゴリ一覧</h3>
                @forelse ($categories as $category)
                    <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <form method="POST" action="{{ route('categories.update', $category) }}" class="flex gap-2 items-center">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1 text-sm dark:bg-[#131314] dark:text-white">
                                <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1 text-sm w-20 dark:bg-[#131314] dark:text-white">
                                <button type="submit" class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">更新</button>
                            </form>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('削除しますか？')" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">削除</button>
                            </form>
                        </div>

                        {{-- 中分類 --}}
                        @foreach ($category->children as $child)
                            <div class="ml-6 mt-2 flex items-center justify-between border border-gray-100 dark:border-gray-700 rounded p-2">
                                <form method="POST" action="{{ route('categories.update', $child) }}" class="flex gap-2 items-center">
                                    @csrf
                                    @method('PUT')
                                    <span class="text-xs text-gray-400 mr-1">└</span>
                                    <input type="text" name="name" value="{{ $child->name }}" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1 text-sm dark:bg-[#131314] dark:text-white">
                                    <input type="number" name="sort_order" value="{{ $child->sort_order }}" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1 text-sm w-20 dark:bg-[#131314] dark:text-white">
                                    <button type="submit" class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">更新</button>
                                </form>
                                <form method="POST" action="{{ route('categories.destroy', $child) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('削除しますか？')" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">削除</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-gray-500">カテゴリがまだありません。</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>