<?php

use App\Http\Controllers\Api\TopicApiController;
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

    // 認証済みユーザー確認用
    Route::get('/user/me', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'avatar'                    => $user->avatar ?? null,
            'is_pro'                    => $user->is_pro,
            'is_admin'                  => $user->is_admin,
            'unread_notifications_count' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    });

    // トピック作成（PRO限定）
    Route::post('/topics', [TopicApiController::class, 'store']);

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
