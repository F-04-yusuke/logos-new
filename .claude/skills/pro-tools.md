# PRO機能・分析ツール・通知 詳細仕様

## 1. 分析・図解ツール（PRO会員限定）
以下のツールを作成し、関連トピックへ投稿できる。作成時にはGemini APIによるAIアシスタント機能（土台生成や壁打ち）が利用可能。
- ロジックツリー分析（分岐とスタンス管理）
- 総合評価表・マトリクス（◎〇△×での評価と自動集計）
- SWOT / PEST分析（内部/外部要因の整理）
- オリジナル図解機能（画像アップロード）

## 2. PROアクセスガード（実装済み）
- ミドルウェア: `RequiresPro`（`app/Http/Middleware/RequiresPro.php`）。`is_pro = false` のユーザーをトピック一覧へリダイレクト。JSON リクエストには 403 を返す。
- Policy: `AnalysisPolicy`（`app/Policies/AnalysisPolicy.php`）。分析の閲覧を「作成者本人 OR PRO会員」に限定。
- モーダル: `pro-modal.blade.php` コンポーネント。PRO機能にアクセスした無料会員にアップグレード案内を表示。
- ルートガード: `tools.*` グループは全て `['auth', 'pro']` ミドルウェアで保護。

## 3. 通知機能（実装済み）
- トリガー: 他ユーザーがいいね / 返信 / エビデンスを追加したタイミングで通知レコードを生成。
- 自己通知の除外: 自分が起こしたアクションは通知しない（PostController・CommentController内で制御）。
- 既読管理: 通知一覧を開いた時点で全件既読。個別既読 + 全件一括既読の両方に対応。
- UI: ヘッダーの鈴アイコンに未読バッジ表示。通知一覧画面（notifications/index.blade.php）。
- `/api/user/me` のレスポンスに `unread_notifications_count` を含む（Next.js側で使用）。

## 4. 決済方針（フェーズ3以降）
- Stripe Payment Linksを使う（コード実装なし）
- フェーズ3でWebhookの受け口のみ実装
- 決済コードの作り込みはしない

## 5. 外部API・SaaS連携
- Gemini API (Google AI Studio): トピック時系列の自動生成、分析ツールのチャットアシスト
- Stripe (Payment Links + Webhook): PROプランの月額サブスク決済（フェーズ3以降）
- Laravel Socialite: Google / X (Twitter) ワンクリック登録（SNSログイン）
- Liquid eKYC / TRUSTDOCK: 本人確認・質の高い議論コミュニティの維持（フェーズ4）

## 6. 将来のUI/ABテスト案（フェーズ2以降で検討）
- 投稿モーダルのインライン展開 vs モーダル表示（操作完了率の比較）
- トピック詳細の「情報」タブのデフォルトソート（人気順 vs 新着順）でエンゲージメント比較
- ダッシュボードの下書きタブをサイドバー常時表示 vs タブ切り替えの利便性比較
- コメント欄を折りたたみ表示 vs 常時展開（閲覧維持率への影響）
