<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    // 保存する処理
    public function store(Topic $topic)
    {
        // attach() で架け橋のデータを作ります
        auth()->user()->savedTopics()->attach($topic->id);
        return back()->with('status', 'トピックを保存しました！');
    }

    // 保存を解除する処理
    public function destroy(Topic $topic)
    {
        // detach() で架け橋のデータを消します
        auth()->user()->savedTopics()->detach($topic->id);
        return back()->with('status', '保存を解除しました。');
    }
}