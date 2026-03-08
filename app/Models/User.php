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
}
