<?php

use App\Http\Controllers\Api\AnalysisApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\PostApiController;
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
    Route::post('/topics/{topic}/posts', [PostApiController::class, 'store']);

    // コメント投稿（1人1件制限）
    Route::post('/topics/{topic}/comments', [CommentApiController::class, 'store']);

    // エビデンスいいね
    Route::post('/posts/{post}/like', [PostApiController::class, 'like']);

    // コメント返信・削除
    Route::post('/comments/{comment}/reply', [CommentApiController::class, 'reply']);
    Route::delete('/comments/{comment}', [CommentApiController::class, 'destroy']);

    // 投稿補足
    Route::post('/posts/{post}/supplement', [PostApiController::class, 'supplement']);

    // 分析補足
    Route::post('/analyses/{analysis}/supplement', [AnalysisApiController::class, 'supplement']);

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

    // 下書き編集・投稿削除
    Route::patch('/posts/{post}', [PostApiController::class, 'update']);
    Route::delete('/posts/{post}', [PostApiController::class, 'destroy']);

    // トピック削除（自分のトピックのみ）
    Route::delete('/topics/{topic}', function (Request $request, Topic $topic) {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $topic->delete();
        return response()->json(['message' => '削除しました']);
    });

    // 分析ツール
    Route::get('/analyses/{analysis}', [AnalysisApiController::class, 'show']);
    Route::post('/analyses', [AnalysisApiController::class, 'store']);
    Route::put('/analyses/{analysis}', [AnalysisApiController::class, 'update']);
    Route::delete('/analyses/{analysis}', [AnalysisApiController::class, 'destroy']);
    Route::post('/analyses/{analysis}/publish', [AnalysisApiController::class, 'publish']);
    Route::post('/analyses/{analysis}/like', [AnalysisApiController::class, 'like']);
    Route::get('/user/analyses', [AnalysisApiController::class, 'userAnalyses']);
    Route::post('/topics/{topic}/analyses/image', [AnalysisApiController::class, 'storeImage']);
    Route::post('/tools/ai-assist', [AnalysisApiController::class, 'aiAssist']);

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
