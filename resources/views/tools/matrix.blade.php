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

        function sendAiMessage() {
            const inputEl = document.getElementById('ai-input');
            const chatHistory = document.getElementById('chat-history');
            const text = inputEl.value.trim();
            if (!text) return;
            chatHistory.insertAdjacentHTML('beforeend', `<div class="flex gap-3 flex-row-reverse"><div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center shrink-0 shadow-md text-xs text-white">You</div><div class="bg-blue-600 p-3 rounded-lg rounded-tr-none text-sm text-white shadow-md max-w-[85%] whitespace-pre-wrap">${text}</div></div>`);
            inputEl.value = ''; inputEl.style.height = 'auto'; chatHistory.scrollTop = chatHistory.scrollHeight;
            setTimeout(() => {
                chatHistory.insertAdjacentHTML('beforeend', `<div class="flex gap-3"><div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div><div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%] whitespace-pre-wrap leading-relaxed">指示: 「${text}」\n本番環境ではここに提案が返ります。</div></div>`);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }, 800);
        }

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

        // データベースに送信する処理
        function saveMatrix() {
            const btn = document.getElementById('save-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '保存中...';
            btn.disabled = true;

            const matrixData = buildMatrixData();
            const title = '総合評価表 (' + new Date().toLocaleDateString() + ')';

            fetch('{{ route("tools.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: title,
                    type: 'matrix',
                    data: matrixData
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