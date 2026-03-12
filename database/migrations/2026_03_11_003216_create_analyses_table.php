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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // どのトピックに対する分析か（後から紐付けられるように nullable にしておきます）
            $table->foreignId('topic_id')->nullable()->constrained()->cascadeOnDelete();

            // 分析のタイトル（例：「日本の原発再稼働について」など）
            $table->string('title');

            // ツールの種類 ('tree', 'matrix', 'swot' のいずれかが入る)
            $table->string('type');

            // 🔥 ここが魔法のJSONカラム。複雑な図解のデータを丸ごと保存します。
            $table->json('data');

            // トピックの「分析・図解 PRO」タブに公開（投稿）されているかどうか
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
