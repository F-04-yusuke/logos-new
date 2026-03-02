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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // 誰が投稿したか（usersテーブルのIDと紐付け）。ユーザーが退会したらこの投稿も一緒に消す（cascadeOnDelete）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // どのトピックへの投稿か（topicsテーブルのIDと紐付け）。トピックが消えたらこの投稿も一緒に消す
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();

            // YouTubeや記事などのURLを保存する箱（stringは255文字までの文字列）
            $table->string('url');

            // ユーザーが選択した「分類」を保存する箱
            $table->string('category');

            // ユーザーのコメント。文章が長くなるためstringより大きな text を使用。未入力でもOKにするため nullable() を付与
            $table->text('comment')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
