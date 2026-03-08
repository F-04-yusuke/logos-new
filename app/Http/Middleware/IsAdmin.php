<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 今ログインしているユーザーのIDが「1（一番最初に登録した管理者）」なら、そのまま通す
        if (auth()->check() && auth()->id() === 1) {
            return $next($request);
        }

        // それ以外の一般ユーザーがアクセスしようとしたら、403エラー（アクセス拒否）で追い返す
        abort(403, 'このページは管理者専用です。');
    }
}