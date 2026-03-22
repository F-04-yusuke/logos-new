<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    // カテゴリ作成（管理者専用）
    public function store(StoreCategoryRequest $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validated();
        $category = Category::create($data);
        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    // カテゴリ更新（管理者専用）
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }
        $data = $request->validated();
        $category->update($data);
        return new CategoryResource($category);
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
