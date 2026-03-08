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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // カテゴリの名前（「政治」「外交」など）
            $table->string('name');
            
            // 🔽 ここが階層構造の魔法です 🔽
            // parent_id（親のID）を保存します。
            // カラッポ(null)の場合は「自分が一番上の大分類である」と判定し、
            // 数字が入っていれば「その数字のIDを持つ大分類の『子（中分類）』である」と判定します。
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
