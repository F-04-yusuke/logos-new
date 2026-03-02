<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // アプリ画面からのデータ保存を許可するカラム
    protected $fillable = ['topic_id', 'url', 'category', 'comment'];

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
}
