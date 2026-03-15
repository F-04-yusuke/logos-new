<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center mt-2">新規アカウント作成</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label for="name" class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">アカウント名</label>
            <input id="name" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-[#131314] dark:text-white focus:border-blue-500 focus:ring-blue-500" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label for="email" class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input id="email" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-[#131314] dark:text-white focus:border-blue-500 focus:ring-blue-500" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label for="password" class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">パスワード</label>
            <input id="password" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-[#131314] dark:text-white focus:border-blue-500 focus:ring-blue-500" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-5">
            <label for="password_confirmation" class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">パスワード（確認用）</label>
            <input id="password_confirmation" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-[#131314] dark:text-white focus:border-blue-500 focus:ring-blue-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-8 pt-2 border-t border-gray-200 dark:border-gray-800">
            <a class="text-sm font-bold text-gray-500 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" href="{{ route('login') }}">
                既に登録済みですか？
            </a>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-md transition-colors shadow-sm">
                登録する
            </button>
        </div>
    </form>
</x-guest-layout>