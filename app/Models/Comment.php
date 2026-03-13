<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'topic_id', 'body', 'edit_count', 'parent_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function topic() { return $this->belongsTo(Topic::class); }

    // このコメントに対する「返信（子コメント）」を取得する関係性
    public function replies()
    {
        // 古い順（投稿された順）に並べて取得します
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    // このコメントの「親コメント」を取得する関係性
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    // このコメントについた「いいね」
    public function likes() { return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps(); }

    // ログイン中のユーザーがこのコメントに「いいね」しているか判定
    public function isLikedBy($user) {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}