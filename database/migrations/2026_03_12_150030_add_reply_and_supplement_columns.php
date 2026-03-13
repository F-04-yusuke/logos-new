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
        // 1. 情報（posts）テーブルに補足枠を追加
        Schema::table('posts', function (Blueprint $table) {
            $table->text('supplement')->nullable()->after('comment')->comment('投稿者による補足（1回のみ）');
        });

        // 2. 分析（analyses）テーブルに補足枠を追加
        Schema::table('analyses', function (Blueprint $table) {
            $table->text('supplement')->nullable()->after('data')->comment('投稿者による補足（1回のみ）');
        });

        // 3. コメント（comments）テーブルに「親コメントID（返信先）」を追加
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete()->after('topic_id')->comment('返信先のコメントID');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('supplement');
        });
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('supplement');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
