<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            プロフィール情報
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            プロフィール画像、アカウント名、メールアドレスを更新できます。
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @php
            $user = auth()->user();
            $lastUpdated = $user->name_updated_at ? \Carbon\Carbon::parse($user->name_updated_at) : null;
            $canChangeName = !$lastUpdated || $lastUpdated->copy()->addDays(7)->isPast();
            $daysPassed = $lastUpdated ? (int) $lastUpdated->diffInDays(now()) : 0;
            $daysLeft = $canChangeName ? 0 : (7 - $daysPassed);
        @endphp

        <div class="flex items-center gap-4">
            <div class="shrink-0">
                @if ($user->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="h-16 w-16 object-cover rounded-full border border-gray-200 dark:border-gray-700">
                @else
                    <div id="avatar-preview" class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                        <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <x-input-label for="avatar" :value="__('プロフィール画像')" class="dark:text-gray-300 font-bold mb-1" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400 transition-colors cursor-pointer" onchange="previewImage(event)" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('アカウント名')" class="dark:text-gray-300 font-bold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full dark:bg-[#131314] dark:border-gray-700 dark:text-white" :value="old('name', $user->name)" required autofocus autocomplete="name" :readonly="!$canChangeName" :class="!$canChangeName ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : ''" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
            
            @if(!$canChangeName)
                <p class="mt-2 text-xs font-bold text-red-500">※アカウント名は前回の変更から7日間変更できません。（残り約{{ $daysLeft }}日）</p>
            @else
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">※一度変更すると、その後7日間は再変更できなくなります。</p>
            @endif
        </div>

        <div>
            <x-input-label for="email" :value="__('Email (ログイン用)')" class="dark:text-gray-300 font-bold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full dark:bg-[#131314] dark:border-gray-700 dark:text-white" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">
                保存する
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400 font-bold">保存しました。</p>
            @endif
        </div>
    </form>
</section>

<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                if(preview.tagName.toLowerCase() === 'img') {
                    preview.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.id = 'avatar-preview';
                    img.src = e.target.result;
                    img.className = 'h-16 w-16 object-cover rounded-full border border-gray-200 dark:border-gray-700';
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>