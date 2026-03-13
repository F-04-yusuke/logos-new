<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TopicImageController extends Controller
{
    public function store(Request $request, \App\Models\Topic $topic)
    {
        // 入力チェック（画像は5MBまで、形式はjpg, png, gifなどに限定）
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // 🌟 画像をサーバーの storage/app/public/analyses フォルダに保存
        $path = $request->file('image')->store('analyses', 'public');

        // 🌟 分析（analyses）テーブルに「画像タイプ」として保存
        \App\Models\Analysis::create([
            'user_id' => auth()->id(),
            'topic_id' => $topic->id, // このトピックに紐付ける
            'title' => $request->title,
            'type' => 'image', // 新しい種類「image」
            'data' => ['image_path' => $path], // 保存した画像のパスを記録
        ]);

        return back()->with('status', 'オリジナル図解（画像）を公開しました！');
    }
}