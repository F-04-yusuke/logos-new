<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    // エビデンス投稿
    public function store(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'url'          => 'required|url|max:2048',
            'category'     => 'required|string|in:YouTube,X,記事,知恵袋,本,その他',
            'comment'      => 'nullable|string|max:5000',
            'is_published' => 'boolean',
        ]);

        $isPublished   = $data['is_published'] ?? true;
        $title         = null;
        $thumbnail_url = null;

        // 公開時のみOGP取得（下書き保存時はスキップして高速化）
        if ($isPublished) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                        'timeout' => 5,
                    ]
                ]);
                $html = @file_get_contents($data['url'], false, $context);
                if ($html) {
                    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                        $title = html_entity_decode($m[1]);
                    }
                    if (preg_match('/<meta property="og:title" content="(.*?)"/is', $html, $m)) {
                        $title = html_entity_decode($m[1]);
                    }
                    if (preg_match('/<meta property="og:image" content="(.*?)"/is', $html, $m)) {
                        $thumbnail_url = mb_substr(html_entity_decode($m[1]), 0, 2048);
                    }
                }
            } catch (\Exception $e) {}
        }

        $post = new Post();
        $post->user_id       = $request->user()->id;
        $post->topic_id      = $topic->id;
        $post->url           = $data['url'];
        $post->category      = $data['category'];
        $post->comment       = $data['comment'] ?? null;
        $post->title         = $title;
        $post->thumbnail_url = $thumbnail_url;
        $post->is_published  = $isPublished;
        $post->save();

        $post->load('user:id,name');
        $post->loadCount('likes');

        return response()->json($post, 201);
    }

    // エビデンスいいね（トグル）
    public function like(Request $request, Post $post)
    {
        $user = $request->user();
        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
            // 通知：投稿者が別ユーザーの場合のみ
            if ($post->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $post->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'post_like',
                    'notifiable_type' => 'App\\Models\\Post',
                    'notifiable_id'   => $post->id,
                ]);
            }
        }
        return response()->json(['liked' => $liked, 'likes_count' => $post->likes()->count()]);
    }

    // 投稿補足（投稿者本人・1回のみ）
    public function supplement(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($post->supplement !== null) {
            return response()->json(['message' => '補足はすでに追加済みです'], 422);
        }
        $data = $request->validate(['supplement' => 'required|string|max:5000']);
        $post->supplement = $data['supplement'];
        $post->save();
        return response()->json(['supplement' => $post->supplement]);
    }

    // 下書き編集（自分の下書きのみ・公開済みは403）
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($post->is_published) {
            return response()->json(['message' => '公開済みのエビデンスは編集できません'], 403);
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
        if ($validated['is_published'] && !$post->is_published) {
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

            // 本投稿時のみ通知を送信（トピック作成者へ）
            $topic = $post->topic;
            if ($topic && $topic->user_id !== $request->user()->id) {
                Notification::create([
                    'user_id'         => $topic->user_id,
                    'actor_id'        => $request->user()->id,
                    'type'            => 'new_post',
                    'notifiable_type' => 'topic',
                    'notifiable_id'   => $topic->id,
                ]);
            }
        }

        $post->update([
            'url'           => $validated['url'],
            'category'      => $validated['category'],
            'comment'       => $validated['comment'] ?? null,
            'is_published'  => $validated['is_published'],
            'title'         => $title,
            'thumbnail_url' => $thumbnail_url,
        ]);

        return response()->json($post->fresh());
    }

    // 投稿削除（自分の投稿のみ）
    public function destroy(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $post->delete();
        return response()->json(['message' => '削除しました']);
    }
}
