<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic; // 🌟 トピックの情報を使うための準備
use App\Models\Post;  // 🌟 投稿の情報を使うための準備

class PostController extends Controller
{
    // 🔽 投稿を保存するための命令（storeメソッド）
    // 引数に Topic $topic を入れることで、「どのトピックに対する投稿か」を自動で受け取ります
    public function store(Request $request, Topic $topic)
    {
        // 1. 入力チェック（バリデーション）
        // ユーザーがフォームから送ってきたデータが正しいルールか確認します
        $validated = $request->validate([
            'url' => 'required|url|max:255',        // URL：必須、URLの形式であること、255文字以内
            'category' => 'required|string|max:255', // 分類：必須
            'comment' => 'nullable|string',          // コメント：任意（未入力でもOK）
        ]);

        $url = $validated['url'];
        $title = null;
        $thumbnail_url = null;

        // 🌟 追加：URL先のサイトから、タイトルとサムネイル画像を自動取得する（簡易スクレイピング）
        try {
            // 相手のサイトに弾かれないように「普通のブラウザからのアクセスですよ」と偽装する
            $context = stream_context_create([
                'http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]
            ]);
            $html = @file_get_contents($url, false, $context);

            if ($html) {
                // ① <title>タグを探す
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                    $title = html_entity_decode($matches[1]);
                }
                // ② OGPのタイトルを探す（こっちの方が正確なので上書き）
                if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $matches)) {
                    $title = html_entity_decode($matches[1]);
                }
                // ③ OGPの画像（サムネイル）を探す
                if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $matches)) {
                    $thumbnail_url = html_entity_decode($matches[1]);
                }
            }
        } catch (\Exception $e) {
            // もし取得に失敗しても、エラーで止めずに「URLのみ」で保存を続行する
        }

        // 2. データベースに保存
        // ログイン中のユーザー情報に紐付けて、新しい投稿を作成します
        $request->user()->posts()->create([
            'topic_id' => $topic->id,         // URLから受け取ったトピックIDをセット
            'url' => $validated['url'],
            'category' => $validated['category'],
            'comment' => $validated['comment'] ?? null, // コメントが空の場合はnullを入れる
            'title' => $title, // 取得したタイトル
            'thumbnail_url' => $thumbnail_url, // 取得したサムネイル画像
        ]);

        // 3. 通知：トピック作成者が別ユーザーの場合のみ通知を送る（自己投稿は通知しない）
        if ($topic->user_id !== $request->user()->id) {
            \App\Models\Notification::create([
                'user_id'         => $topic->user_id,      // 通知を受け取るトピック作成者
                'actor_id'        => $request->user()->id, // エビデンスを追加したユーザー
                'type'            => 'new_post',
                'notifiable_type' => 'topic',
                'notifiable_id'   => $topic->id,
            ]);
        }

        // 4. 元の詳細画面（topics.show）に戻り、成功メッセージを表示する
        return redirect()->route('topics.show', $topic)->with('status', 'エビデンス（投稿）を追加しました！');
    }

// エビデンスの編集画面を表示する処理（edit）
    public function edit(\App\Models\Post $post)
    {
        // セキュリティ対策：他人の投稿は編集不可
        if ($post->user_id !== auth()->id()) {
            abort(403, '他のユーザーのエビデンスは編集できません。');
        }
        
        // 'posts.edit' という画面に、編集対象の投稿データ（$post）を渡す
        return view('posts.edit', compact('post'));
    }

    // エビデンスの変更内容を保存する処理（update）
    public function update(\Illuminate\Http\Request $request, \App\Models\Post $post)
    {
        // セキュリティ対策
        if ($post->user_id !== auth()->id()) {
            abort(403, '他のユーザーのエビデンスは編集できません。');
        }

        // データベースの情報を新しい文字で上書き更新する
        $post->update([
            'url' => $request->url,
            'category' => $request->category,
            'comment' => $request->comment,
        ]);

        // 更新が終わったら、そのエビデンスが紐づいている「トピック詳細画面」に戻る
        return redirect()->route('topics.show', $post->topic_id);
    }

    // エビデンス（投稿）を削除する処理（destroy） 
    // 引数の \App\Models\Post $post には、ボタンを押した投稿のデータが自動的に入ってきます
    public function destroy(\App\Models\Post $post)
    {
        // ① セキュリティ対策（超重要）：自分の投稿以外は削除できないようにブロックする
        if ($post->user_id !== auth()->id()) {
            abort(403, '他のユーザーのエビデンスは削除できません。');
        }

        // ② データベースからこの投稿を完全に削除する
        $post->delete();

        // ③ 削除が完了したら、元の画面（ダッシュボード等）にそのまま戻る
        return back();
    }
}