<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic; // 🌟 データベースのTopicテーブルを操作するための事前宣言

class TopicController extends Controller
{

    // トピック一覧画面（トップページ）を表示する処理
    // 新しく Request $request を追加して、検索窓からの文字を受け取れるようにします
    public function index(\Illuminate\Http\Request $request)
    {
        // 🌟 新機能1：左上タブ用のカテゴリ別最新トピック
        // 大分類（親がないカテゴリ）と、その子供（中分類）を順番通りに取得します
        $tabCategories = \App\Models\Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children') // 🌟 修正：中分類（children）も一緒に読み込む
            ->get();

        // 🌟 修正：各大分類について、「自分自身のID」と「ぶら下がっている中分類のID」を合わせた
        // すべてのトピックの中から、最新5件を取得して $category->latest_topics に入れます
        foreach ($tabCategories as $category) {
            $allCategoryIds = $category->children->pluck('id')->push($category->id);
            $category->latest_topics = \App\Models\Topic::whereHas('categories', function ($q) use ($allCategoryIds) {
                $q->whereIn('categories.id', $allCategoryIds);
            })->withCount('posts')->latest()->take(5)->get();
        }

        // 🌟 新機能2：右側の総合人気トピック
        // エビデンス（投稿）が多い順にトップ10件を取得します
        $popularTopics = \App\Models\Topic::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->latest()
            ->take(10)
            ->get();

        // ① まず、「トピック」の基本の検索準備をします
        // （※ まだ get() を使ってデータを取り出しません。絞り込むかもしれないからです）
        // （※ 並び替えに使うため、エビデンスの「数（posts_count）」も一緒に数えておきます）
        $query = \App\Models\Topic::withCount('posts');

        // ② もし、画面から「search」という名前のデータ（キーワード）が送られてきていたら…
        if ($request->filled('search')) {

            $keyword = $request->search;

            // ③ そのキーワードが「タイトル」または「内容」に含まれているかを探す（曖昧検索）
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('content', 'like', '%' . $keyword . '%');
            });
        }

        // カテゴリでの絞り込み 
        // URLに ?category=1 のようにIDが送られてきたら発動します
        $selectedCategory = null;
        if ($request->filled('category')) {
            $categoryId = $request->category;
            // 🌟 修正：画面に「〇〇のトピック」と表示するために、カテゴリの情報（中分類も含めて）を取得しておく
            $selectedCategory = \App\Models\Category::with('children')->find($categoryId);

            if ($selectedCategory) {
                // 🌟 修正：選ばれたのが大分類だった場合、その下にある中分類のトピックも表示できるようにIDをまとめる
                $searchCategoryIds = $selectedCategory->children->pluck('id')->push($selectedCategory->id);

                $query->whereHas('categories', function ($q) use ($searchCategoryIds) {
                    $q->whereIn('categories.id', $searchCategoryIds);
                });
            }
        }

        // 🔄 並び替え（ソート）の処理
        if ($request->filled('sort')) {
            if ($request->sort === 'oldest') {
                $query->oldest();
            } elseif ($request->sort === 'popular') {
                $query->orderBy('posts_count', 'desc')->latest();
            } else {
                $query->latest();
            }
        } else {
            // デフォルトは新着順に並べます
            $query->latest();
        }

        // 全件取得（get）ではなく、10件ごとのページ分割（paginate）にして取得します
        $topics = $query->paginate(10);

        // 取得したすべてのデータ（$topics, $tabCategories, $popularTopics, $selectedCategory）を画面に渡します
        return view('topics.index', compact('topics', 'tabCategories', 'popularTopics', 'selectedCategory'));
    }

    // トピック新規作成画面を表示する処理（create）
    public function create()
    {
        // 大分類（親がいないカテゴリ）と、それに紐づく中分類をセットで取得して画面に渡す
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();

        return view('topics.create', compact('categories'));
    }

    // 新しいトピックをデータベースに保存する処理（store）
    public function store(\Illuminate\Http\Request $request)
    {
        // 1. 入力チェック（バリデーション）
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // 🌟 変更：カテゴリIDの配列を受け取り、最大2つまでに制限する
            'category_ids' => 'required|array|max:2|min:1',
            'category_ids.*' => 'exists:categories,id', // 存在するカテゴリかチェック
        ], [
            // エラーメッセージを日本語で分かりやすくする
            'category_ids.required' => 'カテゴリを少なくとも1つ選択してください。',
            'category_ids.max' => 'カテゴリは最大2つまでしか選択できません。',
        ]);

        // 2. トピック本体を保存する
        $topic = \App\Models\Topic::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // 3. 🌟 新機能：選ばれたカテゴリ（複数）を、中間テーブルを使ってトピックと紐づける
        // attach() という魔法の言葉を使うだけで、架け橋のデータが自動的に作られます
        $topic->categories()->attach($request->category_ids);

        // 4. 保存が終わったら、そのトピックの詳細画面へ移動
        return redirect()->route('topics.show', $topic);
    }

    //  トピックの詳細画面を表示する処理（show）
    // （検索条件を受け取るために Request $request を引数に追加しています）
    public function show(\Illuminate\Http\Request $request, \App\Models\Topic $topic)
    {
        // ① まず、このトピックに紐づくエビデンス（投稿）を取得する準備をします
        // URLに「?category=YouTube」などがついていたら、その分類だけで絞り込む
        $query = $topic->posts()->with('user');

        // ② メディア分類（YouTube、記事など）での絞り込み
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // ③ 並び替え（ソート）の処理
        if ($request->filled('sort')) {
            if ($request->sort === 'oldest') {
                // 古い順
                $query->oldest();
            } elseif ($request->sort === 'newest') {
                // 新着順
                $query->latest();
            } else {
                // 指定がない・popularの場合は「人気順（いいねの数が多い順）」
                $query->withCount('likes')->orderBy('likes_count', 'desc')->latest();
            }
        } else {
            // デフォルトを「人気順」にしました
            $query->withCount('likes')->orderBy('likes_count', 'desc')->latest();
        }

        // ④ 絞り込みと並び替えが終わった状態のデータを取得
        $posts = $query->get();

        // コメントタブ用のデータを取得する
        // コメントにも「いいね数（参考になった数）」をくっつけて取得する準備をします
        // 親コメント（parent_id が空のもの）だけを取得し、それに紐づく返信（replies）も一緒に読み込む
        $commentQuery = $topic->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies' => function ($q) {
                // 返信にもユーザー情報といいね数をくっつける
                $q->with('user')->withCount('likes')->oldest();
            }])
            ->withCount('likes');

        // コメント用の並び替え（comment_sort）処理
        if ($request->filled('comment_sort')) {
            if ($request->comment_sort === 'oldest') {
                $commentQuery->oldest();
            } elseif ($request->comment_sort === 'newest') {
                $commentQuery->latest();
            } else {
                // 指定がない・popularの場合は「人気順」
                $commentQuery->orderBy('likes_count', 'desc')->latest();
            }
        } else {
            // デフォルトを「人気順」にしました
            $commentQuery->orderBy('likes_count', 'desc')->latest();
        }
        $comments = $commentQuery->get();

        // ログイン中のユーザーが既にコメントしているかチェック（1人1件の制限用）
        $userComment = null;
        if (auth()->check()) {
            $userComment = $topic->comments()->where('user_id', auth()->id())->first();
        }

        // 分析・図解タブ用のデータを取得し、並び替える
        $analysisQuery = \App\Models\Analysis::where('topic_id', $topic->id)->where('is_published', true)->with('user')->withCount('likes');

        if ($request->filled('analysis_sort')) {
            if ($request->analysis_sort === 'oldest') {
                $analysisQuery->oldest();
            } elseif ($request->analysis_sort === 'newest') {
                $analysisQuery->latest();
            } else {
                $analysisQuery->orderBy('likes_count', 'desc')->latest();
            }
        } else {
            // デフォルトは人気順
            $analysisQuery->orderBy('likes_count', 'desc')->latest();
        }
        $topicAnalyses = $analysisQuery->get();

        // 自分が作成した未公開（下書き）の分析を取得（モーダル表示用）
        $myAvailableAnalyses = collect();
        if (auth()->check()) {
            $myAvailableAnalyses = \App\Models\Analysis::where('user_id', auth()->id())->where('is_published', false)->latest()->get();
        }

        // ⑤ 画面に渡す（荷物リストに comments, userComment, topicAnalyses, myAvailableAnalyses を追加！）
        return view('topics.show', compact('topic', 'posts', 'comments', 'userComment', 'topicAnalyses', 'myAvailableAnalyses'));
    }

    // 編集画面（View）を表示する処理（edit）
    public function edit(\App\Models\Topic $topic)
    {
        // セキュリティ対策：他人のトピックの編集画面は開けないようにブロック
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは編集できません。');
        }

        // 編集画面でもカテゴリを選べるように、カテゴリ一覧を取得して渡す
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();

        return view('topics.edit', compact('topic', 'categories'));
    }

    // 編集された新しい内容をデータベースに上書き保存する処理（update）
    public function update(\Illuminate\Http\Request $request, \App\Models\Topic $topic)
    {
        // セキュリティ対策
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは編集できません。');
        }

        // 🌟 追加：入力内容とカテゴリが正しく選ばれているかチェック
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_ids' => 'required|array|max:2|min:1',
            'category_ids.*' => 'exists:categories,id',
            // 🌟 追加：時系列データのチェックもここに入れます
            'timeline_date' => 'nullable|array',
            'timeline_event' => 'nullable|array',
        ], [
            'category_ids.required' => 'カテゴリを少なくとも1つ選択してください。',
            'category_ids.max' => 'カテゴリは最大2つまでしか選択できません。',
        ]);

        // 🌟 新機能：時系列データの組み立て
        $timeline = [];
        if ($request->has('timeline_date') && $request->has('timeline_event')) {
            foreach ($request->timeline_date as $key => $date) {
                // 日付か出来事のどちらかが入力されていれば保存
                if (!empty($date) || !empty($request->timeline_event[$key])) {
                    $timeline[] = [
                        'date' => $date,
                        'event' => $request->timeline_event[$key] ?? '',
                        // 画面から送られてきた is_ai フラグをブール値（true/false）に変換
                        'is_ai' => filter_var($request->timeline_is_ai[$key] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ];
                }
            }
        }

        // データベースの情報を新しい文字で上書き更新する
        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
            'timeline' => empty($timeline) ? null : $timeline, // 🌟 追加：時系列も上書き
        ]);

        // 選ばれたカテゴリを上書き保存する
        // sync() という魔法を使うと、「古い紐づけを消して、新しいIDの配列に入れ替える」作業を自動でやってくれます
        $topic->categories()->sync($request->category_ids);

        // 更新が終わったら、そのトピックの詳細画面へ自動で移動する
        return redirect()->route('topics.show', $topic)->with('status', 'トピックを更新しました。');
    }

    // ここから追加：トピックを削除する処理（destroy）
    // 引数の \App\Models\Topic $topic には、ボタンを押したトピックのデータが自動的に入ってきます
    public function destroy(\App\Models\Topic $topic)
    {
        // ① セキュリティ対策（超重要）：自分のトピック以外は削除できないようにブロックする
        // URLを直接いじって他人のトピックを消そうとする悪意ある攻撃を防ぎます
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは削除できません。');
        }

        // ② データベースからこのトピックを完全に削除する
        $topic->delete();

        // ③ 削除が完了したら、元の画面（ダッシュボード）にそのまま戻る
        return back();
    }

    // Gemini APIを使って時系列を自動生成する処理
    public function generateTimeline(\App\Models\Topic $topic)
    {
        // トピック作成者本人しか実行できないようにブロック
        if ($topic->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }

        // すでに生成済みの場合は実行しない
        if ($topic->timeline) {
            return back()->with('error', 'すでに時系列は生成されています。');
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'APIキーが設定されていません。');
        }

        // 🤖 Geminiへのプロンプト（指示書）を作成
        $prompt = <<<EOT
以下のトピックの「前提となる歴史的背景や時系列」を抽出・推測し、JSON配列形式で出力してください。
トピックから直接読み取れない場合は、一般的な歴史的事実に基づき、最大5件程度の重要な出来事を挙げてください。

【トピック名】: {$topic->title}
【トピック概要】: {$topic->content}

【出力形式の絶対ルール】
必ず以下の形式のJSON配列のみを出力し、それ以外の説明文やマークダウン（```json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約"},
    {"date": "YYYY年MM月", "event": "出来事の短い要約"}
]
EOT;

        try {
            // Gemini API（v1beta）へ通信
            $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.2, // 事実に基づくため創造性を低く設定
                ]
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // 余計なマークダウン（```json や ```）を念のため除去
                $generatedText = preg_replace('/```json\n?|```\n?/', '', $generatedText);
                $generatedText = trim($generatedText);

                // JSON文字列として正しいか検証して保存
                $timelineArray = json_decode($generatedText, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timelineArray)) {
                    // AIが生成したすべての要素に 'is_ai' => true の目印を付ける
                    $timelineArray = array_map(function ($item) {
                        $item['is_ai'] = true;
                        return $item;
                    }, $timelineArray);

                    $topic->update(['timeline' => $timelineArray]);
                    return back()->with('status', 'AIによる時系列の生成が完了しました！');
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini JSON Parse Error: ' . $generatedText);
                    return back()->with('error', 'AIの回答を解析できませんでした。もう一度お試しください。');
                }
            } else {
                return back()->with('error', 'AIとの通信に失敗しました。');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    // 🌟 追加：既存の時系列と最新エビデンスを元に、AIに時系列をアップデートさせる処理
    public function updateTimeline(\Illuminate\Http\Request $request, \App\Models\Topic $topic)
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }

        if (!$topic->timeline) {
            return back()->with('error', 'まずは初期の時系列を生成してください。');
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'APIキーが設定されていません。');
        }

        // 既存の時系列をAIが読める文字列に変換（is_ai の情報もそのまま渡す）
        $currentTimeline = json_encode($topic->timeline, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // トピック内の最新の投稿（エビデンス）の情報をまとめる
        $postsData = "";
        foreach ($topic->posts()->latest()->take(10)->get() as $post) {
            $postsData .= "- URL: {$post->url}\n  コメント: {$post->comment}\n\n";
        }
        if (empty($postsData)) {
            $postsData = "新しいエビデンスは特にありません。";
        }

        // 🤖 Geminiへのプロンプト（アップデート指示書）を作成
        // 🌟 修正：「ユーザーが編集した行は絶対にイジるな」という強い命令を追加しました
        $prompt = <<<EOT
以下のトピックに関する「既存の時系列データ」と「最近追加されたエビデンス（情報）」を提供します。
これらを統合・分析し、必要であれば新しい出来事を時系列に追加して、最新版のJSON配列として出力してください。

【トピック名】: {$topic->title}
【トピック概要】: {$topic->content}

【既存の時系列データ】:
{$currentTimeline}

【新しく追加されたエビデンス】:
{$postsData}

【出力形式の絶対ルール】
1. 既存のデータの中で "is_ai": false となっている項目はユーザーが手動で編集した重要なデータです。絶対に削除や改変を行わず、そのまま残してください。
2. 新しく追加する項目、またはAIが再構成した項目には "is_ai": true を設定してください。
3. 必ず以下の形式のJSON配列のみを出力し、マークダウン（```json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse},
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse}
]
EOT;

        try {
            $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.2]
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $generatedText = preg_replace('/```json\n?|```\n?/', '', $generatedText);
                $generatedText = trim($generatedText);

                $timelineArray = json_decode($generatedText, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timelineArray)) {
                    
                    // 🌟 修正：全部を true に上書きするのではなく、AIの返答を尊重して保存する
                    $timelineArray = array_map(function($item) {
                        // is_ai がセットされていない場合は true（AI生成）とする
                        $item['is_ai'] = isset($item['is_ai']) ? filter_var($item['is_ai'], FILTER_VALIDATE_BOOLEAN) : true;
                        return $item;
                    }, $timelineArray);

                    $topic->update(['timeline' => $timelineArray]);
                    return back()->with('status', 'AIによる時系列のアップデートが完了しました！');
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini JSON Parse Error: ' . $generatedText);
                    return back()->with('error', 'AIの回答を解析できませんでした。');
                }
            } else {
                return back()->with('error', 'AIとの通信に失敗しました。');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }
}
