{{--
    x-pro-modal コンポーネント

    使い方:
      グローバルイベントで開く:
        @click="$dispatch('open-pro-modal')"

      コンテキストを渡す（オプション）:
        @click="$dispatch('open-pro-modal', { feature: 'ロジックツリー分析' })"

    このコンポーネントは <x-app-layout> 内（app.blade.php）に1つだけ配置する。
--}}
<div
    x-data="{
        show: false,
        feature: '分析ツール',
        open(detail) {
            this.feature = detail?.feature ?? '分析ツール';
            this.show = true;
        }
    }"
    @open-pro-modal.window="open($event.detail)"
    x-cloak
>
    {{-- オーバーレイ --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center px-4"
        @click.self="show = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pro-modal-title"
    >
        {{-- モーダル本体 --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-[#1e1f20] border border-gray-700 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden"
        >
            {{-- ヘッダー部 --}}
            <div class="relative bg-gradient-to-br from-yellow-500/20 to-transparent px-6 pt-6 pb-5 border-b border-gray-700/50">
                <button
                    @click="show = false"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-300 transition-colors"
                    aria-label="閉じる"
                >
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-yellow-500/20 border border-yellow-500/40">
                        <svg aria-hidden="true" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span class="text-xs font-black tracking-wider text-yellow-400 uppercase">PRO会員限定</span>
                </div>

                <h2 id="pro-modal-title" class="text-lg font-bold text-gray-100">
                    <span x-text="feature"></span>はPRO会員専用です
                </h2>
                <p class="mt-1 text-sm text-gray-400">
                    この機能を使うにはPROプランへのアップグレードが必要です。
                </p>
            </div>

            {{-- ベネフィットリスト --}}
            <div class="px-6 py-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">PROプランでできること</p>
                <ul class="space-y-3">
                    @foreach([
                        ['icon' => 'tree',   'color' => 'text-blue-400',   'text' => 'ロジックツリーで論点を構造化'],
                        ['icon' => 'matrix', 'color' => 'text-purple-400', 'text' => '総合評価表で選択肢を◎〇△×で比較'],
                        ['icon' => 'swot',   'color' => 'text-green-400',  'text' => 'SWOT / PEST分析でリスクを整理'],
                        ['icon' => 'ai',     'color' => 'text-yellow-400', 'text' => 'Gemini AIによる分析アシスト'],
                        ['icon' => 'topic',  'color' => 'text-red-400',    'text' => 'トピックの新規作成と図解の公開'],
                    ] as $item)
                    <li class="flex items-center gap-3 text-sm text-gray-300">
                        <span class="shrink-0 h-5 w-5 {{ $item['color'] }}">
                            @if($item['icon'] === 'tree')
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @elseif($item['icon'] === 'matrix')
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            @elseif($item['icon'] === 'swot')
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            @elseif($item['icon'] === 'ai')
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                            @else
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            @endif
                        </span>
                        {{ $item['text'] }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- CTAボタン --}}
            <div class="px-6 pb-6">
                {{-- フェーズ4でStripe Payment LinkのURLに差し替える --}}
                <a
                    href="{{ route('topics.index') }}"
                    class="block w-full text-center bg-yellow-500 hover:bg-yellow-400 text-white font-bold py-3 px-4 rounded-xl transition-colors text-sm shadow-lg shadow-yellow-500/20"
                >
                    PROプランにアップグレードする
                </a>
                <button
                    @click="show = false"
                    class="mt-3 block w-full text-center text-xs text-gray-500 hover:text-gray-300 transition-colors py-1"
                >
                    今はしない
                </button>
            </div>
        </div>
    </div>
</div>
