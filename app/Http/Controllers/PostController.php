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
            'url'          => 'required|url|max:2048',    // URL：必須、URLの形式であること
            'category'     => 'required|string|max:255',  // 分類：必須
            'comment'      => 'nullable|string|max:2000', // コメント：任意（未入力でもOK）
            'is_published' => 'nullable|boolean',         // 下書き(0) or 公開(1)。未指定は公開として扱う
        ]);
        // ⚠️ セキュリティ注意: file_get_contents でサーバーが外部URLを取得するため、
        // http://127.0.0.1 や http://169.254.169.254 (AWSメタデータ) などの内部アドレスへの
        // SSRFリスクがある。本番前に IP ブロックリストまたはドメインホワイトリストの導入を推奨。

        $isPublished = $validated['is_published'] ?? true;
        $url = $validated['url'];
        $title = null;
        $thumbnail_url = null;

        // 公開時のみOGP情報を取得する（下書き保存時はスキップしてレスポンスを速くする）
        if ($isPublished) {
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
                        // DBのカラム長（2048）に収まるようトランケート
                        $thumbnail_url = mb_substr(html_entity_decode($matches[1]), 0, 2048);
                    }
                }
            } catch (\Exception $e) {
                // もし取得に失敗しても、エラーで止めずに「URLのみ」で保存を続行する
            }
        }

        // 2. データベースに保存
        $request->user()->posts()->create([
            'topic_id'     => $topic->id,
            'url'          => $validated['url'],
            'category'     => $validated['category'],
            'comment'      => $validated['comment'] ?? null,
            'title'        => $title,
            'thumbnail_url'=> $thumbnail_url,
            'is_published' => $isPublished,
        ]);

        // 3. 通知：公開投稿のみ送信（下書き保存時は通知しない）
        if ($isPublished && $topic->user_id !== $request->user()->id) {
            \App\Models\Notification::create([
                'user_id'         => $topic->user_id,
                'actor_id'        => $request->user()->id,
                'type'            => 'new_post',
                'notifiable_type' => 'topic',
                'notifiable_id'   => $topic->id,
            ]);
        }

        // 4. 下書きはダッシュボードへ、公開はトピック詳細へ戻る
        if (!$isPublished) {
            return redirect()->route('dashboard')->with([
                'status'     => '下書きとして保存しました。ダッシュボードの「下書き」タブから確認できます。',
                'draft_saved' => true,
            ]);
        }
        return redirect()->route('topics.show', $topic)->with('status', 'エビデンス（投稿）を追加しました！');
    }

// エビデンスの編集画面を表示する処理（edit）
    // ✅ 仕様変更: 下書き（is_published = false）のみ編集可能。公開済み投稿は 403。
    public function edit(\App\Models\Post $post)
    {
        // セキュリティ対策：他人の投稿は編集不可
        if ($post->user_id !== auth()->id()) {
            abort(403, '他のユーザーのエビデンスは編集できません。');
        }

        // 公開済みの投稿は編集不可（仕様：公開後は補足のみ）
        if ($post->is_published) {
            abort(403, '公開済みのエビデンスは編集できません。補足機能をご利用ください。');
        }

        return view('posts.edit', compact('post'));
    }

    // エビデンスの変更内容を保存する処理（update）
    // ✅ 仕様変更: 下書きのみ編集可。is_published=true で本投稿（OGP再取得・通知送信）。
    public function update(\Illuminate\Http\Request $request, \App\Models\Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, '他のユーザーのエビデンスは編集できません。');
        }

        // 公開済みの投稿は一切変更不可
        if ($post->is_published) {
            abort(403, '公開済みのエビデンスは編集できません。');
        }

        $validated = $request->validate([
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|max:255',
            'comment'      => 'nullable|string|max:2000',
            'is_published' => 'required|boolean',
        ]);

        $title         = $post->title;
        $thumbnail_url = $post->thumbnail_url;

        // 本投稿（is_published = true）に切り替わる瞬間だけ OGP を取得する
        if ($validated['is_published'] && !$post->title) {
            try {
                $context = stream_context_create([
                    'http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]
                ]);
                $html = @file_get_contents($validated['url'], false, $context);
                if ($html) {
                    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                        $title = html_entity_decode($matches[1]);
                    }
                    if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $matches)) {
                        $title = html_entity_decode($matches[1]);
                    }
                    if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $matches)) {
                        $thumbnail_url = mb_substr(html_entity_decode($matches[1]), 0, 2048);
                    }
                }
            } catch (\Exception $e) {}
        }

        $post->update([
            'url'          => $validated['url'],
            'category'     => $validated['category'],
            'comment'      => $validated['comment'] ?? null,
            'is_published' => $validated['is_published'],
            'title'        => $title,
            'thumbnail_url'=> $thumbnail_url,
        ]);

        // 本投稿時のみ通知を送信
        if ($validated['is_published']) {
            $topic = $post->topic;
            if ($topic->user_id !== auth()->id()) {
                \App\Models\Notification::create([
                    'user_id'         => $topic->user_id,
                    'actor_id'        => auth()->id(),
                    'type'            => 'new_post',
                    'notifiable_type' => 'topic',
                    'notifiable_id'   => $topic->id,
                ]);
            }
            return redirect()->route('topics.show', $post->topic_id)->with('status', 'エビデンスを公開しました！');
        }

        return redirect()->route('dashboard')->with([
            'status'     => '下書きを保存しました。',
            'draft_saved' => true,
        ]);
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