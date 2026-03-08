<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    // 🔽 アプリ画面からのデータ保存を許可する項目を明記（セキュリティ対策）
    protected $fillable = [
        'user_id',
        'post_id',
    ];
}