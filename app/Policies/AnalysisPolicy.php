<?php

namespace App\Policies;

use App\Models\Analysis;
use App\Models\User;

class AnalysisPolicy
{
    /**
     * 図解の詳細ページへのアクセス権を判定する。
     *
     * 許可条件:
     *   1. 作成者本人（自分の下書きも含めて常に閲覧可）
     *   2. PRO会員（公開済みの図解を閲覧可）
     *
     * 無料会員は analysis-card のプレビューのみ閲覧でき、
     * 詳細ページへの遷移はPROアップグレード案内モーダルでブロックする。
     */
    public function view(User $user, Analysis $analysis): bool
    {
        // 作成者本人は常に閲覧可
        if ($user->id === $analysis->user_id) {
            return true;
        }

        // PRO会員は公開済みなら閲覧可
        return $user->is_pro && $analysis->is_published;
    }
}
