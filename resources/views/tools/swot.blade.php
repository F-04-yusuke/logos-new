<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                SWOT分析作成 (PRO)
            </h2>
            <button onclick="saveSwot()" id="save-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors shadow-sm">
                分析を保存する
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <style>textarea { resize: none; overflow: hidden; }</style>

            <div class="flex flex-col gap-6">
                <div class="border-b border-gray-200 dark:border-gray-800 pb-4 flex justify-between items-end">
                    <p class="text-sm text-gray-600 dark:text-gray-400">内部要因（強み・弱み）と外部要因（機会・脅威）を整理する定番フレームワークです。</p>
                    <button onclick="generateWithAI()" id="ai-btn" class="text-xs font-bold text-white transition-colors flex items-center bg-blue-600 hover:bg-blue-500 px-3 py-1.5 rounded shadow-md h-fit">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AIでSWOTを自動生成
                    </button>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm dark:shadow-none">
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">分析テーマ（主題）</div>
                    <input type="text" id="theme-input" value="" class="w-full bg-transparent dark:bg-[#1e1f20] font-bold text-xl text-gray-900 dark:text-gray-100 focus:outline-none focus:border-b border-blue-500 placeholder-gray-400 dark:placeholder-gray-600" placeholder="例：日本の原発再稼働について">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-blue-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center">
                                <span class="text-2xl mr-2">S</span>trengths <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">強み (内部要因)</span>
                            </h2>
                        </div>
                        <div id="list-s" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-s')" class="text-xs font-bold text-gray-400 hover:text-blue-500 transition-colors flex items-center mt-auto pt-2"><span class="mr-1">＋</span> 項目を追加</button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-red-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center">
                                <span class="text-2xl mr-2">W</span>eaknesses <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">弱み (内部要因)</span>
                            </h2>
                        </div>
                        <div id="list-w" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-w')" class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors flex items-center mt-auto pt-2"><span class="mr-1">＋</span> 項目を追加</button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-green-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 class="text-lg font-bold text-green-600 dark:text-green-400 flex items-center">
                                <span class="text-2xl mr-2">O</span>pportunities <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">機会 (外部要因)</span>
                            </h2>
                        </div>
                        <div id="list-o" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-o')" class="text-xs font-bold text-gray-400 hover:text-green-500 transition-colors flex items-center mt-auto pt-2"><span class="mr-1">＋</span> 項目を追加</button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-yellow-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 class="text-lg font-bold text-yellow-600 dark:text-yellow-400 flex items-center">
                                <span class="text-2xl mr-2">T</span>hreats <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">脅威 (外部要因)</span>
                            </h2>
                        </div>
                        <div id="list-t" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-t')" class="text-xs font-bold text-gray-400 hover:text-yellow-500 transition-colors flex items-center mt-auto pt-2"><span class="mr-1">＋</span> 項目を追加</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function autoResize(textarea) { textarea.style.height = 'auto'; textarea.style.height = (textarea.scrollHeight) + 'px'; }

        function addItem(listId, text = "", isAI = false) {
            const container = document.getElementById(listId);
            const aiBadge = isAI ? `<span class="ai-badge ml-2 text-[9px] border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#131314] text-gray-500 px-1 py-0.5 rounded shrink-0 transition-opacity duration-300">AI生成</span>` : '';
            const html = `
                <div class="relative group flex items-start bg-gray-50 dark:bg-[#131314] p-2 rounded border border-gray-200 dark:border-gray-800 focus-within:border-gray-400 dark:focus-within:border-gray-500 transition-colors">
                    <span class="text-gray-400 dark:text-gray-500 mr-2 mt-0.5">•</span>
                    <textarea oninput="autoResize(this); removeAiBadge(this)" class="w-full bg-transparent dark:bg-[#131314] text-sm text-gray-800 dark:text-gray-200 focus:outline-none" rows="1" placeholder="内容を入力...">${text}</textarea>
                    ${aiBadge}
                    <button onclick="this.closest('.relative').remove()" class="ml-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold shrink-0 mt-0.5">✕</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            const textareas = container.querySelectorAll('textarea');
            if(textareas.length > 0) autoResize(textareas[textareas.length - 1]);
        }

        function removeAiBadge(textarea) {
            const badge = textarea.parentElement.querySelector('.ai-badge');
            if (badge) { badge.style.opacity = '0'; setTimeout(() => badge.remove(), 300); }
        }

        function generateWithAI() {
            const btn = document.getElementById('ai-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="animate-pulse">AIが多角的に分析中...</span>`;
            btn.classList.add('opacity-70', 'cursor-not-allowed');

            ['list-s', 'list-w', 'list-o', 'list-t'].forEach(id => document.getElementById(id).innerHTML = '');
            document.getElementById('theme-input').value = "日本の原発再稼働について";

            setTimeout(() => {
                addItem('list-s', 'ベースロード電源としての安定した供給能力', true);
                addItem('list-s', '発電時のCO2排出量が極めて少ない', true);
                addItem('list-w', '過酷事故発生時の国土に対する壊滅的なリスク', true);
                addItem('list-w', '高レベル放射性廃棄物（核のゴミ）の最終処分場が未定', true);
                addItem('list-o', '生成AI普及やデータセンター増設に伴う電力需要の爆発的増加', true);
                addItem('list-o', '中東情勢の不安定化による化石燃料の価格高騰リスクの回避', true);
                addItem('list-t', '日本特有の巨大地震・津波などの予期せぬ自然災害', true);
                addItem('list-t', 'ミサイルやテロリストによる原発施設への直接攻撃リスク', true);

                btn.innerHTML = originalText;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
            }, 1000);
        }

        // 🌟 リスト内のテキストを配列で取得する関数
        function getListData(listId) {
            const container = document.getElementById(listId);
            const textareas = container.querySelectorAll('textarea');
            const data = [];
            textareas.forEach(ta => {
                if (ta.value.trim() !== '') {
                    data.push(ta.value.trim());
                }
            });
            return data;
        }

        // 🌟 データベースに送信する処理
        function saveSwot() {
            const btn = document.getElementById('save-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '保存中...';
            btn.disabled = true;

            // テーマと4つの領域のデータを取得
            const theme = document.getElementById('theme-input').value.trim() || '未設定のテーマ';
            const swotData = {
                theme: theme,
                strengths: getListData('list-s'),
                weaknesses: getListData('list-w'),
                opportunities: getListData('list-o'),
                threats: getListData('list-t')
            };

            const title = theme !== '未設定のテーマ' ? 'SWOT: ' + theme : 'SWOT分析 (' + new Date().toLocaleDateString() + ')';

            fetch('{{ route("tools.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: title,
                    type: 'swot',
                    data: swotData
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('保存に失敗しました。');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</x-app-layout>