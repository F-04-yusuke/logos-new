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
                
                <div class="mt-4 border-t border-gray-200 dark:border-gray-800 pt-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-gray-900 dark:text-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AI SWOT・アシスタント
                    </h2>
                    <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-xl flex flex-col h-[350px] shadow-sm dark:shadow-lg overflow-hidden">
                        <div id="chat-history" class="overflow-y-auto p-4 space-y-4 flex-1" style="scrollbar-width: thin;">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                                <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%]">
                                    「この強みを活かした戦略を提案して」「弱みを克服するアイデアは？」など、作成したSWOTをもとにAIと議論できます。
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191a]">
                            <div class="flex gap-2 items-end">
                                <textarea id="ai-input" oninput="autoResize(this)" class="flex-1 bg-white dark:bg-[#131314] border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 text-sm p-2.5 focus:outline-none focus:border-blue-500 max-h-32" rows="1" placeholder="AIへの指示や質問を入力..."></textarea>
                                <button onclick="sendAiMessage()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-md shrink-0">送信</button>
                            </div>
                        </div>
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

        // 本物のAIと通信してSWOTを自動生成する
        function generateWithAI() {
            const btn = document.getElementById('ai-btn');
            const originalText = btn.innerHTML;
            const themeInput = document.getElementById('theme-input');
            const theme = themeInput.value.trim();

            if (!theme) {
                alert("AIに分析させる「テーマ（主題）」を入力してください。");
                themeInput.focus();
                return;
            }

            btn.innerHTML = `<span class="animate-pulse">AIが多角的に分析中...</span>`;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            btn.disabled = true;

            // 既存のリストをクリア
            ['list-s', 'list-w', 'list-o', 'list-t'].forEach(id => document.getElementById(id).innerHTML = '');

            // AIへの指示（プロンプト）を作成
            const promptText = `
                以下のテーマについてSWOT分析を行ってください。
                テーマ: 「${theme}」
                
                出力は必ず以下のJSON形式のみとし、他のテキスト（マークダウンや挨拶など）は一切含めないでください。
                {
                    "strengths": ["強み1", "強み2", "強み3"],
                    "weaknesses": ["弱み1", "弱み2", "弱み3"],
                    "opportunities": ["機会1", "機会2", "機会3"],
                    "threats": ["脅威1", "脅威2", "脅威3"]
                }
            `;

            // Laravelのバックエンド（AnalysisController@aiAssist）に送信
            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    prompt: promptText,
                    context: "現在、ユーザーはSWOT分析の自動生成を求めています。必ず指定されたJSONフォーマットのみで返答してください。"
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                
                try {
                    // AIの返答（テキスト）からJSON部分だけを抜き出して解析
                    const jsonMatch = data.reply.match(/\{[\s\S]*\}/);
                    if (!jsonMatch) throw new Error("JSONデータの抽出に失敗しました。");
                    
                    const swotData = JSON.parse(jsonMatch[0]);

                    // 各リストにAIの回答を追加
                    if (swotData.strengths) swotData.strengths.forEach(txt => addItem('list-s', txt, true));
                    if (swotData.weaknesses) swotData.weaknesses.forEach(txt => addItem('list-w', txt, true));
                    if (swotData.opportunities) swotData.opportunities.forEach(txt => addItem('list-o', txt, true));
                    if (swotData.threats) swotData.threats.forEach(txt => addItem('list-t', txt, true));

                } catch (e) {
                    console.error('Parse Error:', e);
                    alert("AIの回答の解析に失敗しました。もう一度お試しください。");
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert("通信エラーが発生しました。時間をおいて再度お試しください。");
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                btn.disabled = false;
            });
        }

        // データベースに送信する処理
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

        // 各リストの中身を取得するヘルパー関数
        function getListData(listId) {
            const container = document.getElementById(listId);
            if (!container) return [];
            return Array.from(container.querySelectorAll('textarea')).map(t => t.value.trim()).filter(v => v !== "");
        }

        // 本物のAIとチャットする機能（SWOT版）
        function sendAiMessage() {
            const inputEl = document.getElementById('ai-input');
            const chatHistory = document.getElementById('chat-history');
            const text = inputEl.value.trim();
            if (!text) return;

            // ユーザーのメッセージを表示
            chatHistory.insertAdjacentHTML('beforeend', `
                <div class="flex gap-3 flex-row-reverse">
                    <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center shrink-0 shadow-md text-xs text-white font-bold">You</div>
                    <div class="bg-blue-600 p-3 rounded-lg rounded-tr-none text-sm text-white shadow-md max-w-[85%] whitespace-pre-wrap">${text}</div>
                </div>
            `);
            inputEl.value = ''; inputEl.style.height = 'auto'; chatHistory.scrollTop = chatHistory.scrollHeight;

            // ローディング表示
            const loadingId = 'loading-' + Date.now();
            chatHistory.insertAdjacentHTML('beforeend', `
                <div id="${loadingId}" class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg class="h-4 w-4 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                    <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-500 border border-gray-200 dark:border-gray-800 font-bold"><span class="animate-pulse">AIが分析中...</span></div>
                </div>
            `);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // 現在のSWOTデータを取得してAIに送る
            const swotData = {
                theme: document.getElementById('theme-input').value.trim() || '未設定のテーマ',
                strengths: getListData('list-s'),
                weaknesses: getListData('list-w'),
                opportunities: getListData('list-o'),
                threats: getListData('list-t')
            };
            const contextText = "【現在のSWOT分析の状況】\n" + JSON.stringify(swotData, null, 2);

            // Laravelバックエンドに送信
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
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                        <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%] whitespace-pre-wrap leading-relaxed">${replyText}</div>
                    </div>
                `);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(loadingId).remove();
                chatHistory.insertAdjacentHTML('beforeend', `
                    <div class="flex gap-3"><div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shrink-0 text-white font-bold">!</div><div class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg text-sm text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">通信エラーが発生しました。</div></div>
                `);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }
    </script>
</x-app-layout>