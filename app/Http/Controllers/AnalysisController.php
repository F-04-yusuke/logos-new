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
}