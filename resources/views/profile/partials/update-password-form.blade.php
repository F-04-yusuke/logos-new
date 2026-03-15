<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            パスワードの更新
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            アカウントのセキュリティを保つため、長くランダムなパスワードを使用してください。
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="現在のパスワード" class="dark:text-gray-300 font-bold" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="新しいパスワード" class="dark:text-gray-300 font-bold" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="新しいパスワード（確認用）" class="dark:text-gray-300 font-bold" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full dark:bg-[#131314] dark:border-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors shadow-sm">
                保存する
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400 font-bold"
                >保存しました。</p>
            @endif
        </div>
    </form>
</section>