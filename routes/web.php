<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
// ファイルの上部（他のuse宣言が並んでいる場所）に追加
use App\Http\Controllers\AnalysisController;

Route::get('/', function () {
    return view('welcome');
});

// ダッシュボード画面を表示するルール（コントローラーを経由するように変更）
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// /topics というURLにアクセスが来たら、TopicControllerの「index」という処理を呼び出す
// ->middleware('auth') を付けることで、「ログインしているユーザーだけ」がアクセスできるようにブロックできます
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index')->middleware('auth');

// トピック作成画面を表示するルール
Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create')->middleware('auth');

// トピックをデータベースに保存するルール
Route::post('/topics', [TopicController::class, 'store'])->name('topics.store')->middleware('auth');

// トピック詳細画面を表示するルール（※必ず create のルールより下に書いてください）
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show')->middleware('auth');

// AIによる時系列生成ルート
Route::post('/topics/{topic}/timeline', [TopicController::class, 'generateTimeline'])->name('topics.timeline')->middleware('auth');

// AIによる時系列アップデート（更新）ルート
Route::post('/topics/{topic}/timeline-update', [\App\Http\Controllers\TopicController::class, 'updateTimeline'])->name('topics.timeline_update')->middleware('auth');

// トピックへの画像直接アップロードルート
Route::post('/topics/{topic}/analyses/image', [\App\Http\Controllers\TopicImageController::class, 'store'])->name('topics.analyses.image')->middleware('auth');

// トピックの詳細画面から「投稿（Post）」を保存するためのルール
Route::post('/topics/{topic}/posts', [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store')->middleware('auth');

// いいねボタンを押したときのルール
Route::post('/posts/{post}/like', [\App\Http\Controllers\LikeController::class, 'store'])->name('likes.store')->middleware('auth');

// マイページ用：参考になった（いいねした）エビデンス一覧を表示するルール
Route::get('/liked-posts', [\App\Http\Controllers\LikeController::class, 'index'])->name('likes.index')->middleware('auth');

// トピックの保存（ブックマーク）と解除のルール
Route::post('/topics/{topic}/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'store'])->name('bookmarks.store')->middleware('auth');
Route::delete('/topics/{topic}/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'destroy'])->name('bookmarks.destroy')->middleware('auth');

// マイページのボタンを機能させるための「編集・削除」の経路を追加
Route::get('/topics/{topic}/edit', [\App\Http\Controllers\TopicController::class, 'edit'])->name('topics.edit')->middleware('auth');

// トピックの編集内容を「保存（更新）」するための経路
Route::patch('/topics/{topic}', [\App\Http\Controllers\TopicController::class, 'update'])->name('topics.update')->middleware('auth');

// エビデンス（投稿）の「編集画面」と「更新処理」の経路
Route::get('/posts/{post}/edit', [\App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit')->middleware('auth');
Route::patch('/posts/{post}', [\App\Http\Controllers\PostController::class, 'update'])->name('posts.update')->middleware('auth');
Route::delete('/topics/{topic}', [\App\Http\Controllers\TopicController::class, 'destroy'])->name('topics.destroy')->middleware('auth');
Route::delete('/posts/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');

// 一般ユーザー向け：カテゴリ一覧画面を表示するルール
Route::get('/category-list', [\App\Http\Controllers\CategoryController::class, 'list'])->name('categories.list')->middleware('auth');

// 管理者用：カテゴリ（大分類・中分類）の管理画面と保存処理（※IsAdminの関所を追加）
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])
    ->name('categories.index')
    ->middleware(['auth', \App\Http\Middleware\IsAdmin::class]);

Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])
    ->name('categories.store')
    ->middleware(['auth', \App\Http\Middleware\IsAdmin::class]);

// カテゴリの更新（編集）と削除のルール
Route::patch('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])
    ->name('categories.update')->middleware(['auth', \App\Http\Middleware\IsAdmin::class]);

Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])
    ->name('categories.destroy')->middleware(['auth', \App\Http\Middleware\IsAdmin::class]);

// コメント機能のルール
Route::post('/topics/{topic}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store')->middleware('auth');
Route::patch('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');
Route::post('/comments/{comment}/like', [\App\Http\Controllers\CommentController::class, 'toggleLike'])->name('comments.like')->middleware('auth');

// 分析ツール (PROプラン用)
Route::middleware('auth')->prefix('tools')->name('tools.')->group(function () {
    Route::get('/tree', function () { return view('tools.tree'); })->name('tree');
    Route::get('/matrix', function () { return view('tools.matrix'); })->name('matrix');
    Route::get('/swot', function () { return view('tools.swot'); })->name('swot');
// 保存用のPOSTルート
    Route::post('/store', [AnalysisController::class, 'store'])->name('store');
// 公開用のルート
    Route::post('/analyses/{analysis}/publish', [AnalysisController::class, 'publish'])->name('publish');
// AIアシスタント用のルート
    Route::post('/ai-assist', [AnalysisController::class, 'aiAssist'])->name('ai_assist');
});

// 分析・図解 (閲覧用)
Route::middleware('auth')->group(function () {
    Route::get('/analyses/{analysis}', [App\Http\Controllers\AnalysisController::class, 'show'])->name('analyses.show');
});
// 分析・図解の削除ルート
Route::delete('/analyses/{analysis}', [\App\Http\Controllers\AnalysisController::class, 'destroy'])->name('analyses.destroy');

// 分析・図解のいいねルート
Route::post('/analyses/{analysis}/like', [\App\Http\Controllers\AnalysisController::class, 'toggleLike'])->name('analyses.like')->middleware('auth');

// 補足機能のルート
Route::post('/posts/{post}/supplement', [\App\Http\Controllers\SupplementController::class, 'storePost'])->name('posts.supplement')->middleware('auth');
Route::post('/analyses/{analysis}/supplement', [\App\Http\Controllers\SupplementController::class, 'storeAnalysis'])->name('analyses.supplement')->middleware('auth');

// コメントへの返信ルート
Route::post('/comments/{comment}/reply', [\App\Http\Controllers\CommentController::class, 'reply'])->name('comments.reply')->middleware('auth');

require __DIR__.'/auth.php';

