<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;

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
        // Policyで「作成者本人 or PRO会員」のみ閲覧を許可
        // 無料会員が直接URLをたたいた場合も403でブロック
        Gate::authorize('view', $analysis);

        return view('analyses.show', compact('analysis'));
    }

    // Gemini APIと通信してAIの回答を取得する処理
    public function aiAssist(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'context' => 'nullable|string', // 画面の現在のツリーや表のデータ
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'APIキーが設定されていません。システム管理者にお問い合わせください。'], 500);
        }

        // プロンプトの組み立て（システムプロンプトとしてコンサルの役割を与えます）
        $fullPrompt = "あなたは政治・経済の議論を整理するプロのコンサルタントです。\n" 
                    . "以下の【現在の状況・データ】を踏まえて、ユーザーの【指示】に的確に答えてください。\n\n"
                    . "【現在の状況・データ】\n" . $request->context . "\n\n"
                    . "【指示】\n" . $request->prompt;

        // リストに確実に存在していた「gemini-2.5-flash」を使用します！
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'AIからの回答を取得できませんでした。';
            return response()->json(['reply' => $text]);
        }

        // エラー時の詳細表示
        $errorBody = $response->body();
        return response()->json(['error' => "APIエラー詳細: " . $errorBody], 500);
    }

    // 他人のデータを勝手に消せないようにする
    public function destroy(\App\Models\Analysis $analysis)
    {
        // セキュリティ：自分の分析データでなければ403エラーを返す
        if ($analysis->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }

        $analysis->delete();

        return back()->with('status', '分析・図解を削除しました。');
    }

    // いいねの切り替え（追加・削除）処理
    public function toggleLike(\App\Models\Analysis $analysis)
    {
        $user = auth()->user();

        if ($analysis->isLikedBy($user)) {
            // すでにいいねしていれば解除
            $analysis->likes()->detach($user->id);
        } else {
            // まだなら、いいねを登録
            $analysis->likes()->attach($user->id);
        }

        return back(); // 元の画面に戻る
    }

    // 🌟 上書き：分析・図解の編集画面（実際のツール画面）を表示する
    public function edit(\App\Models\Analysis $analysis)
    {
        // 他人のデータは編集できないようにブロック
        if ($analysis->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }

        // ツール種類に応じて、元の作成画面を「編集モード」として開く
        if ($analysis->type === 'tree') return view('tools.tree', compact('analysis'));
        if ($analysis->type === 'matrix') return view('tools.matrix', compact('analysis'));
        if ($analysis->type === 'swot') return view('tools.swot', compact('analysis'));

        return back();
    }

    // 🌟 上書き：分析・図解のデータ（中身）を完全に上書き保存する
    public function update(\Illuminate\Http\Request $request, \App\Models\Analysis $analysis)
    {
        if ($analysis->user_id !== auth()->id()) {
            abort(403, '権限がありません。');
        }

        // タイトルと中身(data)を上書き保存
        $analysis->update([
            'title' => $request->title ?? $analysis->title,
            'data' => $request->data,
        ]);

        return response()->json(['message' => '分析データを上書き保存しました！']);
    }
}