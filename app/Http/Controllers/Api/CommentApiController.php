<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Topic;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    // コメント投稿（1人1件制限）
    public function store(StoreCommentRequest $request, Topic $topic)
    {
        $exists = $topic->comments()
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'すでにコメントを投稿済みです'], 422);
        }

        $data = $request->validated();

        $comment = Comment::create([
            'user_id'  => $request->user()->id,
            'topic_id' => $topic->id,
            'body'     => $data['body'],
        ]);

        $comment->load('user:id,name');
        $comment->loadCount('likes');
        $comment->setRelation('replies', collect());

        return response()->json($comment, 201);
    }

    // コメント返信投稿（制限付き: 投稿主5件・他1件）
    public function reply(StoreCommentRequest $request, Comment $comment)
    {
        $user = $request->user();
        $data = $request->validated();

        $myRepliesCount = Comment::where('parent_id', $comment->id)
            ->where('user_id', $user->id)
            ->count();

        if ($comment->user_id === $user->id) {
            if ($myRepliesCount >= 5) {
                return response()->json(['message' => '補足は最大5件までです'], 422);
            }
        } else {
            if ($myRepliesCount >= 1) {
                return response()->json(['message' => 'このコメントへの返信は1件のみ可能です'], 422);
            }
        }

        $reply = Comment::create([
            'user_id'   => $user->id,
            'topic_id'  => $comment->topic_id,
            'parent_id' => $comment->id,
            'body'      => $data['body'],
        ]);

        // 通知：返信先コメントの作成者が別ユーザーの場合のみ
        if ($comment->user_id !== $user->id) {
            Notification::create([
                'user_id'         => $comment->user_id,
                'actor_id'        => $user->id,
                'type'            => 'comment_reply',
                'notifiable_type' => 'App\\Models\\Comment',
                'notifiable_id'   => $comment->id,
            ]);
        }

        $reply->load('user:id,name,avatar');
        $reply->loadCount('likes');

        return response()->json($reply, 201);
    }

    // コメント削除（自分のコメント・返信のみ）
    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $comment->delete();
        return response()->json(['message' => '削除しました']);
    }

    // コメントいいね（トグル）
    public function like(Request $request, Comment $comment)
    {
        $user  = $request->user();
        $liked = $comment->likes()->where('user_id', $user->id)->exists();
        if ($liked) {
            $comment->likes()->detach($user->id);
            $liked = false;
        } else {
            $comment->likes()->attach($user->id);
            $liked = true;
            // 通知：コメント作成者が別ユーザーの場合のみ
            if ($comment->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $comment->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'comment_like',
                    'notifiable_type' => 'App\\Models\\Comment',
                    'notifiable_id'   => $comment->id,
                ]);
            }
        }
        return response()->json(['liked' => $liked, 'likes_count' => $comment->likes()->count()]);
    }
}
