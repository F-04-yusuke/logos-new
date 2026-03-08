<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // 🔽 ダッシュボード画面を表示する処理（indexメソッド）
    public function index(Request $request)
    {
        // ① 今ログインしているユーザーの情報を取得する
        $user = $request->user();

        // ② そのユーザーが「自分で作ったトピック」を新しい順（latest）で全て取得する
        $myTopics = $user->topics()->latest()->get();

        // ③ そのユーザーが「自分で投稿したエビデンス（Post）」を新しい順（latest）で全て取得する
        $myPosts = $user->posts()->latest()->get();

        // ④ 取得したデータを、'dashboard' という画面（View）に渡して表示する
        return view('dashboard', compact('myTopics', 'myPosts'));
    }
}
