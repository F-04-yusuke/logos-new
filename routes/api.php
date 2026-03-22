<?php

use App\Http\Controllers\Api\AnalysisApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\TopicApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Models\Category;
use App\Models\Notification;
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
    Route::post('/topics/{topic}/timeline/generate', [TopicApiController::class, 'timelineGenerate']);

    // 時系列: 最新エビデンスからAI更新（トピック作成者限定・生成済みの場合のみ）
    Route::post('/topics/{topic}/timeline/update', [TopicApiController::class, 'timelineUpdate']);

    // ブックマーク（トグル）
    Route::post('/topics/{topic}/bookmark', [TopicApiController::class, 'bookmark']);

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
    Route::delete('/topics/{topic}', [TopicApiController::class, 'destroy']);

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
    Route::post('/categories', [CategoryApiController::class, 'store']);
    Route::patch('/categories/{category}', [CategoryApiController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy']);
});
