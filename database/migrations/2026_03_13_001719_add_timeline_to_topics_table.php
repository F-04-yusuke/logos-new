<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            // 🌟 JSON形式（配列データ）をそのまま保存できる枠を作ります
            $table->json('timeline')->nullable()->after('content')->comment('AIが生成した時系列データ');
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('timeline');
        });
    }
};
