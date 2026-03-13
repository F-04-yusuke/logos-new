<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // アプリ画面からのデータ保存を許可するカラム
    protected $fillable = ['topic_id', 'url', 'category', 'comment', 'title', 'thumbnail_url','supplement',];

    // この投稿は、1人のユーザーに属している（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // この投稿は、1つのトピックに属している（多対1）
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

// 1つの投稿は、複数のいいねを持つ（1対多）
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // 🔽 ログイン中のユーザーが、すでにこの投稿に「いいね」しているか判定する便利な機能
    public function isLikedBy($user)
    {
        // ユーザー情報がない（未ログイン等）場合は false（してない）を返す
        if (!$user) {
            return false;
        }
        // likes() とカッコをつけ、exists() で直接データベースに「存在するか」を確認します
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
