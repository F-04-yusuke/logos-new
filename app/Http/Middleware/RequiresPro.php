<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresPro
{
    public function handle(Request $request, Closure $next): Response
    {
        // PRO会員でなければブロック
        if (!$request->user()?->is_pro) {
            // Next.js移行後のAPIリクエストにはJSONで返す
            if ($request->expectsJson()) {
                return response()->json([
                    'error'       => 'PRO会員限定の機能です。',
                    'upgrade_url' => route('upgrade.show'),
                ], 403);
            }

            // Blade: 直接URLアクセスされた場合はリダイレクト
            return redirect()->route('topics.index')
                ->with('pro_required', 'この機能はPRO会員限定です。');
        }

        return $next($request);
    }
}
