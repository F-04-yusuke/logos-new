<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Http\Controllers\Controller;

class TopicApiController extends Controller
{
    public function index()
    {
        $topics = Topic::with(['user:id,name', 'categories'])
            ->withCount(['posts', 'comments'])
            ->latest()
            ->paginate(20);

        return response()->json($topics);
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
