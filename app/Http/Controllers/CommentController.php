<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // コメントの投稿
    public function store(Request $request, Topic $topic)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        // すでにコメントしていないか念のためチェック
        if ($topic->comments()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'コメントは1人1件までです。');
        }

        $topic->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
            'edit_count' => 0,
        ]);

        return back()->with('status', 'コメントを投稿しました！');
    }

    // コメントの更新（編集）
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) abort(403);
        
        // 🌟 編集回数が3回に達していたらブロック
        if ($comment->edit_count >= 3) {
            return back()->with('error', '編集回数の上限（3回）に達しているため、これ以上編集できません。');
        }

        $request->validate(['body' => 'required|string|max:1000']);

        $comment->update([
            'body' => $request->body,
            'edit_count' => $comment->edit_count + 1, // 編集回数を1増やす
        ]);

        return back()->with('status', 'コメントを更新しました。（残り編集可能回数: ' . (3 - $comment->edit_count) . '回）');
    }

    // コメントの削除
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) abort(403);
        $comment->delete();
        return back()->with('status', 'コメントを削除しました。');
    }

    // コメントへの「いいね」と「解除」
    public function toggleLike(Comment $comment)
    {
        $user = auth()->user();
        if ($comment->isLikedBy($user)) {
            $comment->likes()->detach($user->id);
        } else {
            $comment->likes()->attach($user->id);
        }
        return back();
    }
}