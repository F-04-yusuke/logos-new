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
        $topic->load([
            'user:id,name',
            'categories',
            'posts' => fn($q) => $q
                ->where('is_published', true)
                ->with('user:id,name')
                ->withCount('likes'),
            'comments' => fn($q) => $q
                ->whereNull('parent_id')
                ->with(['user:id,name', 'replies.user:id,name']),
        ]);

        return response()->json($topic);
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
