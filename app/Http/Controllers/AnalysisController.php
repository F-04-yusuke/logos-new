<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analysis;

class AnalysisController extends Controller
{
    public function store(Request $request)
    {
        // 送られてきたデータを検証
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:tree,matrix,swot',
            'data' => 'required|array', // JSONは配列として受け取る
        ]);

        // データベースに保存
        $analysis = Analysis::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'data' => $validated['data'],
            'is_published' => false,
        ]);

        // 保存成功のレスポンスを返す
        return response()->json([
            'message' => '保存しました！',
            'id' => $analysis->id
        ]);
    }

    // トピックへ公開（連携）する処理
    public function publish(Request $request, Analysis $analysis)
    {
        // 他人のデータを勝手に公開できないようにブロック
        if ($analysis->user_id !== auth()->id()) {
            abort(403);
        }

        // 連携先のトピックIDが送られてきているか確認
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
        ]);

        // DBのデータを「公開状態」かつ「このトピックに紐付け」に更新
        $analysis->update([
            'topic_id' => $request->topic_id,
            'is_published' => true,
        ]);

        return back()->with('status', '分析・図解をこのトピックに公開しました！');
    }

    // 図解の閲覧ページを表示
    public function show(Analysis $analysis)
    {
        // 非公開（下書き）のものは、作成者本人しか見られないようにブロック
        if (!$analysis->is_published && $analysis->user_id !== auth()->id()) {
            abort(403);
        }

        return view('analyses.show', compact('analysis'));
    }
}