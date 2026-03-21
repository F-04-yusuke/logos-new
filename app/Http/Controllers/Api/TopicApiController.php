<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Http\Controllers\Controller;

class TopicApiController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
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
}
