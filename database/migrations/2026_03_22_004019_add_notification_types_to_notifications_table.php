<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM に comment_like / analysis_like / topic_bookmark を追加
        \DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('new_post','comment_reply','post_like','system','comment_like','analysis_like','topic_bookmark') NOT NULL");

        // notifiable_type の長さを拡張（App\Models\Analysis = 19文字 で varchar(20) が逼迫するため）
        \DB::statement("ALTER TABLE notifications MODIFY COLUMN notifiable_type VARCHAR(50) NULL");
    }

    public function down(): void
    {
        // 元の定義に戻す（新型の通知レコードが存在する場合はエラーになるため注意）
        \DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('new_post','comment_reply','post_like','system') NOT NULL");
        \DB::statement("ALTER TABLE notifications MODIFY COLUMN notifiable_type VARCHAR(20) NULL");
    }
};
