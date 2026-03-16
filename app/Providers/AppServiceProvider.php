<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Analysis;
use App\Policies\AnalysisPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 図解詳細の閲覧権限: 作成者 or PRO会員
        Gate::policy(Analysis::class, AnalysisPolicy::class);
    }
}
