<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // 通知一覧
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->with('actor:id,name,avatar')
            ->latest()
            ->paginate(20);

        $items = collect($notifications->items())->map(function ($n) {
            $topicId = null;
            $type    = $n->notifiable_type;
            // Blade版（lowercase）とAPI版（フルクラス名）の両形式に対応
            if (in_array($type, ['post', 'App\\Models\\Post'])) {
                $topicId = \App\Models\Post::find($n->notifiable_id)?->topic_id;
            } elseif (in_array($type, ['topic', 'App\\Models\\Topic'])) {
                $topicId = $n->notifiable_id;
            } elseif (in_array($type, ['comment', 'App\\Models\\Comment'])) {
                $topicId = \App\Models\Comment::find($n->notifiable_id)?->topic_id;
            } elseif (in_array($type, ['analysis', 'App\\Models\\Analysis'])) {
                $topicId = \App\Models\Analysis::find($n->notifiable_id)?->topic_id;
            }
            // モデルの getTextAttribute が空を返す新型通知のテキストを補完
            $text = $n->text;
            if ($text === '') {
                $actorName = $n->actor?->name ?? 'だれか';
                $text = match ($n->type) {
                    'comment_like'   => "{$actorName} さんがあなたのコメントを「参考になった」と評価しました",
                    'analysis_like'  => "{$actorName} さんがあなたの分析を「参考になった」と評価しました",
                    'topic_bookmark' => "{$actorName} さんがあなたのトピックを保存しました",
                    default          => '',
                };
            }
            return [
                'id'         => $n->id,
                'type'       => $n->type,
                'text'       => $text,
                'is_unread'  => $n->isUnread(),
                'created_at' => $n->created_at,
                'topic_id'   => $topicId,
                'actor'      => $n->actor ? ['id' => $n->actor->id, 'name' => $n->actor->name, 'avatar' => $n->actor->avatar] : null,
            ];
        });

        return response()->json([
            'data'         => $items,
            'current_page' => $notifications->currentPage(),
            'last_page'    => $notifications->lastPage(),
            'has_unread'   => Notification::where('user_id', $user->id)->whereNull('read_at')->exists(),
        ]);
    }

    // 通知を全て既読
    public function readAll(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['message' => '既読にしました']);
    }

    // 通知を1件既読
    public function read(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $notification->markAsRead();
        return response()->json(['message' => '既読にしました']);
    }
}
