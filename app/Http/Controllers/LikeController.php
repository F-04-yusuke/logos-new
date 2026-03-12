<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post; // 🌟 投稿（Post）の設計図を使うための準備
use App\Models\Like; // 🌟 いいね（Like）の設計図を使うための準備

class LikeController extends Controller
{
    // 🔽 いいねボタンが押されたときに動く命令（storeという名前にしています）
    // 引数の $request には「今ログインしているのは誰か」などの情報が入っています
    // 引数の $post には「どの投稿のいいねボタンが押されたか」の情報が入っています
    public function store(Request $request, Post $post)
    {
        // ① 今ログインしているユーザーの「ID（出席番号のようなもの）」を取り出して、$user_id という名前の箱に入れます
        $user_id = $request->user()->id;

        // ② データベースの likes テーブルを探しに行きます。
        // 「今ログインしているユーザー」が、「今ボタンを押した投稿」に対して、
        // 過去にいいねを押した記録が残っているか？を調べ、$like という箱に入れます。
        $like = Like::where('user_id', $user_id)->where('post_id', $post->id)->first();

        // ③ もし、$like の箱の中に「過去の記録」がすでに入っていたら…
        if ($like) {
            // その記録を消します。（つまり「いいね」の取り消しです）
            $like->delete();
            
        // ④ もし、$like の箱が「空っぽ（まだいいねした記録がない）」だったら…
        } else {
            // likes テーブルに、新しく「この人が、この投稿にいいねしました」という記録を作成（保存）します。
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post->id,
            ]);
        }

        // ⑤ 処理が終わったら、元の画面（トピック詳細画面）にそのまま戻ります。
        return back();
    }

    // 🔽 マイページの「参考になった一覧」画面を表示する処理
    public function index()
    {
        $user_id = auth()->id();

        // 【1. いいねした情報（Post）の取得】
        // 自分がいいねした投稿を、トピック情報・投稿者情報・全体のいいね数と一緒に取得する
        $likedPosts = auth()->user()->likedPosts()
            ->with(['topic', 'user']) // 関連データをまとめて取得
            ->withCount('likes')      // 🌟 最新のいいね数を計算して「likes_count」として取得
            ->latest()                // 新しい順に並べる
            ->get();
            
        // 【2. いいねしたコメント（Comment）の取得】
        // comment_likesテーブル（いいねの記録）と commentsテーブル（コメント本体）をくっつけて、
        // 自分がいいねしたコメントだけを引っ張り出してきます
        $likedComments = \App\Models\Comment::select('comments.*')
            ->join('comment_likes', 'comments.id', '=', 'comment_likes.comment_id')
            ->where('comment_likes.user_id', $user_id)
            ->with(['topic', 'user'])->withCount('likes')
            ->orderBy('comment_likes.created_at', 'desc')->get(); // いいねした日時が新しい順

        // 【3. いいねした分析・図解（Analysis）の取得】
        // analysis_likesテーブルと analysesテーブルをくっつけて取得します
        $likedAnalyses = \App\Models\Analysis::select('analyses.*')
            ->join('analysis_likes', 'analyses.id', '=', 'analysis_likes.analysis_id')
            ->where('analysis_likes.user_id', $user_id)
            ->with(['topic', 'user'])->withCount('likes')
            ->orderBy('analysis_likes.created_at', 'desc')->get();

        // 取得した3種類のデータを、画面（likes/index.blade.php）に荷物として渡します
        return view('likes.index', compact('likedPosts', 'likedComments', 'likedAnalyses'));
    }
}