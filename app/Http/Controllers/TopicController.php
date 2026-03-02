<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic; // 🌟 データベースのTopicテーブルを操作するための事前宣言

class TopicController extends Controller
{
    // 🔽 ここから追加

    // 一覧画面を表示するための処理（indexメソッド）
    public function index()
    {
        // データベースからトピックのデータを新しい順（latest）にすべて取得する
        $topics = Topic::latest()->get();

        // resources/views/topics/index.blade.php という画面（View）を開く。
        // その際、取得した $topics のデータを画面に渡す（compact）
        return view('topics.index', compact('topics'));
    }

    // 🔼 ここまで
}