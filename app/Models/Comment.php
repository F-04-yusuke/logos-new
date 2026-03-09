<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'topic_id', 'body', 'edit_count'];

    public function user() { return $this->belongsTo(User::class); }
    public function topic() { return $this->belongsTo(Topic::class); }
    
    // このコメントについた「いいね」
    public function likes() { return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps(); }

    // ログイン中のユーザーがこのコメントに「いいね」しているか判定
    public function isLikedBy($user) {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}