<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    // アプリ画面からのデータ保存を許可するカラム（ホワイトリスト）
    protected $fillable = ['name'];

    // このトピックは、1人のユーザーに属している（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1つのトピックには、複数の投稿が含まれる（1対多）
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

}
