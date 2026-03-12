<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                ロジックツリー作成 (PRO)
            </h2>
            <button onclick="saveTree()" id="save-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors shadow-sm">
                ツリーを保存する
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <style>
                textarea {
                    resize: none;
                    overflow: hidden;
                }
                .tree-line::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    left: -0.75rem;
                    width: 2px;
                    background-color: #374151;
                    border-radius: 2px;
                }
                .chat-scroll::-webkit-scrollbar {
                    width: 6px;
                }
                .chat-scroll::-webkit-scrollbar-thumb {
                    background-color: #4B5563;
                    border-radius: 3px;
                }
            </style>

            <div class="flex flex-col gap-8">
                <div>
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-800 pb-4 flex justify-between items-end">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">各コメントに自動でID（自1, A2など）が付与され、下部のAIと連携します。完成したツリーはトピックの分析タブに投稿できます。</p>
                        </div>
                    </div>

                    <div class="bg-transparent mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">事前情報</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">AI読み込み用データ</span>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 flex flex-col sm:flex-row gap-3 items-end">
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-bold text-blue-800 dark:text-blue-300 mb-1">AIでツリーの土台を自動生成</label>
                                <input type="text" id="tree-theme-input" class="w-full bg-white dark:bg-[#131314] border border-blue-300 dark:border-blue-700 rounded text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="議論したいテーマを入力（例：消費税増税の是非について）">
                            </div>
                            <button id="ai-generate-btn" onclick="generateWithAI()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded text-sm transition-colors shadow-sm shrink-0 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                AIで生成
                            </button>
                        </div>

                        <div class="space-y-2 mb-3">
                            <input type="url" id="info-url" class="w-full bg-transparent dark:bg-[#131314] border-b border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200 text-sm py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-600" placeholder="元情報のURL (例: https://youtu.be/...)">
                            <textarea id="info-desc" oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] border-b border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-200 text-sm py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-gray-400 dark:placeholder-gray-600" rows="1" placeholder="トピックの主題や元情報の概要を入力..."></textarea>
                        </div>
                        
                        <button onclick="addNode(document.getElementById('root-replies'))" class="text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors flex items-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/50 dark:hover:bg-gray-700 px-3 py-1.5 rounded-full w-fit border border-gray-200 dark:border-gray-700">
                            <span class="text-base mr-1 leading-none">＋</span> 分岐を追加
                        </button>
                    </div>

                    <div id="root-replies" class="space-y-2">
                        </div>
                </div>

                <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-gray-900 dark:text-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AIアシスタント (Gemini連携準備中)
                    </h2>
                    
                    <div class="bg-white dark:bg-[#1e1f20] border border-gray-200 dark:border-gray-700 rounded-xl flex flex-col h-[400px] shadow-sm dark:shadow-lg overflow-hidden">
                        
                        <div id="chat-history" class="chat-scroll flex-1 overflow-y-auto p-4 space-y-4">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </div>
                                <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%]">
                                    ※これはUIの確認用モックアップです。本番環境ではここにGemini APIからの実際の回答が表示されます。<br>
                                    「自1の返信案を作って」など、対象を選んで指示を試してみてください。
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191a]">
                            <div class="flex gap-2 mb-2 items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-bold">返信対象:</span>
                                <select id="ai-target-select" class="bg-white dark:bg-[#131314] text-gray-900 dark:text-gray-200 text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 focus:outline-none focus:border-blue-500 cursor-pointer">
                                    <option value="指定なし">指定なし (全体への質問・調査)</option>
                                </select>
                            </div>
                            <div class="flex gap-2 items-end">
                                <textarea id="ai-input" oninput="autoResize(this)" class="flex-1 bg-white dark:bg-[#131314] border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 text-sm p-2.5 focus:outline-none focus:border-blue-500 max-h-32" rows="1" placeholder="AIへの指示や修正案を入力..."></textarea>
                                <button onclick="sendAiMessage()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-md shrink-0">
                                    送信
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        // 🌟 修正1：手動追加時のプルダウンに「主張」を追加
        function addNode(container) {
            const nodeHTML = `
                <div class="mt-2 group relative tree-line tree-node">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 mb-1">
                            <select onchange="handleSelectChange(this)" class="speaker-select bg-transparent dark:bg-[#131314] text-gray-700 dark:text-gray-300 text-sm font-bold focus:outline-none cursor-pointer">
                                <option value="ユーザーA" class="bg-white dark:bg-[#1e1f20]">ユーザーA</option>
                                <option value="ユーザーB" class="bg-white dark:bg-[#1e1f20]">ユーザーB</option>
                                <option value="ユーザーC" class="bg-white dark:bg-[#1e1f20]">ユーザーC</option>
                                <option value="自分 (自)" class="bg-white dark:bg-[#1e1f20] text-blue-600 dark:text-blue-400">自分 (自)</option>
                                <option value="その他" class="bg-white dark:bg-[#1e1f20]">その他</option>
                            </select>
                            
                            <span class="speaker-id text-xs font-black text-gray-500 dark:text-gray-500 bg-gray-200 dark:bg-gray-800 px-1.5 py-0.5 rounded"></span>

                            <select onchange="updateStanceColor(this)" class="stance-select text-[10px] px-1.5 py-0.5 rounded focus:outline-none cursor-pointer border bg-red-100 dark:bg-red-400/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-400/30">
                                <option value="主張" class="bg-white dark:bg-[#1e1f20] text-gray-600 dark:text-gray-400">主張</option>
                                <option value="反論" class="bg-white dark:bg-[#1e1f20] text-red-600 dark:text-red-400" selected>反論</option>
                                <option value="賛成・補足" class="bg-white dark:bg-[#1e1f20] text-green-600 dark:text-green-400">賛成・補足</option>
                                <option value="疑問" class="bg-white dark:bg-[#1e1f20] text-yellow-600 dark:text-yellow-400">疑問</option>
                            </select>

                            <button onclick="removeNode(this)" class="ml-auto text-gray-400 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 text-xs flex items-center transition-colors px-2 py-1 rounded">
                                <span class="text-sm mr-1 leading-none">✕</span>
                            </button>
                        </div>

                        <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] border-b border-gray-300 dark:border-gray-700 focus:border-blue-500 text-gray-900 dark:text-gray-200 text-sm py-1.5 focus:outline-none transition-colors placeholder-gray-400 dark:placeholder-gray-600" rows="1" placeholder="意見を入力..."></textarea>
                        
                        <button onclick="addNode(this.nextElementSibling)" class="mt-1.5 text-[11px] font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center w-fit">
                            <span class="mr-1 leading-none">＋</span> 返信を追加
                        </button>
                        
                        <div class="replies-container ml-2 pl-3 sm:ml-2 sm:pl-4 mt-1 space-y-1"></div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', nodeHTML);
            updateAllLabels(); 
        }

        function removeNode(btn) {
            btn.closest('.tree-node').remove();
            updateAllLabels(); 
        }

        function handleSelectChange(selectElement) {
            updateSpeakerColor(selectElement);
            updateAllLabels(); 
        }

        function updateAllLabels() {
            const nodes = document.querySelectorAll('.tree-node');
            const counts = {}; 
            const aiSelect = document.getElementById('ai-target-select');
            
            aiSelect.innerHTML = '<option value="指定なし">指定なし (全体への質問・調査)</option>';

            nodes.forEach(node => {
                const select = node.querySelector('.speaker-select');
                const speaker = select.value;
                
                if (!counts[speaker]) counts[speaker] = 0;
                counts[speaker]++;

                let prefix = "他";
                if (speaker.includes("A")) prefix = "A";
                else if (speaker.includes("B")) prefix = "B";
                else if (speaker.includes("C")) prefix = "C";
                else if (speaker.includes("自分")) prefix = "自";

                const label = prefix + counts[speaker];
                const idSpan = node.querySelector('.speaker-id');
                idSpan.innerText = label;

                if (speaker.includes("自分")) {
                    idSpan.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
                    idSpan.classList.remove('bg-gray-200', 'text-gray-500', 'dark:bg-gray-800', 'dark:text-gray-500');
                    aiSelect.insertAdjacentHTML('beforeend', `<option value="${label}">${label} を対象にする</option>`);
                } else {
                    idSpan.classList.add('bg-gray-200', 'text-gray-500', 'dark:bg-gray-800', 'dark:text-gray-500');
                    idSpan.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
                }
            });
        }

        function updateSpeakerColor(selectElement) {
            if (selectElement.value.includes('自分')) {
                selectElement.classList.add('text-blue-600', 'dark:text-blue-400');
                selectElement.classList.remove('text-gray-700', 'dark:text-gray-300');
            } else {
                selectElement.classList.remove('text-blue-600', 'dark:text-blue-400');
                selectElement.classList.add('text-gray-700', 'dark:text-gray-300');
            }
        }

        // 🌟 修正2：スタンスに「主張」の時の色を追加
        function updateStanceColor(selectElement) {
            selectElement.className = 'stance-select text-[10px] px-1.5 py-0.5 rounded focus:outline-none cursor-pointer border';
            const value = selectElement.value;
            if (value === '主張') selectElement.classList.add('bg-gray-100', 'text-gray-600', 'border-gray-200', 'dark:bg-gray-400/10', 'dark:text-gray-400', 'dark:border-gray-400/30');
            else if (value === '反論') selectElement.classList.add('bg-red-100', 'text-red-600', 'border-red-200', 'dark:bg-red-400/10', 'dark:text-red-400', 'dark:border-red-400/30');
            else if (value === '賛成・補足') selectElement.classList.add('bg-green-100', 'text-green-600', 'border-green-200', 'dark:bg-green-400/10', 'dark:text-green-400', 'dark:border-green-400/30');
            else if (value === '疑問') selectElement.classList.add('bg-yellow-100', 'text-yellow-600', 'border-yellow-200', 'dark:bg-yellow-400/10', 'dark:text-yellow-400', 'dark:border-yellow-400/30');
        }

        function sendAiMessage() {
            const inputEl = document.getElementById('ai-input');
            const targetEl = document.getElementById('ai-target-select');
            const chatHistory = document.getElementById('chat-history');
            const text = inputEl.value.trim();
            const target = targetEl.value;

            if (!text) return;

            // ユーザーのメッセージを画面に表示
            const userMsg = `
                <div class="flex gap-3 flex-row-reverse">
                    <div class="w-8 h-8 rounded-full bg-gray-500 dark:bg-gray-600 flex items-center justify-center shrink-0 shadow-md text-xs text-white font-bold">You</div>
                    <div class="flex flex-col items-end">
                        ${target !== '指定なし' ? `<span class="text-[10px] text-gray-500 mb-1 font-bold">Target: ${target}</span>` : ''}
                        <div class="bg-blue-600 p-3 rounded-lg rounded-tr-none text-sm text-white shadow-md max-w-[85%] whitespace-pre-wrap">${text}</div>
                    </div>
                </div>
            `;
            chatHistory.insertAdjacentHTML('beforeend', userMsg);
            
            inputEl.value = '';
            inputEl.style.height = 'auto';
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // ローディング（思考中）アニメーションを表示
            const loadingId = 'loading-' + Date.now();
            const loadingMsg = `
                <div id="${loadingId}" class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md">
                        <svg class="h-4 w-4 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800 font-bold">
                        <span class="animate-pulse">AIが思考中...</span>
                    </div>
                </div>
            `;
            chatHistory.insertAdjacentHTML('beforeend', loadingMsg);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // 現在のツリー構造と事前情報を読み取ってAIに送る
            const rootContainer = document.getElementById('root-replies');
            const treeData = buildTreeData(rootContainer);
            const urlInput = document.getElementById('info-url');
            const descInput = document.getElementById('info-desc');
            const url = urlInput ? urlInput.value.trim() : '';
            const desc = descInput ? descInput.value.trim() : '';
            
            let contextText = "";
            if (desc || url) contextText += `【事前情報】\n概要: ${desc}\nURL: ${url}\n\n`;
            contextText += '【現在のツリー構造】\n' + JSON.stringify(treeData, null, 2);

            // Laravelのバックエンド（AnalysisController@aiAssist）に送信
            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    prompt: (target !== '指定なし' ? '対象: 【' + target + '】\n' : '') + text,
                    context: contextText
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById(loadingId).remove();
                
                let replyText = data.reply || data.error || 'エラーが発生しました。';
                replyText = replyText.replace(/\*\*(.*?)\*\*/g, '<span class="font-bold text-gray-900 dark:text-white">$1</span>');

                const aiMsg = `
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-md">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div class="bg-gray-100 dark:bg-[#131314] p-3 rounded-lg rounded-tl-none text-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-800 max-w-[85%] whitespace-pre-wrap leading-relaxed">${replyText}</div>
                    </div>
                `;
                chatHistory.insertAdjacentHTML('beforeend', aiMsg);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(loadingId).remove();
                const errorMsg = `
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shrink-0 shadow-md text-white font-bold">!</div>
                        <div class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg rounded-tl-none text-sm text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                            通信エラーが発生しました。
                        </div>
                    </div>
                `;
                chatHistory.insertAdjacentHTML('beforeend', errorMsg);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }

        // ツリーの階層構造を読み取る再帰関数
        function buildTreeData(container) {
            const data = [];
            const nodes = container.children;
            for(let i=0; i<nodes.length; i++) {
                if(nodes[i].classList.contains('tree-node')) {
                    const nodeEl = nodes[i];
                    const speaker = nodeEl.querySelector('.speaker-select').value;
                    const stance = nodeEl.querySelector('.stance-select').value;
                    const text = nodeEl.querySelector('textarea').value;
                    const repliesContainer = nodeEl.querySelector('.replies-container');
                    
                    // 子ノード（返信）があれば、再帰的に読み取る
                    const children = buildTreeData(repliesContainer);

                    data.push({ speaker, stance, text, children });
                }
            }
            return data;
        }

        // データベースに送信する処理
        function saveTree() {
            const btn = document.getElementById('save-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '保存中...';
            btn.disabled = true;

            // 事前情報（meta）とツリー構造（nodes）をセットにして保存
            const rootContainer = document.getElementById('root-replies');
            const treeData = buildTreeData(rootContainer);
            const url = document.getElementById('info-url').value.trim();
            const desc = document.getElementById('info-desc').value.trim();
            
            const payloadData = {
                meta: { url: url, description: desc },
                nodes: treeData
            };

            const title = 'ロジックツリー (' + new Date().toLocaleDateString() + ')';

            fetch('{{ route("tools.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    title: title,
                    type: 'tree',
                    data: payloadData // まとめたデータを送る
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
        
        // 本物のAIと通信してロジックツリーを自動生成する
        // 🌟 修正3：AIに正しい話者名を指定させる＆生成後にラベルを更新する
        function generateWithAI() {
            const btn = document.getElementById('ai-generate-btn');
            const theme = document.getElementById('tree-theme-input').value.trim();
            if (!theme) { alert('テーマを入力してください'); return; }

            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="animate-pulse">AIがツリーを構築中...</span>';
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');

            const prompt = `
                テーマ: 「${theme}」
                このテーマについて、多角的な議論を展開するロジックツリー（主張とそれに対する賛成・反論・疑問の分岐）を自動生成してください。
                深さは2〜3階層、合計5〜8ノード程度にしてください。
                各ノードは以下のプロパティを持つJSONオブジェクトの配列として出力してください。他のテキストは一切含めないでください。
                ※ "speaker" の値は必ず「自分 (自)」「ユーザーA」「ユーザーB」「ユーザーC」のいずれかにしてください。
                [
                  {
                    "speaker": "自分 (自)",
                    "stance": "主張",
                    "text": "テーマに対するメインの主張や問い",
                    "children": [
                      {
                        "speaker": "ユーザーA",
                        "stance": "反論",
                        "text": "メインの主張に対する反論や懸念",
                        "children": []
                      },
                      {
                        "speaker": "自分 (自)",
                        "stance": "賛成・補足",
                        "text": "メインの主張を補強する理由",
                        "children": []
                      }
                    ]
                  }
                ]
            `;

            fetch('{{ route("tools.ai_assist") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ prompt: prompt, context: "JSON配列のみ出力" })
            })
            .then(res => res.json())
            .then(data => {
                if(data.error) throw new Error(data.error);
                
                const jsonMatch = data.reply.match(/\[[\s\S]*\]/);
                if(!jsonMatch) throw new Error('JSONの抽出に失敗しました');
                const treeData = JSON.parse(jsonMatch[0]);

                const rootContainer = document.getElementById('root-replies');
                rootContainer.innerHTML = ''; 
                
                treeData.forEach(node => {
                    rootContainer.insertAdjacentHTML('beforeend', buildNodeHTML(node));
                });
                
                // 🌟 追加：生成されたノードに「自1」「A2」などのIDを振り直す
                updateAllLabels();

                setTimeout(() => {
                    document.querySelectorAll('textarea').forEach(t => autoResize(t));
                }, 100);

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

        // ツリーの各ノードのHTMLを再帰的に組み立てる関数
        // 🌟 修正4：手動追加と全く同じHTMLを生成するように変更
        function buildNodeHTML(node) {
            let childrenHTML = '';
            if (node.children && node.children.length > 0) {
                node.children.forEach(child => {
                    childrenHTML += buildNodeHTML(child);
                });
            }
            
            // AIが指定してきた値をもとに、初期状態の選択肢や色を設定
            const s = node.speaker || 'ユーザーA';
            const selUserA = s === 'ユーザーA' ? 'selected' : '';
            const selUserB = s === 'ユーザーB' ? 'selected' : '';
            const selUserC = s === 'ユーザーC' ? 'selected' : '';
            const selSelf = s === '自分 (自)' ? 'selected' : '';
            const selOther = s === 'その他' ? 'selected' : '';
            const speakerColor = s === '自分 (自)' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300';

            const st = node.stance || '反論';
            const selArg = st === '主張' ? 'selected' : '';
            const selOpp = st === '反論' ? 'selected' : '';
            const selAgr = st === '賛成・補足' ? 'selected' : '';
            const selQ = st === '疑問' ? 'selected' : '';
            
            let stanceColor = "";
            if (st === '主張') stanceColor = 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-400/10 dark:text-gray-400 dark:border-gray-400/30';
            else if (st === '反論') stanceColor = 'bg-red-100 text-red-600 border-red-200 dark:bg-red-400/10 dark:text-red-400 dark:border-red-400/30';
            else if (st === '賛成・補足') stanceColor = 'bg-green-100 text-green-600 border-green-200 dark:bg-green-400/10 dark:text-green-400 dark:border-green-400/30';
            else if (st === '疑問') stanceColor = 'bg-yellow-100 text-yellow-600 border-yellow-200 dark:bg-yellow-400/10 dark:text-yellow-400 dark:border-yellow-400/30';
            else stanceColor = 'bg-red-100 text-red-600 border-red-200 dark:bg-red-400/10 dark:text-red-400 dark:border-red-400/30';

            return `
                <div class="mt-2 group relative tree-line tree-node">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 mb-1">
                            <select onchange="handleSelectChange(this)" class="speaker-select bg-transparent dark:bg-[#131314] ${speakerColor} text-sm font-bold focus:outline-none cursor-pointer">
                                <option value="ユーザーA" class="bg-white dark:bg-[#1e1f20]" ${selUserA}>ユーザーA</option>
                                <option value="ユーザーB" class="bg-white dark:bg-[#1e1f20]" ${selUserB}>ユーザーB</option>
                                <option value="ユーザーC" class="bg-white dark:bg-[#1e1f20]" ${selUserC}>ユーザーC</option>
                                <option value="自分 (自)" class="bg-white dark:bg-[#1e1f20] text-blue-600 dark:text-blue-400" ${selSelf}>自分 (自)</option>
                                <option value="その他" class="bg-white dark:bg-[#1e1f20]" ${selOther}>その他</option>
                            </select>
                            
                            <span class="speaker-id text-xs font-black text-gray-500 dark:text-gray-500 bg-gray-200 dark:bg-gray-800 px-1.5 py-0.5 rounded"></span>

                            <select onchange="updateStanceColor(this)" class="stance-select text-[10px] px-1.5 py-0.5 rounded focus:outline-none cursor-pointer border ${stanceColor}">
                                <option value="主張" class="bg-white dark:bg-[#1e1f20] text-gray-600 dark:text-gray-400" ${selArg}>主張</option>
                                <option value="反論" class="bg-white dark:bg-[#1e1f20] text-red-600 dark:text-red-400" ${selOpp}>反論</option>
                                <option value="賛成・補足" class="bg-white dark:bg-[#1e1f20] text-green-600 dark:text-green-400" ${selAgr}>賛成・補足</option>
                                <option value="疑問" class="bg-white dark:bg-[#1e1f20] text-yellow-600 dark:text-yellow-400" ${selQ}>疑問</option>
                            </select>

                            <button onclick="removeNode(this)" class="ml-auto text-gray-400 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 text-xs flex items-center transition-colors px-2 py-1 rounded">
                                <span class="text-sm mr-1 leading-none">✕</span>
                            </button>
                        </div>

                        <textarea oninput="autoResize(this)" class="w-full bg-transparent dark:bg-[#131314] border-b border-gray-300 dark:border-gray-700 focus:border-blue-500 text-gray-900 dark:text-gray-200 text-sm py-1.5 focus:outline-none transition-colors placeholder-gray-400 dark:placeholder-gray-600" rows="1" placeholder="意見を入力...">${node.text || ''}</textarea>
                        
                        <button onclick="addNode(this.nextElementSibling)" class="mt-1.5 text-[11px] font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center w-fit">
                            <span class="mr-1 leading-none">＋</span> 返信を追加
                        </button>
                        
                        <div class="replies-container ml-2 pl-3 sm:ml-2 sm:pl-4 mt-1 space-y-1">${childrenHTML}</div>
                    </div>
                </div>
            `;
        }
    </script>
</x-app-layout>