<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    // カテゴリ作成（管理者専用）
    public function store(Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'parent_id'  => 'nullable|integer|exists:categories,id',
        ]);
        $category = Category::create($data);
        return response()->json($category, 201);
    }

    // カテゴリ更新（管理者専用）
    public function update(Request $request, Category $category)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);
        $category->update($data);
        return response()->json($category);
    }

    // カテゴリ削除（管理者専用）
    public function destroy(Request $request, Category $category)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $category->delete();
        return response()->json(['message' => '削除しました']);
    }
}
