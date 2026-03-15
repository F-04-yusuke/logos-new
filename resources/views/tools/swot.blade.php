<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
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
                {{-- 元の美しいレイアウト --}}
                <div class="border-b border-gray-200 dark:border-gray-800 pb-4 flex justify-between items-end px-4 sm:px-0">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">内部要因・外部要因、またはマクロ環境を整理するフレームワークです。</p>
                        <select id="framework-select" onchange="toggleFramework()" class="bg-white dark:bg-[#131314] border border-gray-300 dark:border-gray-700 rounded text-gray-900 dark:text-gray-100 text-sm px-2 py-1 focus:outline-none focus:border-blue-500 font-bold">
                            <option value="SWOT">SWOT分析 (強み・弱み・機会・脅威)</option>
                            <option value="PEST">PEST分析 (政治・経済・社会・技術)</option>
                        </select>
                    </div>
                    <button onclick="generateWithAI()" id="ai-btn" class="text-xs font-bold text-white transition-colors flex items-center bg-blue-600 hover:bg-blue-500 px-3 py-1.5 rounded shadow-md h-fit shrink-0">
                        <svg aria-hidden="true" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AIで自動生成
                    </button>
                </div>

                <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm dark:shadow-none">
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">分析テーマ（主題）</div>
                    <input type="text" id="theme-input" value="" class="w-full bg-transparent dark:bg-[#1e1f20] font-bold text-xl text-gray-900 dark:text-gray-100 focus:outline-none focus:border-b border-blue-500 placeholder-gray-400 dark:placeholder-gray-600 py-1" placeholder="例：日本の原発再稼働について">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-blue-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 id="label-box1" class="text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center">
                                <span class="text-2xl mr-2" aria-hidden="true">S</span>trengths <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">強み (内部要因)</span>
                            </h2>
                        </div>
                        <div id="list-s" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-s')" class="text-xs font-bold text-gray-400 hover:text-blue-500 transition-colors flex items-center mt-auto pt-2 py-1 pr-2 w-fit"><span aria-hidden="true" class="mr-1">＋</span> 項目を追加</button>
                    </div>                    

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-red-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 id="label-box2" class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center">
                                <span class="text-2xl mr-2" aria-hidden="true">W</span>eaknesses <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">弱み (内部要因)</span>
                            </h2>
                        </div>
                        <div id="list-w" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-w')" class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors flex items-center mt-auto pt-2 py-1 pr-2 w-fit"><span aria-hidden="true" class="mr-1">＋</span> 項目を追加</button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-green-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 id="label-box3" class="text-lg font-bold text-green-600 dark:text-green-400 flex items-center">
                                <span class="text-2xl mr-2" aria-hidden="true">O</span>pportunities <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">機会 (外部要因)</span>
                            </h2>
                        </div>
                        <div id="list-o" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-o')" class="text-xs font-bold text-gray-400 hover:text-green-500 transition-colors flex items-center mt-auto pt-2 py-1 pr-2 w-fit"><span aria-hidden="true" class="mr-1">＋</span> 項目を追加</button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border-t-4 border-yellow-500 rounded-lg p-4 shadow-sm dark:shadow-lg flex flex-col h-full border-x border-b dark:border-transparent border-gray-200">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <h2 id="label-box4" class="text-lg font-bold text-yellow-600 dark:text-yellow-400 flex items-center">
                                <span class="text-2xl mr-2" aria-hidden="true">T</span>hreats <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">脅威 (外部要因)</span>
                            </h2>
                        </div>
                        <div id="list-t" class="flex-1 space-y-2 mb-3"></div>
                        <button onclick="addItem('list-t')" class="text-xs font-bold text-gray-400 hover:text-yellow-500 transition-colors flex items-center mt-auto pt-2 py-1 pr-2 w-fit"><span aria-hidden="true" class="mr-1">＋</span> 項目を追加</button>
                    </div>
                </div>
                
                <div class="mt-4 border-t border-gray-200 dark:border-gray-800 pt-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-gray-900 dark:text-gray-100">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AI SWOT・アシスタント
                    </h2>
                    <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-xl flex flex-col h-[350px] shadow-sm dark:shadow-lg overflow-hidden">
                        <div id="chat-history" class="overflow-y-auto p-4 space-y-4 flex-1" style="scrollbar-width: thin;">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg aria-hidden="true" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                                <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%] leading-relaxed">
                                    「この強みを活かした戦略を提案して」「弱みを克服するアイデアは？」など、作成したSWOTをもとにAIと議論できます。
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191a]">
                            <div class="flex gap-2 items-end">
                                <textarea id="ai-input" oninput="autoResize(this)" class="flex-1 bg-white dark:bg-[#131314] border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 text-sm p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 max-h-32 transition-shadow" rows="1" placeholder="AIへの指示や質問を入力..."></textarea>
                                <button onclick="sendAiMessage()" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-md shrink-0">送信</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($analysis) && !empty($analysis->data))
                const savedData = @json($analysis->data);
                
                const themeInput = document.getElementById('swot-theme-input') || document.getElementById('theme-input');
                if(savedData.theme && themeInput) {
                    themeInput.value = savedData.theme;
                }

                if(savedData.meta) {
                    const urlInput = document.getElementById('info-url');
                    const descInput = document.getElementById('info-desc');
                    if (urlInput) urlInput.value = savedData.meta.url || '';
                    if (descInput) descInput.value = savedData.meta.description || '';
                }

                if(savedData.framework) {
                    const frameworkSelect = document.getElementById('framework-select');
                    if(frameworkSelect) {
                        frameworkSelect.value = savedData.framework;
                        if(typeof toggleFramework === 'function') toggleFramework();
                    }
                }

                // BOX1~4の復元
                for(let i=1; i<=4; i++) {
                    let boxList = document.getElementById(`box${i}-list`) || document.getElementById(`list-${['s','w','o','t'][i-1]}`);
                    const key = `box${i}`;
                    
                    if(boxList && savedData[key] && savedData[key].length > 0) {
                        boxList.innerHTML = ''; 
                        savedData[key].forEach(text => {
                            // 復元時も手動追加時と同じ美しい「・」とレイアウトを使用する
                            const div = document.createElement('div');
                            div.className = "relative group flex items-start bg-gray-50 dark:bg-[#131314] p-2 rounded border border-gray-200 dark:border-gray-800 focus-within:border-gray-400 dark:focus-within:border-gray-500 transition-colors mt-2";
                            div.innerHTML = `
                                <span aria-hidden="true" class="text-gray-400 dark:text-gray-500 mr-2 mt-0.5">•</span>
                                <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] text-[15px] sm:text-sm text-gray-800 dark:text-gray-200 focus:outline-none border-none focus:ring-0 p-0 resize-none leading-relaxed" rows="1">${text}</textarea>
                                <button type="button" onclick="this.closest('.relative').remove()" class="ml-2 text-gray-400 hover:text-red-500 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity text-[10px] font-bold shrink-0 mt-0.5 px-2 py-1 rounded flex items-center justify-center"><span class="sr-only">削除</span>✕</button>
                            `;
                            boxList.appendChild(div);
                        });
                    }
                }
                setTimeout(() => document.querySelectorAll('textarea').forEach(t => autoResize(t)), 100);
            @endif
        });

        function autoResize(textarea) { textarea.style.height = 'auto'; textarea.style.height = (textarea.scrollHeight) + 'px'; }

        function addItem(listId, text = "", isAI = false) {
            const container = document.getElementById(listId);
            const aiBadge = isAI ? `<span class="ai-badge ml-2 text-[9px] border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#131314] text-gray-500 px-1 py-0.5 rounded shrink-0 transition-opacity duration-300 mt-1">AI生成</span>` : '';
            const html = `
                <div class="relative group flex items-start bg-gray-50 dark:bg-[#131314] p-2 rounded border border-gray-200 dark:border-gray-800 focus-within:border-gray-400 dark:focus-within:border-gray-500 transition-colors mt-2">
                    <span aria-hidden="true" class="text-gray-400 dark:text-gray-500 mr-2 mt-0.5">•</span>
                    <textarea oninput="autoResize(this); removeAiBadge(this)" class="w-full bg-transparent dark:bg-[#131314] text-[15px] sm:text-sm text-gray-800 dark:text-gray-200 focus:outline-none border-none focus:ring-0 p-0 resize-none leading-relaxed" rows="1" placeholder="内容を入力...">${text}</textarea>
                    ${aiBadge}
                    <button type="button" onclick="this.closest('.relative').remove()" class="ml-2 text-gray-400 hover:text-red-500 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity text-[10px] font-bold shrink-0 mt-0.5 px-2 py-1 rounded flex items-center justify-center"><span class="sr-only">削除</span>✕</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            const textareas = container.querySelectorAll('textarea');
            if(textareas.length > 0) autoResize(textareas[textareas.length - 1]);
        }

        function removeAiBadge(textarea) {
            const badge = textarea.parentElement.querySelector('.ai-badge');
            if (badge) { badge.style.opacity = '0'; setTimeout(() => badge.remove(), 300); }
        }

        // プルダウン変更時にタイトルを切り替える
        function toggleFramework() {
            const fw = document.getElementById('framework-select').value;
            if (fw === 'SWOT') {
                document.getElementById('label-box1').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">S</span>trengths <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">強み (内部要因)</span>';
                document.getElementById('label-box2').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">W</span>eaknesses <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">弱み (内部要因)</span>';
                document.getElementById('label-box3').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">O</span>pportunities <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">機会 (外部要因)</span>';
                document.getElementById('label-box4').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">T</span>hreats <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">脅威 (外部要因)</span>';
            } else {
                document.getElementById('label-box1').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">P</span>olitics <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">政治</span>';
                document.getElementById('label-box2').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">E</span>conomy <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">経済</span>';
                document.getElementById('label-box3').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">S</span>ociety <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">社会</span>';
                document.getElementById('label-box4').innerHTML = '<span class="text-2xl mr-2" aria-hidden="true">T</span>echnology <span class="text-sm text-gray-500 dark:text-gray-400 ml-2 font-normal">技術</span>';
            }
        }

        // SWOT/PESTを判定してAIに指示を出す
        function generateWithAI() {
            const btn = document.getElementById('ai-btn');
            const originalText = btn.innerHTML;
            const themeInput = document.getElementById('theme-input');
            const theme = themeInput.value.trim();
            const fw = document.getElementById('framework-select').value;

            if (!theme) {
                alert("AIに分析させるテーマ（主題）を入力してください。");
                themeInput.focus();
                return;
            }

            btn.innerHTML = `<span class="animate-pulse">AIが多角的に分析中...</span>`;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            btn.disabled = true;

            ['list-s', 'list-w', 'list-o', 'list-t'].forEach(id => document.getElementById(id).innerHTML = '');

            const promptText = fw === 'PEST' ? `
                テーマ: 「${theme}」
                このテーマについてPEST分析を行ってください。
                出力は必ず以下のJSON形式のみとし、他のテキストは一切含めないでください。
                {"box1":["政治的要因1","政治的要因2"],"box2":["経済的要因1","経済的要因2"],"box3":["社会的要因1","社会的要因2"],"box4":["技術的要因1","技術的要因2"]}
            ` : `
                テーマ: 「${theme}」
                このテーマについてSWOT分析を行ってください。
                出力は必ず以下のJSON形式のみとし、他のテキストは一切含めないでください。
                {"box1":["強み1","強み2"],"box2":["弱み1","弱み2"],"box3":["機会1","機会2"],"box4":["脅威1","脅威2"]}
            `;

            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ prompt: promptText, context: "JSONのみ出力" })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                const jsonMatch = data.reply.match(/\{[\s\S]*\}/);
                if (!jsonMatch) throw new Error("JSON抽出失敗");
                
                const parsedData = JSON.parse(jsonMatch[0]);
                if (parsedData.box1) parsedData.box1.forEach(txt => addItem('list-s', txt, true));
                if (parsedData.box2) parsedData.box2.forEach(txt => addItem('list-w', txt, true));
                if (parsedData.box3) parsedData.box3.forEach(txt => addItem('list-o', txt, true));
                if (parsedData.box4) parsedData.box4.forEach(txt => addItem('list-t', txt, true));
                
                setTimeout(() => {
                    document.querySelectorAll('textarea').forEach(t => autoResize(t));
                }, 100);
            })
            .catch(err => {
                console.error(err);
                alert("AIの分析に失敗しました。もう一度お試しください。");
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                btn.disabled = false;
            });
        }

        // 保存通信
        function saveSwot() {
            const btn = document.getElementById('save-btn');
            if(!btn) return;
            const originalText = btn.innerHTML;
            btn.innerHTML = '保存中...';
            btn.disabled = true;

            const fwSelect = document.getElementById('framework-select');
            const fw = fwSelect ? fwSelect.value : 'SWOT';
            
            const themeInput = document.getElementById('swot-theme-input') || document.getElementById('theme-input');
            const theme = themeInput ? themeInput.value.trim() : '';
            
            const urlInput = document.getElementById('info-url');
            const descInput = document.getElementById('info-desc');
            const url = urlInput ? urlInput.value.trim() : '';
            const desc = descInput ? descInput.value.trim() : '';

            const getTexts = (id1, id2) => {
                const container = document.getElementById(id1) || document.getElementById(id2);
                if(!container) return [];
                return Array.from(container.querySelectorAll('textarea'))
                            .map(t => t.value.trim())
                            .filter(t => t.length > 0);
            };

            const payloadData = {
                meta: { url: url, description: desc },
                framework: fw,
                theme: theme,
                box1: getTexts('box1-list', 'list-s'),
                box2: getTexts('box2-list', 'list-w'),
                box3: getTexts('box3-list', 'list-o'),
                box4: getTexts('box4-list', 'list-t')
            };

            const isEdit = {{ isset($analysis) ? 'true' : 'false' }};
            const title = theme ? fw + ': ' + theme : fw + '分析 (' + new Date().toLocaleDateString() + ')';
            
            const fetchUrl = isEdit ? '{{ isset($analysis) ? route("analyses.update", $analysis->id ?? 0) : "" }}' : '{{ route("tools.store") }}';
            const fetchMethod = isEdit ? 'PATCH' : 'POST';

            fetch(fetchUrl, {
                method: fetchMethod,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title: title, type: 'swot', data: payloadData })
            })
            .then(res => res.json())
            .then(data => { alert(data.message); btn.innerHTML = originalText; btn.disabled = false; })
            .catch(error => { console.error(error); alert('保存に失敗しました。'); btn.innerHTML = originalText; btn.disabled = false; });
        }

        // チャット用
        function getListData(listId) {
            const container = document.getElementById(listId);
            if (!container) return [];
            return Array.from(container.querySelectorAll('textarea')).map(t => t.value.trim()).filter(v => v !== "");
        }

        function sendAiMessage() {
            const inputEl = document.getElementById('ai-input');
            const chatHistory = document.getElementById('chat-history');
            const text = inputEl.value.trim();
            if (!text) return;

            chatHistory.insertAdjacentHTML('beforeend', `
                <div class="flex gap-3 flex-row-reverse">
                    <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center shrink-0 shadow-md text-xs text-white font-bold" aria-hidden="true">You</div>
                    <div class="bg-blue-600 p-3 rounded-lg rounded-tr-none text-sm text-white shadow-md max-w-[85%] whitespace-pre-wrap leading-relaxed">${text}</div>
                </div>
            `);
            inputEl.value = ''; inputEl.style.height = 'auto'; chatHistory.scrollTop = chatHistory.scrollHeight;

            const loadingId = 'loading-' + Date.now();
            chatHistory.insertAdjacentHTML('beforeend', `
                <div id="${loadingId}" class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg aria-hidden="true" class="h-4 w-4 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                    <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-500 border border-gray-200 dark:border-gray-800 font-bold"><span class="animate-pulse">AIが分析中...</span></div>
                </div>
            `);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            const swotData = {
                theme: document.getElementById('theme-input').value.trim() || '未設定のテーマ',
                strengths: getListData('list-s'),
                weaknesses: getListData('list-w'),
                opportunities: getListData('list-o'),
                threats: getListData('list-t')
            };
            const contextText = "【現在のSWOT分析の状況】\n" + JSON.stringify(swotData, null, 2);

            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ prompt: text, context: contextText })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById(loadingId).remove();
                let replyText = data.reply || data.error || 'エラーが発生しました。';
                replyText = replyText.replace(/\*\*(.*?)\*\*/g, '<span class="font-bold text-gray-900 dark:text-white">$1</span>');

                chatHistory.insertAdjacentHTML('beforeend', `
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg aria-hidden="true" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                        <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%] whitespace-pre-wrap leading-relaxed">${replyText}</div>
                    </div>
                `);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(loadingId).remove();
                chatHistory.insertAdjacentHTML('beforeend', `
                    <div class="flex gap-3"><div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shrink-0 text-white font-bold" aria-hidden="true">!</div><div class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg text-sm text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">通信エラーが発生しました。</div></div>
                `);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }
    </script>
</x-app-layout>