<?php

use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\TopicApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// 公開API（認証不要）
Route::get('/topics', [TopicApiController::class, 'index']);
Route::get('/topics/{topic}', [TopicApiController::class, 'show']);

// OGP取得プロキシ（認証不要・サーバーサイドでフェッチしてCORS回避）
Route::get('/og', function (Request $request) {
    $url = $request->query('url');
    if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['title' => null, 'thumbnail_url' => null]);
    }

    $title         = null;
    $thumbnail_url = null;

    try {
        $context = stream_context_create([
            'http' => [
                'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                'timeout' => 5,
            ]
        ]);
        $html = @file_get_contents($url, false, $context);
        if ($html) {
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $title = html_entity_decode($m[1]);
            }
            if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $m)) {
                $title = html_entity_decode($m[1]);
            }
            if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $m)) {
                $thumbnail_url = mb_substr(html_entity_decode($m[1]), 0, 2048);
            }
        }
    } catch (\Exception $e) {}

    return response()->json(['title' => $title, 'thumbnail_url' => $thumbnail_url]);
});

Route::get('/categories', fn() => response()->json(
    Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get()
));

// 新規登録
Route::post('/register', function (Request $request) {
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user  = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);
    $token = $user->createToken('next-app')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => 0,
        ],
    ], 201);
});

// ログイン（トークン発行）
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
    }

    $user  = Auth::user();
    $token = $user->createToken('next-app')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ],
    ]);
});

// 認証済みルートグループ
Route::middleware('auth:sanctum')->group(function () {

    // ログアウト（トークン削除）
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ログアウトしました']);
    });

    // 通知
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::patch('/notifications/read-all', [NotificationApiController::class, 'readAll']);
    Route::patch('/notifications/{notification}/read', [NotificationApiController::class, 'read']);

    // ユーザー情報
    Route::get('/user/me', [UserApiController::class, 'me']);
    Route::get('/user/bookmarks', [UserApiController::class, 'bookmarks']);
    Route::get('/user/likes', [UserApiController::class, 'likes']);

    // トピック作成（PRO限定）
    Route::post('/topics', [TopicApiController::class, 'store']);

    // トピック編集（作成者限定）
    Route::put('/topics/{topic}', [TopicApiController::class, 'update']);

    // エビデンス投稿
    Route::post('/topics/{topic}/posts', function (Request $request, Topic $topic) {
        $data = $request->validate([
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|in:YouTube,X,記事,知恵袋,本,その他',
            'comment'      => 'nullable|string|max:5000',
            'is_published' => 'boolean',
        ]);

        $isPublished   = $data['is_published'] ?? true;
        $title         = null;
        $thumbnail_url = null;

        // 公開時のみOGP取得（下書き保存時はスキップして高速化）
        if ($isPublished) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                        'timeout' => 5,
                    ]
                ]);
                $html = @file_get_contents($data['url'], false, $context);
                if ($html) {
                    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                        $title = html_entity_decode($m[1]);
                    }
                    if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $m)) {
                        $title = html_entity_decode($m[1]);
                    }
                    if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $m)) {
                        $thumbnail_url = mb_substr(html_entity_decode($m[1]), 0, 2048);
                    }
                }
            } catch (\Exception $e) {}
        }

        $post = new \App\Models\Post();
        $post->user_id       = $request->user()->id;
        $post->topic_id      = $topic->id;
        $post->url           = $data['url'];
        $post->category      = $data['category'];
        $post->comment       = $data['comment'] ?? null;
        $post->title         = $title;
        $post->thumbnail_url = $thumbnail_url;
        $post->is_published  = $isPublished;
        $post->save();

        $post->load('user:id,name');
        $post->loadCount('likes');

        return response()->json($post, 201);
    });

    // コメント投稿（1人1件制限）
    Route::post('/topics/{topic}/comments', [CommentApiController::class, 'store']);

    // エビデンスいいね（トグル）
    Route::post('/posts/{post}/like', function (Request $request, \App\Models\Post $post) {
        $user = $request->user();
        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
            // 通知：投稿者が別ユーザーの場合のみ
            if ($post->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $post->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'post_like',
                    'notifiable_type' => 'App\\Models\\Post',
                    'notifiable_id'   => $post->id,
                ]);
            }
        }
        return response()->json(['liked' => $liked, 'likes_count' => $post->likes()->count()]);
    });

    // コメント返信・削除
    Route::post('/comments/{comment}/reply', [CommentApiController::class, 'reply']);
    Route::delete('/comments/{comment}', [CommentApiController::class, 'destroy']);

    // 投稿補足（投稿者本人・1回のみ）
    Route::post('/posts/{post}/supplement', function (Request $request, \App\Models\Post $post) {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($post->supplement !== null) {
            return response()->json(['message' => '補足はすでに追加済みです'], 422);
        }
        $data = $request->validate(['supplement' => 'required|string|max:5000']);
        $post->supplement = $data['supplement'];
        $post->save();
        return response()->json(['supplement' => $post->supplement]);
    });

    // 分析補足（投稿者本人・1回のみ）
    Route::post('/analyses/{analysis}/supplement', function (Request $request, \App\Models\Analysis $analysis) {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($analysis->supplement !== null) {
            return response()->json(['message' => '補足はすでに追加済みです'], 422);
        }
        $data = $request->validate(['supplement' => 'required|string|max:5000']);
        $analysis->supplement = $data['supplement'];
        $analysis->save();
        return response()->json(['supplement' => $analysis->supplement]);
    });

    // コメントいいね
    Route::post('/comments/{comment}/like', [CommentApiController::class, 'like']);

    // 時系列: AIで自動生成（トピック作成者限定・未生成の場合のみ）
    Route::post('/topics/{topic}/timeline/generate', function (Request $request, Topic $topic) {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($topic->timeline) {
            return response()->json(['message' => 'すでに時系列は生成されています'], 422);
        }

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['message' => 'APIキーが設定されていません'], 500);
        }

        $prompt = <<<EOT
以下のトピックの「前提となる歴史的背景や時系列」を抽出・推測し、JSON配列形式で出力してください。
トピックから直接読み取れない場合は、一般的な歴史的事実に基づき、最大5件程度の重要な出来事を挙げてください。

【トピック名】: {$topic->title}
【トピック概要】: {$topic->content}

【出力形式の絶対ルール】
必ず以下の形式のJSON配列のみを出力し、それ以外の説明文やマークダウン（\`\`\`json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約"},
    {"date": "YYYY年MM月", "event": "出来事の短い要約"}
]
EOT;

        try {
            $response = \Illuminate\Support\Facades\Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2],
                ]
            );

            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text = preg_replace('/```json\n?|```\n?/', '', $text);
                $text = trim($text);
                $timeline = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timeline)) {
                    $timeline = array_map(fn($item) => array_merge($item, ['is_ai' => true]), $timeline);
                    $topic->update(['timeline' => $timeline]);
                    return response()->json(['timeline' => $topic->fresh()->timeline]);
                }
                return response()->json(['message' => 'AIの回答を解析できませんでした'], 500);
            }
            return response()->json(['message' => 'AIとの通信に失敗しました'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました: ' . $e->getMessage()], 500);
        }
    });

    // 時系列: 最新エビデンスからAI更新（トピック作成者限定・生成済みの場合のみ）
    Route::post('/topics/{topic}/timeline/update', function (Request $request, Topic $topic) {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if (!$topic->timeline) {
            return response()->json(['message' => 'まずは初期の時系列を生成してください'], 422);
        }

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['message' => 'APIキーが設定されていません'], 500);
        }

        $currentTimeline = json_encode($topic->timeline, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $postsData = "";
        foreach ($topic->posts()->where('is_published', true)->latest()->take(10)->get() as $post) {
            $postsData .= "- URL: {$post->url}\n  コメント: {$post->comment}\n\n";
        }
        if (empty($postsData)) {
            $postsData = "新しいエビデンスは特にありません。";
        }

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
3. 必ず以下の形式のJSON配列のみを出力し、マークダウン（\`\`\`json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse},
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse}
]
EOT;

        try {
            $response = \Illuminate\Support\Facades\Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2],
                ]
            );

            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text = preg_replace('/```json\n?|```\n?/', '', $text);
                $text = trim($text);
                $timeline = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timeline)) {
                    $timeline = array_map(function ($item) {
                        $item['is_ai'] = isset($item['is_ai']) ? filter_var($item['is_ai'], FILTER_VALIDATE_BOOLEAN) : true;
                        return $item;
                    }, $timeline);
                    $topic->update(['timeline' => $timeline]);
                    return response()->json(['timeline' => $topic->fresh()->timeline]);
                }
                return response()->json(['message' => 'AIの回答を解析できませんでした'], 500);
            }
            return response()->json(['message' => 'AIとの通信に失敗しました'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました: ' . $e->getMessage()], 500);
        }
    });

    // ブックマーク（トグル）
    Route::post('/topics/{topic}/bookmark', function (Request $request, Topic $topic) {
        $user        = $request->user();
        $isBookmarked = $topic->isSavedBy($user);
        if ($isBookmarked) {
            \DB::table('bookmarks')->where('user_id', $user->id)->where('topic_id', $topic->id)->delete();
            $bookmarked = false;
        } else {
            \DB::table('bookmarks')->insert(['user_id' => $user->id, 'topic_id' => $topic->id, 'created_at' => now(), 'updated_at' => now()]);
            $bookmarked = true;
            // 通知：トピック作成者が別ユーザーの場合のみ
            if ($topic->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $topic->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'topic_bookmark',
                    'notifiable_type' => 'App\\Models\\Topic',
                    'notifiable_id'   => $topic->id,
                ]);
            }
        }
        return response()->json(['bookmarked' => $bookmarked]);
    });

    // 閲覧履歴
    Route::get('/history', [UserApiController::class, 'history']);

    // プロフィール
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::post('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);
    Route::delete('/profile', [ProfileApiController::class, 'destroy']);

    // ダッシュボード
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // 下書き編集（自分の下書きのみ・公開済みは403）
    Route::patch('/posts/{post}', function (Request $request, \App\Models\Post $post) {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($post->is_published) {
            return response()->json(['message' => '公開済みのエビデンスは編集できません'], 403);
        }

        $validated = $request->validate([
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|max:255',
            'comment'      => 'nullable|string|max:2000',
            'is_published' => 'required|boolean',
        ]);

        $title         = $post->title;
        $thumbnail_url = $post->thumbnail_url;

        // 本投稿（is_published = true）に切り替わる瞬間だけ OGP を取得する
        if ($validated['is_published'] && !$post->is_published) {
            try {
                $context = stream_context_create([
                    'http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]
                ]);
                $html = @file_get_contents($validated['url'], false, $context);
                if ($html) {
                    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                        $title = html_entity_decode($matches[1]);
                    }
                    if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $matches)) {
                        $title = html_entity_decode($matches[1]);
                    }
                    if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $matches)) {
                        $thumbnail_url = mb_substr(html_entity_decode($matches[1]), 0, 2048);
                    }
                }
            } catch (\Exception $e) {}

            // 本投稿時のみ通知を送信（トピック作成者へ）
            $topic = $post->topic;
            if ($topic && $topic->user_id !== $request->user()->id) {
                \App\Models\Notification::create([
                    'user_id'         => $topic->user_id,
                    'actor_id'        => $request->user()->id,
                    'type'            => 'new_post',
                    'notifiable_type' => 'topic',
                    'notifiable_id'   => $topic->id,
                ]);
            }
        }

        $post->update([
            'url'           => $validated['url'],
            'category'      => $validated['category'],
            'comment'       => $validated['comment'] ?? null,
            'is_published'  => $validated['is_published'],
            'title'         => $title,
            'thumbnail_url' => $thumbnail_url,
        ]);

        return response()->json($post->fresh());
    });

    // 投稿削除（自分の投稿のみ）
    Route::delete('/posts/{post}', function (Request $request, \App\Models\Post $post) {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $post->delete();
        return response()->json(['message' => '削除しました']);
    });

    // トピック削除（自分のトピックのみ）
    Route::delete('/topics/{topic}', function (Request $request, Topic $topic) {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $topic->delete();
        return response()->json(['message' => '削除しました']);
    });

    // 分析ツール: 一件取得（編集用）
    Route::get('/analyses/{analysis}', function (Request $request, \App\Models\Analysis $analysis) {
        $user = $request->user();
        $data = $analysis->toArray();
        $data['user'] = \App\Models\User::select('id', 'name', 'avatar')->find($analysis->user_id);
        $data['topic'] = $analysis->topic_id
            ? \App\Models\Topic::select('id', 'title')->find($analysis->topic_id)
            : null;
        $data['likes_count']    = $analysis->likes()->count();
        $data['is_liked_by_me'] = $analysis->likes()->where('user_id', $user->id)->exists();
        return response()->json($data);
    });

    // 分析ツール: 新規保存
    Route::post('/analyses', function (Request $request) {
        $user = $request->user();
        if (!$user->is_pro) {
            return response()->json(['message' => 'PRO会員限定の機能です'], 403);
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|string|in:tree,matrix,swot',
            'data'  => 'required|array',
        ]);
        $analysis = \App\Models\Analysis::create([
            'user_id'      => $user->id,
            'title'        => $data['title'],
            'type'         => $data['type'],
            'data'         => $data['data'],
            'is_published' => false,
        ]);
        return response()->json(['message' => '保存しました！', 'id' => $analysis->id], 201);
    });

    // 分析ツール: 上書き保存
    Route::put('/analyses/{analysis}', function (Request $request, \App\Models\Analysis $analysis) {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'data'  => 'required|array',
        ]);
        $analysis->update([
            'title' => $data['title'] ?? $analysis->title,
            'data'  => $data['data'],
        ]);
        return response()->json(['message' => '上書き保存しました！']);
    });

    // 分析ツール: 削除
    Route::delete('/analyses/{analysis}', function (Request $request, \App\Models\Analysis $analysis) {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $analysis->delete();
        return response()->json(['message' => '削除しました']);
    });

    // AIアシスタント (Gemini)
    Route::post('/tools/ai-assist', function (Request $request) {
        $request->validate([
            'prompt'  => 'required|string|max:5000',
            'context' => 'nullable|string|max:10000',
        ]);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini APIキーが設定されていません'], 500);
        }

        $fullPrompt = "あなたは政治・経済の議論を整理するプロのコンサルタントです。\n"
                    . "以下の【現在の状況・データ】を踏まえて、ユーザーの【指示】に的確に答えてください。\n\n"
                    . "【現在の状況・データ】\n" . ($request->context ?? '') . "\n\n"
                    . "【指示】\n" . $request->prompt;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $fullPrompt]]]]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'AIからの回答を取得できませんでした。';
            return response()->json(['reply' => $text]);
        }

        return response()->json(['error' => 'APIエラー: ' . $response->body()], 500);
    });

    // ユーザーの分析一覧（モーダル選択用）
    Route::get('/user/analyses', function (Request $request) {
        $user = $request->user();
        $analyses = \App\Models\Analysis::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'title', 'type', 'is_published', 'topic_id', 'created_at']);
        return response()->json($analyses);
    });

    // 分析をトピックに公開
    Route::post('/analyses/{analysis}/publish', function (Request $request, \App\Models\Analysis $analysis) {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $data = $request->validate(['topic_id' => 'required|integer|exists:topics,id']);
        $analysis->update(['topic_id' => $data['topic_id'], 'is_published' => true]);
        return response()->json(['message' => '公開しました']);
    });

    // 分析いいね（トグル）
    Route::post('/analyses/{analysis}/like', function (Request $request, \App\Models\Analysis $analysis) {
        $user = $request->user();
        if ($analysis->likes()->where('user_id', $user->id)->exists()) {
            $analysis->likes()->detach($user->id);
            $liked = false;
        } else {
            $analysis->likes()->attach($user->id);
            $liked = true;
            // 通知：分析作成者が別ユーザーの場合のみ
            if ($analysis->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $analysis->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'analysis_like',
                    'notifiable_type' => 'App\\Models\\Analysis',
                    'notifiable_id'   => $analysis->id,
                ]);
            }
        }
        return response()->json(['liked' => $liked, 'likes_count' => $analysis->likes()->count()]);
    });

    // オリジナル図解（画像）をトピックに直接アップロード・公開（PRO限定）
    Route::post('/topics/{topic}/analyses/image', function (Request $request, Topic $topic) {
        $user = $request->user();
        if (!$user->is_pro) {
            return response()->json(['message' => 'PRO会員限定の機能です'], 403);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        $path = $request->file('image')->store('analyses', 'public');
        $analysis = \App\Models\Analysis::create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'title'        => $request->title,
            'type'         => 'image',
            'data'         => ['image_path' => $path],
            'is_published' => true,
        ]);
        return response()->json(['message' => 'オリジナル図解を公開しました！', 'id' => $analysis->id], 201);
    });

    // カテゴリ管理（管理者専用）
    Route::post('/categories', function (Request $request) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'parent_id'  => 'nullable|integer|exists:categories,id',
        ]);
        $category = Category::create($data);
        return response()->json($category, 201);
    });

    Route::patch('/categories/{category}', function (Request $request, Category $category) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);
        $category->update($data);
        return response()->json($category);
    });

    Route::delete('/categories/{category}', function (Request $request, Category $category) {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $category->delete();
        return response()->json(['message' => '削除しました']);
    });
});
