<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic; // 🌟 データベースのTopicテーブルを操作するための事前宣言

class TopicController extends Controller
{

    // トピック一覧画面（トップページ）を表示する処理
    // 新しく Request $request を追加して、検索窓からの文字を受け取れるようにします
    public function index(\Illuminate\Http\Request $request)
    {
        // ① まず、「トピックを新しい順に並べる」という基本の検索準備をします
        // （※ まだ get() を使ってデータを取り出しません。絞り込むかもしれないからです）
        $query = \App\Models\Topic::latest();

        // ② もし、画面から「search」という名前のデータ（キーワード）が送られてきていたら…
        if ($request->filled('search')) {
            
            $keyword = $request->search;

            // ③ そのキーワードが「タイトル」または「内容」に含まれているかを探す（曖昧検索）
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('content', 'like', '%' . $keyword . '%');
            });
        }

        // カテゴリでの絞り込み 
        // URLに ?category=1 のようにIDが送られてきたら発動します
        $selectedCategory = null;
        if ($request->filled('category')) {
            $categoryId = $request->category;
            // 画面に「〇〇のトピック」と表示するために、カテゴリの情報を取得しておく
            $selectedCategory = \App\Models\Category::find($categoryId);
            
            // 中間テーブル（架け橋）を渡って、このカテゴリIDを持っているトピックだけを探す魔法
            if ($selectedCategory) {
                $query->whereHas('categories', function($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            }
        }

        // ④ 絞り込みが終わった状態のデータを、一気にデータベースから取得します
        $topics = $query->get();

        // ⑤ 取得したデータを、トップページ（topics.index）に渡して表示します
        return view('topics.index', compact('topics'));
    }

    // 🔽 トピック新規作成画面を表示する処理（create）
    public function create()
    {
        // 大分類（親がいないカテゴリ）と、それに紐づく中分類をセットで取得して画面に渡す
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
        
        return view('topics.create', compact('categories'));
    }

    // 🔽 新しいトピックをデータベースに保存する処理（store）
    public function store(\Illuminate\Http\Request $request)
    {
        // 1. 入力チェック（バリデーション）
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // 🌟 変更：カテゴリIDの配列を受け取り、最大2つまでに制限する
            'category_ids' => 'required|array|max:2|min:1', 
            'category_ids.*' => 'exists:categories,id', // 存在するカテゴリかチェック
        ], [
            // エラーメッセージを日本語で分かりやすくする
            'category_ids.required' => 'カテゴリを少なくとも1つ選択してください。',
            'category_ids.max' => 'カテゴリは最大2つまでしか選択できません。',
        ]);

        // 2. トピック本体を保存する
        $topic = \App\Models\Topic::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // 3. 🌟 新機能：選ばれたカテゴリ（複数）を、中間テーブルを使ってトピックと紐づける
        // attach() という魔法の言葉を使うだけで、架け橋のデータが自動的に作られます
        $topic->categories()->attach($request->category_ids);

        // 4. 保存が終わったら、そのトピックの詳細画面へ移動
        return redirect()->route('topics.show', $topic);
    }

    //  トピックの詳細画面を表示する処理（show）
    // （検索条件を受け取るために Request $request を引数に追加しています）
    public function show(\Illuminate\Http\Request $request, \App\Models\Topic $topic)
    {
        // ① まず、このトピックに紐づくエビデンス（投稿）を取得する準備をします
        $query = $topic->posts();

        // ② メディア分類（YouTube、記事など）での絞り込み
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // ③ 並び替え（ソート）の処理
        if ($request->filled('sort')) {
            if ($request->sort === 'oldest') {
                // 古い順
                $query->oldest();
            } elseif ($request->sort === 'popular') {
                // 🌟 人気順（いいねの数が多い順）
                // withCount('likes') でいいねの数を計算し、orderBy で多い順（desc）に並べ替えます
                $query->withCount('likes')->orderBy('likes_count', 'desc');
            } else {
                // 新着順（newest）
                $query->latest();
            }
        } else {
            // 何も指定されていない場合のデフォルトは「新着順」
            $query->latest();
        }

        // ④ 絞り込みと並び替えが終わった状態のデータを取得
        $posts = $query->get();

        // ⑤ 画面に渡す
        return view('topics.show', compact('topic', 'posts'));
    }

// 編集画面（View）を表示する処理（edit）
    public function edit(\App\Models\Topic $topic)
    {
        // セキュリティ対策：他人のトピックの編集画面は開けないようにブロック
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは編集できません。');
        }
        
        // 🌟 追加：編集画面でもカテゴリを選べるように、カテゴリ一覧を取得して渡す
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
        
        return view('topics.edit', compact('topic', 'categories'));
    }

    // 編集された新しい内容をデータベースに上書き保存する処理（update）
    public function update(\Illuminate\Http\Request $request, \App\Models\Topic $topic)
    {
        // セキュリティ対策
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは編集できません。');
        }

        // 🌟 追加：入力内容とカテゴリが正しく選ばれているかチェック
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_ids' => 'required|array|max:2|min:1', 
            'category_ids.*' => 'exists:categories,id',
        ], [
            'category_ids.required' => 'カテゴリを少なくとも1つ選択してください。',
            'category_ids.max' => 'カテゴリは最大2つまでしか選択できません。',
        ]);

        // データベースの情報を新しい文字で上書き更新する
        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // 🌟 追加：選ばれたカテゴリを上書き保存する
        // sync() という魔法を使うと、「古い紐づけを消して、新しいIDの配列に入れ替える」作業を自動でやってくれます
        $topic->categories()->sync($request->category_ids);

        // 更新が終わったら、そのトピックの詳細画面へ自動で移動する
        return redirect()->route('topics.show', $topic);
    }

    // ここから追加：トピックを削除する処理（destroy）
    // 引数の \App\Models\Topic $topic には、ボタンを押したトピックのデータが自動的に入ってきます
    public function destroy(\App\Models\Topic $topic)
    {
        // ① セキュリティ対策（超重要）：自分のトピック以外は削除できないようにブロックする
        // URLを直接いじって他人のトピックを消そうとする悪意ある攻撃を防ぎます
        if ($topic->user_id !== auth()->id()) {
            abort(403, '他のユーザーのトピックは削除できません。');
        }

        // ② データベースからこのトピックを完全に削除する
        $topic->delete();

        // ③ 削除が完了したら、元の画面（ダッシュボード）にそのまま戻る
        return back();
    }
}