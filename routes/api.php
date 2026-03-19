<?php

use App\Http\Controllers\Api\TopicApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 公開API（認証不要）
Route::get('/topics', [TopicApiController::class, 'index']);
Route::get('/topics/{topic}', [TopicApiController::class, 'show']);

// 認証済みユーザー確認用
Route::middleware('auth:sanctum')->get('/user/me', function (Request $request) {
    return response()->json([
        'id'       => $request->user()->id,
        'name'     => $request->user()->name,
        'is_pro'   => $request->user()->is_pro,
        'is_admin' => $request->user()->is_admin,
    ]);
});
