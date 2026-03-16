<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // 通知の受信者（ユーザーが削除されたら通知も消す）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 通知を発生させたユーザー（system通知・ユーザー削除時はnull）
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();

            // 通知の種別
            // new_post      : 自分のトピックに新しいエビデンスが追加された
            // comment_reply : 自分のコメントに返信がついた
            // post_like     : 自分の投稿に「参考になった」がついた
            // system        : 管理者からのお知らせ
            $table->enum('type', ['new_post', 'comment_reply', 'post_like', 'system']);

            // 関連モデルを短縮名で保持（Next.js移行後もAPIで型安全に扱える）
            // 'topic' | 'comment' | 'post' | null（system通知）
            $table->string('notifiable_type', 20)->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();

            // 管理者からのシステム通知の本文（他のtypeはnull）
            $table->text('message')->nullable();

            // 既読管理: null = 未読 / タイムスタンプ = 既読済み
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // 未読バッジカウント: WHERE user_id = ? AND read_at IS NULL
            $table->index(['user_id', 'read_at']);

            // 通知一覧: WHERE user_id = ? ORDER BY created_at DESC
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
