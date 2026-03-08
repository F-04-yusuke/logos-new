<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // カテゴリ一覧の表示（🌟 並び順を追加）
    public function index()
    {
        // sort_order（表示順）の数字が小さい順に並べて取得する
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('categories.index', compact('categories'));
    }

    // 新規作成（🌟 表示順の保存を追加）
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0, // 空欄なら0を入れる
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('status', 'カテゴリを追加しました！');
    }

    // カテゴリの編集を保存する処理
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
        ]);

        $category->update([
            'name' => $request->name,
            'sort_order' => $request->sort_order,
        ]);

        return back()->with('status', 'カテゴリを更新しました！');
    }

    // カテゴリを削除する処理
    public function destroy(Category $category)
    {
        // 大分類を消した場合は、ぶら下がっている中分類も自動で一緒に消えます（データベースのcascade設定のおかげです）
        $category->delete();
        return back()->with('status', 'カテゴリを削除しました。');
    }

    // 一般向けの「カテゴリ一覧画面」を表示する処理（復活＆並び順対応）
    public function list()
    {
        // 大分類と中分類を、設定した「表示順（sort_order）」通りに取得する
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('categories.list', compact('categories'));
    }

}