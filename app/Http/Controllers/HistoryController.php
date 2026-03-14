<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // ログインユーザーが閲覧したトピックを、最後に見た順（降順）で12件ずつ取得
        $viewedTopics = auth()->user()->viewedTopics()->paginate(12);
        
        return view('history.index', compact('viewedTopics'));
    }
}