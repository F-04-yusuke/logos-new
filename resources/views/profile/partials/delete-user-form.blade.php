<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            アカウントの削除
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            アカウントを削除すると、すべてのリソースとデータが完全に削除されます。<br>
            アカウントを削除する前に、保持しておきたいデータや情報をダウンロードしてください。
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors shadow-sm">
        アカウントを削除
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-[#1e1f20]">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                本当にアカウントを削除しますか？
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                アカウントを削除すると、すべてのリソースとデータが完全に削除されます。<br>
                アカウントを完全に削除することを確認するため、パスワードを入力してください。
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="パスワード" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full sm:w-3/4 dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-red-500 focus:ring-red-500"
                    placeholder="パスワード"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-4">
                <button type="button" x-on:click="$dispatch('close')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-bold py-2 px-4 rounded-md text-sm transition-colors">
                    キャンセル
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors shadow-sm">
                    完全に削除する
                </button>
            </div>
        </form>
    </x-modal>
</section>