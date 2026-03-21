<?php

use App\Http\Controllers\Api\TopicApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 公開API（認証不要）
Route::get('/topics', [TopicApiController::class, 'index']);
Route::get('/topics/{topic}', [TopicApiController::class, 'show']);
Route::get('/categories', fn() => response()->json(Category::orderBy('sort_order')->get()));

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
            'id'       => $user->id,
            'name'     => $user->name,
            'is_pro'   => $user->is_pro,
            'is_admin' => $user->is_admin,
        ],
    ]);
});

// ログアウト（トークン削除）
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'ログアウトしました']);
});

// 認証済みユーザー確認用
Route::middleware('auth:sanctum')->get('/user/me', function (Request $request) {
    return response()->json([
        'id'       => $request->user()->id,
        'name'     => $request->user()->name,
        'is_pro'   => $request->user()->is_pro,
        'is_admin' => $request->user()->is_admin,
    ]);
});
