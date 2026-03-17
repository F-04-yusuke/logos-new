<div class="flex items-center justify-between mb-3">
    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base">{{ $posts->count() }}件の投稿</h3>
    <div class="flex items-center space-x-2">
        <form method="GET" action="{{ route('topics.show', $topic) }}" class="flex space-x-2">
            {{-- 【修正】スマホでのタップしやすさを考慮し、上下のパディングを少し広げました(py-1.5 sm:py-1) --}}
            <select name="category" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1.5 sm:py-1">
                <option value="">すべてのメディア</option>
                <option value="YouTube" {{ request('category') === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                <option value="X" {{ request('category') === 'X' ? 'selected' : '' }}>X</option>
                <option value="記事" {{ request('category') === '記事' ? 'selected' : '' }}>記事</option>
                <option value="知恵袋" {{ request('category') === '知恵袋' ? 'selected' : '' }}>知恵袋</option>
                <option value="本" {{ request('category') === '本' ? 'selected' : '' }}>本</option>
                <option value="その他" {{ request('category') === 'その他' ? 'selected' : '' }}>その他</option>
            </select>
            <select name="sort" onchange="this.form.submit()" class="text-xs sm:text-sm rounded border-gray-300 dark:border-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-[#1e1f20] dark:text-white py-1.5 sm:py-1 hidden sm:block">
                <option value="popular" {{ request('sort') === 'popular' || !request('sort') ? 'selected' : '' }}>人気順</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>新着順</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>古い順</option>
            </select>
        </form>
        {{-- 【修正】スマホでのタップ領域拡大と、アクセシビリティ対応（sr-only）を追加 --}}
        <button @click="isModalOpen = true; isDraft = false" class="bg-white border border-gray-300 hover:bg-gray-50 dark:bg-[#1e1f20] dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 font-bold py-1.5 px-3 sm:py-1.5 sm:px-4 rounded text-xs sm:text-sm transition-colors flex items-center shrink-0">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="sr-only">エビデンスを投稿する</span>
            <span class="hidden sm:inline" aria-hidden="true">投稿する</span>
        </button>
    </div>
</div>

<div class="space-y-3">
    @foreach ($posts as $post)
        {{-- 【修正】先ほど作成した共通コンポーネントを呼び出すだけ --}}
        <x-post-card :post="$post" />
    @endforeach
</div>