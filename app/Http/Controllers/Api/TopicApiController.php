<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use App\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TopicApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Topic::with(['user:id,name', 'categories'])
            ->withCount(['posts', 'comments']);

        match ($request->query('sort')) {
            'popular' => $query->orderByDesc('posts_count'),
            'oldest'  => $query->oldest(),
            default   => $query->latest(),
        };

        return response()->json($query->paginate(20));
    }

    public function show(Topic $topic)
    {
        $authUser = auth('sanctum')->user();

        $topic->load([
            'user:id,name',
            'categories',
            'posts' => fn($q) => $q
                ->where('is_published', true)
                ->with('user:id,name')
                ->withCount('likes'),
            'comments' => fn($q) => $q
                ->whereNull('parent_id')
                ->withCount('likes')
                ->with([
                    'user:id,name',
                    'replies' => fn($rq) => $rq
                        ->withCount('likes')
                        ->with('user:id,name'),
                ]),
            'analyses' => fn($q) => $q
                ->where('is_published', true)
                ->with('user:id,name,avatar')
                ->withCount('likes')
                ->latest(),
        ]);

        $data = $topic->toArray();
        $data['user_has_commented'] = false;
        $data['is_bookmarked'] = false;

        if ($authUser) {
            // 閲覧履歴を記録
            \Illuminate\Support\Facades\DB::table('topic_views')->updateOrInsert(
                ['user_id' => $authUser->id, 'topic_id' => $topic->id],
                ['last_viewed_at' => now(), 'updated_at' => now()]
            );

            $data['user_has_commented'] = $topic->comments()
                ->where('user_id', $authUser->id)
                ->whereNull('parent_id')
                ->exists();

            $data['is_bookmarked'] = $topic->isSavedBy($authUser);

            // Post likes
            $likedPostIds = \App\Models\Like::where('user_id', $authUser->id)
                ->whereIn('post_id', $topic->posts->pluck('id'))
                ->pluck('post_id')
                ->toArray();

            foreach ($data['posts'] as &$post) {
                $post['is_liked_by_me'] = in_array($post['id'], $likedPostIds);
            }
            unset($post);

            // Comment + reply likes
            $allCommentIds = collect($data['comments'])->pluck('id')->toArray();
            $replyIds = collect($data['comments'])
                ->flatMap(fn($c) => collect($c['replies'] ?? [])->pluck('id'))
                ->toArray();
            $likedCommentIds = \DB::table('comment_likes')
                ->where('user_id', $authUser->id)
                ->whereIn('comment_id', array_merge($allCommentIds, $replyIds))
                ->pluck('comment_id')
                ->toArray();

            foreach ($data['comments'] as &$comment) {
                $comment['is_liked_by_me'] = in_array($comment['id'], $likedCommentIds);
                foreach ($comment['replies'] as &$reply) {
                    $reply['is_liked_by_me'] = in_array($reply['id'], $likedCommentIds);
                }
                unset($reply);
            }
            unset($comment);

            // Analysis likes
            $analysisIds = collect($data['analyses'] ?? [])->pluck('id')->toArray();
            if (!empty($analysisIds)) {
                $likedAnalysisIds = \Illuminate\Support\Facades\DB::table('analysis_likes')
                    ->where('user_id', $authUser->id)
                    ->whereIn('analysis_id', $analysisIds)
                    ->pluck('analysis_id')
                    ->toArray();
                foreach ($data['analyses'] as &$analysis) {
                    $analysis['is_liked_by_me'] = in_array($analysis['id'], $likedAnalysisIds);
                }
                unset($analysis);
            }
        }

        return response()->json($data);
    }

    public function update(Request $request, Topic $topic)
    {
        // 作成者チェック
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '編集権限がありません'], 403);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'required|string|max:20000',
            'category_ids'     => 'required|array|min:1|max:2',
            'category_ids.*'   => 'integer|exists:categories,id',
            'timeline'         => 'nullable|array',
            'timeline.*.date'  => 'nullable|string|max:50',
            'timeline.*.event' => 'nullable|string|max:500',
            'timeline.*.is_ai' => 'nullable|boolean',
        ]);

        $topic->update([
            'title'    => $validated['title'],
            'content'  => $validated['content'],
            'timeline' => $validated['timeline'] ?? null,
        ]);

        $topic->categories()->sync($validated['category_ids']);

        return response()->json($topic->load(['user:id,name', 'categories']));
    }

    public function store(Request $request)
    {
        // PROチェック
        if (!$request->user()->is_pro) {
            return response()->json(['message' => 'PRO会員のみトピックを作成できます'], 403);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'required|string|max:20000',
            'category_ids'     => 'required|array|min:1|max:2',
            'category_ids.*'   => 'integer|exists:categories,id',
            'timeline'         => 'nullable|array',
            'timeline.*.date'  => 'nullable|string|max:255',
            'timeline.*.event' => 'nullable|string|max:1000',
            'timeline.*.is_ai' => 'nullable|boolean',
        ]);

        $topic = Topic::create([
            'user_id'  => $request->user()->id,
            'title'    => $validated['title'],
            'content'  => $validated['content'],
            'timeline' => $validated['timeline'] ?? null,
        ]);

        $topic->categories()->attach($validated['category_ids']);

        return response()->json($topic->load(['user:id,name', 'categories']), 201);
    }

    // トピック削除（自分のトピックのみ）
    public function destroy(Request $request, Topic $topic)
    {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $topic->delete();
        return response()->json(['message' => '削除しました']);
    }

    // ブックマーク（トグル）
    public function bookmark(Request $request, Topic $topic)
    {
        $user         = $request->user();
        $isBookmarked = $topic->isSavedBy($user);
        if ($isBookmarked) {
            DB::table('bookmarks')->where('user_id', $user->id)->where('topic_id', $topic->id)->delete();
            $bookmarked = false;
        } else {
            DB::table('bookmarks')->insert(['user_id' => $user->id, 'topic_id' => $topic->id, 'created_at' => now(), 'updated_at' => now()]);
            $bookmarked = true;
            // 通知：トピック作成者が別ユーザーの場合のみ
            if ($topic->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $topic->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'topic_bookmark',
                    'notifiable_type' => 'App\\Models\\Topic',
                    'notifiable_id'   => $topic->id,
                ]);
            }
        }
        return response()->json(['bookmarked' => $bookmarked]);
    }

    // 時系列: AIで自動生成（トピック作成者限定・未生成の場合のみ）
    public function timelineGenerate(Request $request, Topic $topic)
    {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($topic->timeline) {
            return response()->json(['message' => 'すでに時系列は生成されています'], 422);
        }

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['message' => 'APIキーが設定されていません'], 500);
        }

        $prompt = <<<EOT
以下のトピックの「前提となる歴史的背景や時系列」を抽出・推測し、JSON配列形式で出力してください。
トピックから直接読み取れない場合は、一般的な歴史的事実に基づき、最大5件程度の重要な出来事を挙げてください。

【トピック名】: {$topic->title}
【トピック概要】: {$topic->content}

【出力形式の絶対ルール】
必ず以下の形式のJSON配列のみを出力し、それ以外の説明文やマークダウン（\`\`\`json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約"},
    {"date": "YYYY年MM月", "event": "出来事の短い要約"}
]
EOT;

        try {
            $response = Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2],
                ]
            );

            if ($response->successful()) {
                $text     = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text     = preg_replace('/```json\n?|```\n?/', '', $text);
                $text     = trim($text);
                $timeline = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timeline)) {
                    $timeline = array_map(fn($item) => array_merge($item, ['is_ai' => true]), $timeline);
                    $topic->update(['timeline' => $timeline]);
                    return response()->json(['timeline' => $topic->fresh()->timeline]);
                }
                return response()->json(['message' => 'AIの回答を解析できませんでした'], 500);
            }
            return response()->json(['message' => 'AIとの通信に失敗しました'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました: ' . $e->getMessage()], 500);
        }
    }

    // 時系列: 最新エビデンスからAI更新（トピック作成者限定・生成済みの場合のみ）
    public function timelineUpdate(Request $request, Topic $topic)
    {
        if ($topic->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if (!$topic->timeline) {
            return response()->json(['message' => 'まずは初期の時系列を生成してください'], 422);
        }

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['message' => 'APIキーが設定されていません'], 500);
        }

        $currentTimeline = json_encode($topic->timeline, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $postsData = "";
        foreach ($topic->posts()->where('is_published', true)->latest()->take(10)->get() as $post) {
            $postsData .= "- URL: {$post->url}\n  コメント: {$post->comment}\n\n";
        }
        if (empty($postsData)) {
            $postsData = "新しいエビデンスは特にありません。";
        }

        $prompt = <<<EOT
以下のトピックに関する「既存の時系列データ」と「最近追加されたエビデンス（情報）」を提供します。
これらを統合・分析し、必要であれば新しい出来事を時系列に追加して、最新版のJSON配列として出力してください。

【トピック名】: {$topic->title}
【トピック概要】: {$topic->content}

【既存の時系列データ】:
{$currentTimeline}

【新しく追加されたエビデンス】:
{$postsData}

【出力形式の絶対ルール】
1. 既存のデータの中で "is_ai": false となっている項目はユーザーが手動で編集した重要なデータです。絶対に削除や改変を行わず、そのまま残してください。
2. 新しく追加する項目、またはAIが再構成した項目には "is_ai": true を設定してください。
3. 必ず以下の形式のJSON配列のみを出力し、マークダウン（\`\`\`json など）は一切含めないでください。
[
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse},
    {"date": "YYYY年MM月", "event": "出来事の短い要約", "is_ai": trueまたはfalse}
]
EOT;

        try {
            $response = Http::post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2],
                ]
            );

            if ($response->successful()) {
                $text     = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text     = preg_replace('/```json\n?|```\n?/', '', $text);
                $text     = trim($text);
                $timeline = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($timeline)) {
                    $timeline = array_map(function ($item) {
                        $item['is_ai'] = isset($item['is_ai']) ? filter_var($item['is_ai'], FILTER_VALIDATE_BOOLEAN) : true;
                        return $item;
                    }, $timeline);
                    $topic->update(['timeline' => $timeline]);
                    return response()->json(['timeline' => $topic->fresh()->timeline]);
                }
                return response()->json(['message' => 'AIの回答を解析できませんでした'], 500);
            }
            return response()->json(['message' => 'AIとの通信に失敗しました'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました: ' . $e->getMessage()], 500);
        }
    }
}
