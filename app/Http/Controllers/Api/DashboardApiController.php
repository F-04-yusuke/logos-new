<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    // ダッシュボード
    public function index(Request $request)
    {
        $user = $request->user();

        $posts = $user->posts()
            ->where('is_published', true)
            ->with(['topic:id,title', 'user:id,name,avatar'])
            ->withCount('likes')
            ->latest()
            ->get();

        $drafts = $user->posts()
            ->where('is_published', false)
            ->with(['topic:id,title', 'user:id,name,avatar'])
            ->withCount('likes')
            ->latest()
            ->get();

        $comments = $user->comments()
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar',
                'topic:id,title',
                'replies' => function ($q) { $q->oldest()->with('user:id,name,avatar')->withCount('likes'); },
            ])
            ->withCount('likes')
            ->latest()
            ->get();

        $topics = $user->topics()
            ->latest()
            ->get(['id', 'title', 'created_at']);

        $analyses = \App\Models\Analysis::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'title', 'type', 'is_published', 'topic_id', 'created_at']);

        return response()->json([
            'posts'       => $posts,
            'drafts'      => $drafts,
            'draft_count' => $drafts->count(),
            'comments'    => $comments,
            'analyses'    => $analyses,
            'topics'      => $topics,
        ]);
    }
}
