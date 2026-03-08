<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    // 🔽 保存を許可する項目を明記する（セキュリティ対策）
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    // 🔽 「このトピックは1人のユーザー（作成者）のものです」という関係性を定義
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // 「1つのトピックは複数の投稿（エビデンス）を持つ」という関係性
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    // このトピックに紐づいているカテゴリ（複数）を取得する魔法 
    // belongsToMany（多対多）という関係性を使います
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // ログイン中のユーザーが、このトピックをすでに保存しているか判定する
    public function isSavedBy($user)
    {
        if (!$user) {
            return false;
        }
        return $this->belongsToMany(User::class, 'bookmarks')->where('user_id', $user->id)->exists();
    }
    
}
