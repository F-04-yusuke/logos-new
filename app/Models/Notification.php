<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // ================================
    // リレーション
    // ================================

    // 通知の受信者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 通知を発生させたユーザー（system通知はnull）
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // ================================
    // ヘルパーメソッド
    // ================================

    // 未読かどうかを判定する
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    // この通知を既読にする
    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    // ================================
    // 通知文言の生成（Blade / API 兼用）
    // ================================

    // 通知の説明文を返す（例: 「田中さんがあなたのトピックにエビデンスを追加しました」）
    public function getTextAttribute(): string
    {
        $actorName = $this->actor?->name ?? 'だれか';

        return match ($this->type) {
            'new_post'      => "{$actorName} さんがあなたのトピックにエビデンスを追加しました",
            'comment_reply' => "{$actorName} さんがあなたのコメントに返信しました",
            'post_like'     => "{$actorName} さんがあなたの投稿を「参考になった」と評価しました",
            'system'        => $this->message ?? 'システムからのお知らせ',
            default         => '',
        };
    }
}
