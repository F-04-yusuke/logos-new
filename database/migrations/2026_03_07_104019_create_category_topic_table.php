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
        Schema::create('category_topic', function (Blueprint $table) {
            $table->id();
            
            // 🔽 どのトピックと、どのカテゴリを紐づけるかを記録する2つの柱
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            
            // 🔽 同じトピックに、全く同じカテゴリが2回以上登録されないようにするルール
            $table->unique(['topic_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_topic');
    }
};
