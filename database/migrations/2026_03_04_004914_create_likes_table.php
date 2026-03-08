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
        Schema::create('likes', function (Blueprint $table) {
            $table->id(); // いいね自体の管理番号（自動で1, 2, 3...と振られます）
            
            // 🔽 誰が「参考になった」を押したか（ユーザーID）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // 🔽 どの投稿に対して押したか（投稿ID）
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            
            $table->timestamps(); // 押された日時

            // 🌟 超重要：1人のユーザーが、同じ投稿に何度も連続で押せないようにするバリア
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};