# LOGOS Laravel版 仕様書（logos-new）
最終更新: 2026-03-21

---

# 0. 最重要ルール

## このリポジトリの役割
- **さくら本番稼働中。絶対に壊さない。**
- Claude Codeは原則読み取り専用。**以下2ファイルのみ編集可**:
  - `app/Http/Controllers/Api/TopicApiController.php`
  - `routes/api.php`
- 上記2ファイル編集前も必ずバックアップコミットを作成する
- `migrate:fresh`・`db:wipe`・sqlite切り替えは本番環境で絶対に実行しない
- サーバー上でファイルを直接編集しない（GitHub Actions経由でデプロイ）
- 一度に編集するファイルは **5ファイル以内**

## セキュリティ絶対禁止
- `.env` ファイルを絶対に読み取らない（settings.jsonでブロック済み）
- コントローラー内で `env()` を直接使用しない → 必ず `config()` 経由
- Gemini APIキーに絶対に `NEXT_PUBLIC_` をつけない → ブラウザ公開になる
- 認証情報・APIキーを絶対に露出させない

---

# 1. プロジェクト概要

LOGOSは感情的な論争を防ぎ、エビデンスに基づいた俯瞰的・建設的な議論を促進するプラットフォーム。
- バックエンド: Laravel 12.x + Sanctum + MySQL（さくらレンタルサーバー）
- フロントエンド: Next.js 16.2.0（~/logos-next → Vercel）
- 本番URL: https://gs-f04.sakura.ne.jp
- GitHubリポジトリ: https://github.com/F-04-yusuke/logos-new
- adminユーザー: admin@test.com（is_pro・is_admin設定済み）

---

# 2. 実装済み機能（概要）

- ユーザー認証（Laravel Breeze）・アバター設定・名前変更制限（7日間）
- トピック作成（PRO限定）・カテゴリ分類（大・中）・時系列+AIバッジ
- 投稿（エビデンス）: 参考URL必須・公開後編集不可・下書き保存可
- コメント: 親子階層・1ユーザー1ルートコメント制限・補足5回制限
- 分析ツール（PRO限定）: ロジックツリー・評価表・SWOT/PEST・オリジナル図解
- 通知: いいね/返信/エビデンス追加時・未読バッジ・既読管理
- PROアクセスガード（RequiresPro ミドルウェア）
- Gemini AIアシスタント連携（時系列作成・図解作成）
- 情報の下書き保存機能（posts.is_published）
- PHPUnit: 56ケース・126assertions 全グリーン確認済み

---

# 3. 開発体制（2026-03-21更新）

**Claude Codeがリードエンジニアとしてメインで動く。**

| 役割 | 担当 |
|---|---|
| コード読み取り・API追加（上記2ファイルのみ）・技術検証 | Claude Code |
| ブラウザで判断が必要な視覚的レビュー（スクショ・UIデザイン確認） | AIチャット（claude.ai） |
| ブラウザ確認・スクショ撮影 | ユーザー |

- Next.js実装の参照のため読み取り自由（catコマンドで閲覧）
- 大きな方針転換があった場合は必ずCLAUDE.mdに追記してから実装する
- 大規模改修後は必ず全ページ動作確認を実施

---

# 4. スキルファイル（詳細参照先）

詳細仕様は必要に応じて以下を読むこと:

| ファイル | 内容 |
|---|---|
| `.claude/skills/features.md` | 認証・トピック・投稿・コメント制限・バリデーション・下書き機能の詳細 |
| `.claude/skills/pro-tools.md` | PRO機能・分析ツール・通知・Stripe方針・外部API連携 |
| `.claude/skills/security.md` | セキュリティルール・コーディング原則・UI/UXトンマナ（10ルール） |
| `.claude/skills/infra.md` | さくらサーバー・SSH接続情報・デプロイフロー・ローカル開発環境 |
| `.claude/skills/directory-map.md` | ディレクトリ構成・Bladeファイル一覧・Next.js参照表 |
| `.claude/skills/roadmap.md` | フェーズ計画・進捗・Gitタグ履歴・技術スタック全体図 |
