<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplementController extends Controller
{
    // 情報（Post）への補足保存
    public function storePost(Request $request, \App\Models\Post $post) // ※モデル名が違う場合は修正してください
    {
        // 本人でない、または既に補足済みの場合はエラー弾き
        if ($post->user_id !== auth()->id() || $post->supplement) {
            abort(403, '権限がないか、既に補足済みです。');
        }

        $request->validate(['supplement' => 'required|string|max:1000']);
        $post->update(['supplement' => $request->supplement]);

        return back()->with('status', '補足を追加しました。');
    }

    // 分析・図解（Analysis）への補足保存
    public function storeAnalysis(Request $request, \App\Models\Analysis $analysis)
    {
        if ($analysis->user_id !== auth()->id() || $analysis->supplement) {
            abort(403, '権限がないか、既に補足済みです。');
        }

        $request->validate(['supplement' => 'required|string|max:1000']);
        $analysis->update(['supplement' => $request->supplement]);

        return back()->with('status', '補足を追加しました。');
    }
}