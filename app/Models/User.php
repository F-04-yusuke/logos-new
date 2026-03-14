<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1人のユーザーは複数のトピックを作れる（1対多）
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    // 1人のユーザーは複数の投稿を作れる（1対多）
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // このユーザーが保存（ブックマーク）したトピックを取得する
    public function savedTopics()
    {
        return $this->belongsToMany(Topic::class, 'bookmarks')->withTimestamps();
    }

    // 自分が「いいね（参考になった）」を押した投稿を取得する機能
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }

    // このユーザーが書いたコメントと、いいねしたコメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedComments()
    {
        return $this->belongsToMany(Comment::class, 'comment_likes')->withTimestamps();
    }

    // 1人のユーザーは複数の分析・図解を作れる（1対多）
    public function analyses()
    {
        return $this->hasMany(Analysis::class);
    }

    // このユーザーが閲覧したトピックを取得する
    public function viewedTopics()
    {
        return $this->belongsToMany(Topic::class, 'topic_views')
                    ->withPivot('last_viewed_at')
                    ->orderByPivot('last_viewed_at', 'desc');
    }
}
