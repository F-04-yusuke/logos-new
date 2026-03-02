<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// /topics というURLにアクセスが来たら、TopicControllerの「index」という処理を呼び出す
// ->middleware('auth') を付けることで、「ログインしているユーザーだけ」がアクセスできるようにブロックできます
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index')->middleware('auth');

require __DIR__.'/auth.php';
