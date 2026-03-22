<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
