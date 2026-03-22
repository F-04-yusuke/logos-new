<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    // 認証済みユーザー確認用
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'                         => $user->id,
            'name'                       => $user->name,
            'email'                      => $user->email,
            'avatar'                     => $user->avatar ?? null,
            'is_pro'                     => $user->is_pro,
            'is_admin'                   => $user->is_admin,
            'unread_notifications_count' => Notification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    }

    // ブックマーク（保存トピック）一覧
    public function bookmarks(Request $request)
    {
        $bookmarks = $request->user()->savedTopics()
            ->latest('bookmarks.created_at')
            ->limit(10)
            ->get(['topics.id', 'topics.title']);

        return response()->json(
            $bookmarks->map(fn($t) => ['id' => $t->id, 'title' => $t->title])->values()
        );
    }

    // いいね一覧（投稿・コメント）
    public function likes(Request $request)
    {
        $user = $request->user();

        $likedPostIds = \App\Models\Like::where('user_id', $user->id)->pluck('post_id');
        $likedPosts = \App\Models\Post::whereIn('id', $likedPostIds)
            ->where('is_published', true)
            ->with(['user:id,name', 'topic:id,title'])
            ->withCount('likes')
            ->latest()
            ->get();

        $likedComments = $user->likedComments()
            ->with(['user:id,name', 'topic:id,title'])
            ->withCount('likes')
            ->latest()
            ->get();

        return response()->json([
            'posts'    => $likedPosts,
            'comments' => $likedComments,
        ]);
    }

    // 閲覧履歴
    public function history(Request $request)
    {
        $user = $request->user();

        $topics = $user->viewedTopics()
            ->with('categories:id,name')
            ->paginate(12);

        $items = $topics->getCollection()->map(fn($topic) => [
            'id'             => $topic->id,
            'title'          => $topic->title,
            'categories'     => $topic->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'last_viewed_at' => $topic->pivot->last_viewed_at,
        ]);

        return response()->json([
            'data'         => $items,
            'current_page' => $topics->currentPage(),
            'last_page'    => $topics->lastPage(),
        ]);
    }
}
