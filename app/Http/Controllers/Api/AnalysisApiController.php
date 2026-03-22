<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAnalysisRequest;
use App\Http\Requests\Api\SupplementRequest;
use App\Http\Requests\Api\UpdateAnalysisRequest;
use App\Models\Analysis;
use App\Models\Notification;
use App\Models\Topic;
use Illuminate\Http\Request;

class AnalysisApiController extends Controller
{
    // 分析ツール: 一件取得（編集用・閲覧用）
    public function show(Request $request, Analysis $analysis)
    {
        $user = $request->user();
        $data = $analysis->toArray();
        $data['user'] = \App\Models\User::select('id', 'name', 'avatar')->find($analysis->user_id);
        $data['topic'] = $analysis->topic_id
            ? Topic::select('id', 'title')->find($analysis->topic_id)
            : null;
        $data['likes_count']    = $analysis->likes()->count();
        $data['is_liked_by_me'] = $analysis->likes()->where('user_id', $user->id)->exists();
        return response()->json($data);
    }

    // 分析ツール: 新規保存（PRO限定）
    public function store(StoreAnalysisRequest $request)
    {
        $user = $request->user();
        if (!$user->is_pro) {
            return response()->json(['message' => 'PRO会員限定の機能です'], 403);
        }
        $data = $request->validated();
        $analysis = Analysis::create([
            'user_id'      => $user->id,
            'title'        => $data['title'],
            'type'         => $data['type'],
            'data'         => $data['data'],
            'is_published' => false,
        ]);
        return response()->json(['message' => '保存しました！', 'id' => $analysis->id], 201);
    }

    // 分析ツール: 上書き保存
    public function update(UpdateAnalysisRequest $request, Analysis $analysis)
    {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $data = $request->validated();
        $analysis->update([
            'title' => $data['title'] ?? $analysis->title,
            'data'  => $data['data'],
        ]);
        return response()->json(['message' => '上書き保存しました！']);
    }

    // 分析ツール: 削除
    public function destroy(Request $request, Analysis $analysis)
    {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $analysis->delete();
        return response()->json(['message' => '削除しました']);
    }

    // 分析をトピックに公開
    public function publish(Request $request, Analysis $analysis)
    {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        $data = $request->validate(['topic_id' => 'required|integer|exists:topics,id']);
        $analysis->update(['topic_id' => $data['topic_id'], 'is_published' => true]);
        return response()->json(['message' => '公開しました']);
    }

    // 分析いいね（トグル）
    public function like(Request $request, Analysis $analysis)
    {
        $user = $request->user();
        if ($analysis->likes()->where('user_id', $user->id)->exists()) {
            $analysis->likes()->detach($user->id);
            $liked = false;
        } else {
            $analysis->likes()->attach($user->id);
            $liked = true;
            // 通知：分析作成者が別ユーザーの場合のみ
            if ($analysis->user_id !== $user->id) {
                Notification::create([
                    'user_id'         => $analysis->user_id,
                    'actor_id'        => $user->id,
                    'type'            => 'analysis_like',
                    'notifiable_type' => 'App\\Models\\Analysis',
                    'notifiable_id'   => $analysis->id,
                ]);
            }
        }
        return response()->json(['liked' => $liked, 'likes_count' => $analysis->likes()->count()]);
    }

    // 分析補足（投稿者本人・1回のみ）
    public function supplement(SupplementRequest $request, Analysis $analysis)
    {
        if ($analysis->user_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }
        if ($analysis->supplement !== null) {
            return response()->json(['message' => '補足はすでに追加済みです'], 422);
        }
        $data = $request->validated();
        $analysis->supplement = $data['supplement'];
        $analysis->save();
        return response()->json(['supplement' => $analysis->supplement]);
    }

    // ユーザーの分析一覧（モーダル選択用）
    public function userAnalyses(Request $request)
    {
        $user = $request->user();
        $analyses = Analysis::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'title', 'type', 'is_published', 'topic_id', 'created_at']);
        return response()->json($analyses);
    }

    // オリジナル図解（画像）をトピックに直接アップロード・公開（PRO限定）
    public function storeImage(Request $request, Topic $topic)
    {
        $user = $request->user();
        if (!$user->is_pro) {
            return response()->json(['message' => 'PRO会員限定の機能です'], 403);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        $path = $request->file('image')->store('analyses', 'public');
        $analysis = Analysis::create([
            'user_id'      => $user->id,
            'topic_id'     => $topic->id,
            'title'        => $request->title,
            'type'         => 'image',
            'data'         => ['image_path' => $path],
            'is_published' => true,
        ]);
        return response()->json(['message' => 'オリジナル図解を公開しました！', 'id' => $analysis->id], 201);
    }

    // AIアシスタント (Gemini)
    public function aiAssist(Request $request)
    {
        $request->validate([
            'prompt'  => 'required|string|max:5000',
            'context' => 'nullable|string|max:10000',
        ]);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini APIキーが設定されていません'], 500);
        }

        $fullPrompt = "あなたは政治・経済の議論を整理するプロのコンサルタントです。\n"
                    . "以下の【現在の状況・データ】を踏まえて、ユーザーの【指示】に的確に答えてください。\n\n"
                    . "【現在の状況・データ】\n" . ($request->context ?? '') . "\n\n"
                    . "【指示】\n" . $request->prompt;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $fullPrompt]]]]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'AIからの回答を取得できませんでした。';
            return response()->json(['reply' => $text]);
        }

        return response()->json(['error' => 'APIエラー: ' . $response->body()], 500);
    }
}
