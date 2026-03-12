<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'title',
        'type',
        'data',
        'is_published',
    ];

    // JSONデータを自動的に配列として扱えるようにする魔法
    protected $casts = [
        'data' => 'array',
        'is_published' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    // この分析についた「いいね（ユーザー）」を取得する
    public function likes()
    {
        return $this->belongsToMany(User::class, 'analysis_likes')->withTimestamps();
    }

    // 特定のユーザー（自分）がすでに「いいね」しているか判定する
    public function isLikedBy($user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}