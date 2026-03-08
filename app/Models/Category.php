<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // 画面から安全に保存していい項目（許可証）
    protected $fillable = [
        'name',
        'sort_order', // 🌟 これを追加
        'parent_id',
    ];

    // このカテゴリが持っている「中分類（子）」をすべて引っ張り出す魔法
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // このカテゴリの「大分類（親）」を引っ張り出す魔法
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}