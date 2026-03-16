<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // 通知一覧を表示する
    // 一覧を開いた時点で、表示中の全通知を既読にする
    public function index()
    {
        $user = auth()->user();

        // 送信者（actor）の情報を一緒に取得してN+1を防ぐ
        $notifications = $user
            ->notifications()
            ->with('actor')
            ->latest()
            ->paginate(20);

        // 一覧を開いた時点で全て既読にする
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    // 特定の通知を既読にして、関連ページへリダイレクトする
    public function markAsRead(Notification $notification)
    {
        // 自分の通知以外はブロック
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        // 通知の種別に応じてリダイレクト先を決定する
        return redirect($this->resolveRedirectUrl($notification));
    }

    // 全通知を一括で既読にする
    public function markAllAsRead()
    {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'すべての通知を既読にしました。');
    }

    // ================================
    // プライベートヘルパー
    // ================================

    // 通知の種別・内容に応じてリダイレクト先URLを解決する
    private function resolveRedirectUrl(Notification $notification): string
    {
        return match ($notification->type) {
            // エビデンスが追加されたトピックへ
            'new_post' => route('topics.show', $notification->notifiable_id),

            // 返信がついたコメントが属するトピックへ
            'comment_reply' => route(
                'topics.show',
                Comment::find($notification->notifiable_id)?->topic_id ?? 0
            ),

            // いいねがついた投稿が属するトピックへ
            'post_like' => route(
                'topics.show',
                Post::find($notification->notifiable_id)?->topic_id ?? 0
            ),

            // system通知や解決できない場合は通知一覧へ
            default => route('notifications.index'),
        };
    }
}
