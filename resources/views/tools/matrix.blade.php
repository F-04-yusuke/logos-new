<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                総合評価表作成 (PRO)
            </h2>
            <button onclick="saveMatrix()" id="save-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors shadow-sm">
                評価表を保存する
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <style>
                .custom-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
                .custom-scroll::-webkit-scrollbar-thumb { background-color: #4B5563; border-radius: 4px; }
                textarea { resize: none; overflow: hidden; }
            </style>

            <div class="flex flex-col gap-8">
                <div>
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">行（評価項目）と列（パターン）を自由に追加・削除できます。◎=3点, 〇=2点, △=1点, ×=0点で下部に自動集計されます。</p>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 flex flex-col sm:flex-row gap-3 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-blue-800 dark:text-blue-300 mb-1">AIで表の土台を自動生成</label>
                            <input type="text" id="theme-input" class="w-full bg-white dark:bg-[#131314] border border-blue-300 dark:border-blue-700 rounded text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="比較したいテーマを入力（例：日本のエネルギー政策について）">
                        </div>
                        <button id="ai-generate-btn" onclick="generateWithAI()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded text-sm transition-colors shadow-sm shrink-0 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            AIで生成
                        </button>
                    </div>

                    <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm dark:shadow-lg overflow-hidden">
                        <div class="overflow-x-auto custom-scroll p-4">
                            <table class="w-full text-left border-collapse min-w-[800px]" id="matrix-table">
                                <thead>
                                    <tr id="header-row">
                                        <th class="p-3 border-b border-r border-gray-200 dark:border-gray-700 w-48 bg-gray-50 dark:bg-[#131314] align-bottom">
                                            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">評価項目 \ 比較パターン</div>
                                        </th>
                                        <th class="p-3 border-b border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-50 dark:bg-[#131314] align-top relative group col-pattern">
                                            <div class="flex items-center justify-between mb-2">
                                                <input type="text" value="パターンA: 米国に同調" class="w-full bg-transparent dark:bg-[#131314] font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:border-b border-blue-500 text-sm">
                                                <button onclick="removeColumn(this)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity px-1 text-xs">✕</button>
                                            </div>
                                            <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] text-gray-600 dark:text-gray-400 text-xs focus:outline-none focus:border-b border-gray-300 dark:border-gray-500" rows="1" placeholder="概要や前提..."></textarea>
                                        </th>
                                        <th class="p-3 border-b border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-50 dark:bg-[#131314] align-top relative group col-pattern">
                                            <div class="flex items-center justify-between mb-2">
                                                <input type="text" value="パターンB: 中立・独自外交" class="w-full bg-transparent dark:bg-[#131314] font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:border-b border-blue-500 text-sm">
                                                <button onclick="removeColumn(this)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity px-1 text-xs">✕</button>
                                            </div>
                                            <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] text-gray-600 dark:text-gray-400 text-xs focus:outline-none focus:border-b border-gray-300 dark:border-gray-500" rows="1" placeholder="概要や前提..."></textarea>
                                        </th>
                                        <th class="p-3 border-b border-gray-200 dark:border-gray-700 w-24 bg-gray-100 dark:bg-[#1e1f20] align-middle text-center" id="add-col-th">
                                            <button onclick="addColumn()" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 rounded-lg px-2 py-4 text-xs font-bold transition-colors w-full h-full flex flex-col items-center justify-center gap-1">
                                                <span class="text-lg leading-none">＋</span>
                                                <span>列を追加</span>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="matrix-body">
                                    <tr class="group row-item">
                                        <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#131314] relative">
                                            <button onclick="removeRow(this)" class="absolute top-2 left-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs">✕</button>
                                            <input type="text" value="同盟国（米国）との関係" class="w-full bg-transparent dark:bg-[#131314] font-bold text-gray-900 dark:text-gray-200 focus:outline-none focus:border-b border-gray-300 dark:border-gray-500 text-sm ml-3">
                                        </td>
                                        <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-transparent hover:bg-gray-50 dark:hover:bg-[#252627] transition-colors">
                                            <div class="flex flex-col gap-2">
                                                <select onchange="updateScore()" class="w-full bg-white dark:bg-[#131314] text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-500 font-bold">
                                                    <option value="3">◎ 最適</option>
                                                    <option value="2">〇 良い</option>
                                                    <option value="1">△ 懸念あり</option>
                                                    <option value="0">× 不可</option>
                                                    <option value="-1" selected>-- 評価 --</option>
                                                </select>
                                                <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#1e1f20] border-none text-gray-600 dark:text-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 rounded p-1" rows="2" placeholder="根拠やリンク..."></textarea>
                                            </div>
                                        </td>
                                        <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-transparent hover:bg-gray-50 dark:hover:bg-[#252627] transition-colors">
                                            <div class="flex flex-col gap-2">
                                                <select onchange="updateScore()" class="w-full bg-white dark:bg-[#131314] text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-500 font-bold">
                                                    <option value="3">◎ 最適</option>
                                                    <option value="2">〇 良い</option>
                                                    <option value="1">△ 懸念あり</option>
                                                    <option value="0">× 不可</option>
                                                    <option value="-1" selected>-- 評価 --</option>
                                                </select>
                                                <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#1e1f20] border-none text-gray-600 dark:text-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 rounded p-1" rows="2" placeholder="根拠やリンク..."></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-[#131314]">
                                    <tr id="total-row">
                                        <td class="p-3 border-r border-gray-200 dark:border-gray-700 text-right">
                                            <button onclick="addRow()" class="text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors flex items-center bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 px-3 py-1.5 rounded-full mr-auto">
                                                <span class="text-base mr-1 leading-none">＋</span> 評価項目(行)を追加
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-t border-gray-200 dark:border-gray-800 pt-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-gray-900 dark:text-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AI マトリクス・アシスタント
                    </h2>
                    <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-xl flex flex-col h-[350px] shadow-sm dark:shadow-lg overflow-hidden">
                        <div id="chat-history" class="custom-scroll flex-1 overflow-y-auto p-4 space-y-4">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                                <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%]">
                                    「評価項目に不足はないか」「パターンAのセルを埋める情報を調べて」など、表を完成させるためのサポートを行います。
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191a]">
                            <div class="flex gap-2 items-end">
                                <textarea id="ai-input" oninput="autoResize(this)" class="flex-1 bg-white dark:bg-[#131314] border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 text-sm p-2.5 focus:outline-none focus:border-blue-500 max-h-32" rows="1" placeholder="AIに項目出しや評価のサポートを依頼..."></textarea>
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
        const cellHTML = `
            <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-transparent hover:bg-gray-50 dark:hover:bg-[#252627] transition-colors">
                <div class="flex flex-col gap-2">
                    <select onchange="updateScore()" class="w-full bg-white dark:bg-[#131314] text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-500 font-bold">
                        <option value="3">◎ 最適</option><option value="2">〇 良い</option><option value="1">△ 懸念あり</option><option value="0">× 不可</option><option value="-1" selected>-- 評価 --</option>
                    </select>
                    <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#1e1f20] border-none text-gray-600 dark:text-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 rounded p-1" rows="2" placeholder="根拠やリンク..."></textarea>
                </div>
            </td>`;

        function addColumn() {
            const headerRow = document.getElementById('header-row');
            const addColTh = document.getElementById('add-col-th');
            const newHeader = document.createElement('th');
            newHeader.className = "p-3 border-b border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-50 dark:bg-[#131314] align-top relative group col-pattern";
            newHeader.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <input type="text" value="新規パターン" class="w-full bg-transparent dark:bg-[#131314] font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:border-b border-blue-500 text-sm">
                    <button onclick="removeColumn(this)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity px-1 text-xs">✕</button>
                </div>
                <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] text-gray-600 dark:text-gray-400 text-xs focus:outline-none focus:border-b border-gray-300 dark:border-gray-500" rows="1" placeholder="概要や前提..."></textarea>`;
            headerRow.insertBefore(newHeader, addColTh);
            document.querySelectorAll('.row-item').forEach(row => row.insertAdjacentHTML('beforeend', cellHTML));
            updateScore();
        }

        function removeColumn(btn) {
            const th = btn.closest('th');
            const index = Array.from(th.parentNode.children).indexOf(th);
            th.remove();
            document.querySelectorAll('.row-item').forEach(row => row.children[index].remove());
            updateScore();
        }

        function addRow() {
            const tbody = document.getElementById('matrix-body');
            const colCount = document.querySelectorAll('.col-pattern').length;
            const newRow = document.createElement('tr');
            newRow.className = "group row-item";
            let rowContent = `
                <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#131314] relative">
                    <button onclick="removeRow(this)" class="absolute top-2 left-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs">✕</button>
                    <input type="text" value="新規評価項目" class="w-full bg-transparent dark:bg-[#131314] font-bold text-gray-900 dark:text-gray-200 focus:outline-none focus:border-b border-gray-300 dark:border-gray-500 text-sm ml-3">
                </td>`;
            for (let i = 0; i < colCount; i++) rowContent += cellHTML;
            newRow.innerHTML = rowContent;
            tbody.appendChild(newRow);
            updateScore();
        }

        function removeRow(btn) { btn.closest('tr').remove(); updateScore(); }

        function updateScore() {
            const colCount = document.querySelectorAll('.col-pattern').length;
            const bodyRows = document.querySelectorAll('.row-item');
            const totalRow = document.getElementById('total-row');
            while (totalRow.children.length > 1) totalRow.lastChild.remove();

            for (let i = 1; i <= colCount; i++) {
                let totalScore = 0, isCalculated = false;
                bodyRows.forEach(row => {
                    const val = parseInt(row.children[i].querySelector('select').value);
                    if (val !== -1) { totalScore += val; isCalculated = true; }
                });
                const scoreDisplay = isCalculated ? `<span class="text-2xl font-black text-blue-600 dark:text-blue-400">${totalScore}</span><span class="text-xs text-gray-500 ml-1">pt</span>` : `<span class="text-sm text-gray-400">未評価</span>`;
                const totalTd = document.createElement('td');totalTd.className = "p-3 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#131314] text-center";
                
                totalTd.innerHTML = `<div class="text-xs text-gray-500 mb-1 font-bold">総合評価</div><div>${scoreDisplay}</div>`;
                totalRow.appendChild(totalTd);
            }
        }
        updateScore();

        // 表のデータをJSON化する関数
        function buildMatrixData() {
            const data = { patterns: [], items: [] };
            
            // パターン（列）の取得
            const headerRow = document.getElementById('header-row');
            const patternCols = headerRow.querySelectorAll('.col-pattern');
            patternCols.forEach(col => {
                const title = col.querySelector('input').value;
                const description = col.querySelector('textarea').value;
                data.patterns.push({ title, description });
            });

            // 評価項目（行）と各セルのスコア取得
            const bodyRows = document.querySelectorAll('.row-item');
            bodyRows.forEach(row => {
                const itemTitle = row.querySelector('input').value;
                const scores = [];
                
                // 1列目は項目名なので、2列目（インデックス1）からループ
                for (let i = 1; i <= data.patterns.length; i++) {
                    const cell = row.children[i];
                    if (cell) {
                        const score = cell.querySelector('select').value;
                        const reason = cell.querySelector('textarea').value;
                        scores.push({ score, reason });
                    }
                }
                data.items.push({ itemTitle, scores });
            });

            return data;
        }

        // 本物のAIと通信して表作成のアドバイスをもらう
        function sendAiMessage() {
            const inputEl = document.getElementById('ai-input');
            const chatHistory = document.getElementById('chat-history');
            const text = inputEl.value.trim();
            if (!text) return;

            // ユーザーのメッセージを表示
            chatHistory.insertAdjacentHTML('beforeend', `
                <div class="flex gap-3 flex-row-reverse">
                    <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center shrink-0 shadow-md text-xs text-white">You</div>
                    <div class="bg-blue-600 p-3 rounded-lg rounded-tr-none text-sm text-white shadow-md max-w-[85%] whitespace-pre-wrap">${text}</div>
                </div>
            `);
            inputEl.value = ''; inputEl.style.height = 'auto'; chatHistory.scrollTop = chatHistory.scrollHeight;

            // ローディング表示
            const loadingId = 'loading-' + Date.now();
            chatHistory.insertAdjacentHTML('beforeend', `
                <div id="${loadingId}" class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg class="h-4 w-4 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                    <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-500 border border-gray-200 dark:border-gray-800 font-bold"><span class="animate-pulse">AIが表を分析中...</span></div>
                </div>
            `);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // 現在の表データを取得してAIに文脈として送る
            const matrixData = buildMatrixData();
            const contextText = "【現在の総合評価表の構造】\n" + JSON.stringify(matrixData, null, 2);

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
                    <div class="flex gap-3"><div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shrink-0 text-white font-bold">!</div><div class="bg-red-50 p-3 rounded-lg text-sm text-red-800 border border-red-200">通信エラーが発生しました。</div></div>
                `);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }

        // 🌟 追加：本物のAIと通信して表を自動生成する
        function generateWithAI() {
            const btn = document.getElementById('ai-generate-btn');
            const theme = document.getElementById('theme-input').value.trim();
            if (!theme) { alert('テーマを入力してください'); return; }

            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="animate-pulse">AIが生成中...</span>';
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');

            const prompt = `
                テーマ: 「${theme}」
                このテーマについて、比較すべき2〜3の「パターン（選択肢・方針）」と、それらを評価するための3〜4の「評価項目」を挙げ、総合評価表を作成してください。
                各セルには0〜3のスコア（3:最適, 2:良い, 1:懸念, 0:不可）と短い根拠を入れてください。
                以下のJSON形式のみを出力してください。他のテキストは一切不要です。
                {
                  "patterns": [ {"title": "パターン名", "description": "概要"} ],
                  "items": [
                    {
                      "title": "評価項目名",
                      "evaluations": [ {"score": 3, "reason": "理由"} ]
                    }
                  ]
                }
            `;

            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ prompt: prompt, context: "JSONのみ出力" })
            })
            .then(res => res.json())
            .then(data => {
                if(data.error) throw new Error(data.error);
                const jsonMatch = data.reply.match(/\{[\s\S]*\}/);
                if(!jsonMatch) throw new Error('JSONの抽出に失敗');
                const matrixData = JSON.parse(jsonMatch[0]);

                // テーブルの再構築
                const headerRow = document.getElementById('header-row');
                const tbody = document.getElementById('matrix-body');

                // ヘッダーの初期化
                headerRow.innerHTML = `
                    <th class="p-3 border-b border-r border-gray-200 dark:border-gray-700 w-48 bg-gray-50 dark:bg-[#131314] align-bottom">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">評価項目 \\ 比較パターン</div>
                    </th>
                    <th class="p-3 border-b border-gray-200 dark:border-gray-700 w-24 bg-gray-100 dark:bg-[#1e1f20] align-middle text-center" id="add-col-th">
                        <button onclick="addColumn()" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 rounded-lg px-2 py-4 text-xs font-bold transition-colors w-full h-full flex flex-col items-center justify-center gap-1">
                            <span class="text-lg leading-none">＋</span><span>列を追加</span>
                        </button>
                    </th>
                `;

                const addColTh = document.getElementById('add-col-th');
                matrixData.patterns.forEach(pattern => {
                    const th = document.createElement('th');
                    th.className = "p-3 border-b border-r border-gray-200 dark:border-gray-700 w-64 bg-gray-50 dark:bg-[#131314] align-top relative group col-pattern";
                    th.innerHTML = `
                        <div class="flex items-center justify-between mb-2">
                            <input type="text" value="${pattern.title}" class="w-full bg-transparent dark:bg-[#131314] font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:border-b border-blue-500 text-sm">
                            <button onclick="removeColumn(this)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity px-1 text-xs">✕</button>
                        </div>
                        <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] text-gray-600 dark:text-gray-400 text-xs focus:outline-none focus:border-b border-gray-300 dark:border-gray-500" rows="1">${pattern.description}</textarea>
                    `;
                    headerRow.insertBefore(th, addColTh);
                });

                tbody.innerHTML = '';
                matrixData.items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.className = "group row-item";
                    let cellsHTML = `
                        <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#131314] relative">
                            <button onclick="removeRow(this)" class="absolute top-2 left-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs">✕</button>
                            <input type="text" value="${item.title}" class="w-full bg-transparent dark:bg-[#131314] font-bold text-gray-900 dark:text-gray-200 focus:outline-none focus:border-b border-gray-300 dark:border-gray-500 text-sm ml-3">
                        </td>
                    `;
                    item.evaluations.forEach(eval => {
                        cellsHTML += `
                            <td class="p-3 border-b border-r border-gray-200 dark:border-gray-700 bg-transparent hover:bg-gray-50 dark:hover:bg-[#252627] transition-colors">
                                <div class="flex flex-col gap-2">
                                    <select onchange="updateScore()" class="w-full bg-white dark:bg-[#131314] text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-500 font-bold">
                                        <option value="3" ${eval.score == 3 ? 'selected' : ''}>◎ 最適</option>
                                        <option value="2" ${eval.score == 2 ? 'selected' : ''}>〇 良い</option>
                                        <option value="1" ${eval.score == 1 ? 'selected' : ''}>△ 懸念あり</option>
                                        <option value="0" ${eval.score == 0 ? 'selected' : ''}>× 不可</option>
                                        <option value="-1" ${eval.score == -1 ? 'selected' : ''}>-- 評価 --</option>
                                    </select>
                                    <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#1e1f20] border-none text-gray-600 dark:text-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 rounded p-1" rows="2">${eval.reason || ''}</textarea>
                                </div>
                            </td>
                        `;
                    });
                    tr.innerHTML = cellsHTML;
                    tbody.appendChild(tr);
                });

                updateScore();
                setTimeout(() => document.querySelectorAll('textarea').forEach(t => autoResize(t)), 100);

            })
            .catch(err => {
                console.error(err);
                alert('AI自動生成に失敗しました。もう一度お試しください。');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
            });
        }
    </script>
</x-app-layout>