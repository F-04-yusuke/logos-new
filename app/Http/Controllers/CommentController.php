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

    // コメントに対する返信（補足）を保存する処理
    public function reply(Request $request, \App\Models\Comment $comment)
    {
        $user = auth()->user();

        // 孫への返信（返信に対する返信）は不可とするルール
        if ($comment->parent_id) {
            abort(400, '返信への返信はできません。');
        }

        // 回数制限ルールのチェック
        if ($comment->user_id === $user->id) {
            // 自分のコメントへの返信（補足）は5回まで
            $replyCount = $comment->replies()->where('user_id', $user->id)->count();
            if ($replyCount >= 5) {
                return back()->with('error', '自分のコメントへの補足（返信）は最大5回までです。');
            }
        } else {
            // 他人のコメントへの返信は1回まで
            $replyCount = $comment->replies()->where('user_id', $user->id)->count();
            if ($replyCount >= 1) {
                return back()->with('error', 'このコメントへの返信は1回までです。');
            }
        }

        $request->validate(['body' => 'required|string|max:1000']);

        // 返信を保存
        \App\Models\Comment::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'topic_id' => $comment->topic_id,
            'parent_id' => $comment->id, // ここで親コメントと紐付けます
        ]);

        // 通知：返信先コメントの作成者が別ユーザーの場合のみ通知を送る（自己返信は通知しない）
        if ($comment->user_id !== $user->id) {
            \App\Models\Notification::create([
                'user_id'         => $comment->user_id, // 通知を受け取る元コメントの作成者
                'actor_id'        => $user->id,         // 返信したユーザー
                'type'            => 'comment_reply',
                'notifiable_type' => 'comment',
                'notifiable_id'   => $comment->id,      // 返信先（親）コメントのID
            ]);
        }

        return back()->with('status', '返信を投稿しました。');
    }
}