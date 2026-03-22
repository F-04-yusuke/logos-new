# LOGOSコア機能 詳細仕様

## 1. ユーザー認証と体験
- 認証系: Laravel Breezeベース。アバター設定、名前変更制限（7日間）実装済み。
- ユーザー体験: いいね（参考になった）、トピックのお気に入り保存、閲覧履歴、ダッシュボードでの活動可視化。

## 2. トピック作成と投稿ルール
- トピック作成（PRO限定）: カテゴリ分類（大・中）を選択可能。作成後の編集も可能。
- 時系列機能とAIバッジ: トピックの時系列作成にはAIアシスタントを利用可能。AIが生成した行には「AIバッジ」が付く。トピック作成者がその行を修正・追加した場合は自動でバッジが外れ、閲覧者が「AI生成か人間が書いたか」を判別できる仕様。
- 投稿（エビデンス）: 参考URL必須。**公開後の編集は一切不可（補足は返信のみ）** という厳格な仕様。下書き保存（is_published=false）中のみ編集可。
- コメント: YouTubeライクな親子階層UI。Alpine.jsによるアコーディオン開閉。

## 3. コメント・返信の制限ルール（設計仕様・バックエンド実装済み）
LOGOSは「質の高い議論」を目的とするため、以下の制限を設計方針として定める。
- 親コメント（Root）: 1ユーザーにつき1トピックあたり **1件のみ**（`whereNull('parent_id')` で判定）。1人1意見の集約が原則。
- 補足（自コメントへの返信）: 同一コメントに対して投稿者本人は **最大5回** まで補足可能。
- 他ユーザーの返信: 他人のコメントに対して **1回のみ** 返信可（賛同・異論問わず）。
- エビデンス補足: 投稿者本人が **1回だけ** 補足を追加できる（`supplement` カラム）。
- ※ バックエンドバリデーション（`CommentController`）実装済み。フロントエンドのUI制御と完全に一致させること。

## 4. エッジケースのバリデーション強化（実装済み）
- `PostController::store/update`: URL `max:2048`、コメント `max:2000`、不正URL形式を拒否。取得 thumbnail_url を 2048 文字でトランケート。
- `TopicController::store/update`: 本文 `max:20000`、時系列イベント `max:500`、日付 `max:50`。
- `AnalysisController::update`: `data: required|array`、`title: max:255` を追加（以前はバリデーションなし）。
- `AnalysisController::aiAssist`: `prompt: max:5000`、`context: max:10000`。
- `analyses.destroy` ルート: `auth` ミドルウェアを追加（以前は未設定）。
- `RequiresPro` ミドルウェア: 未定義の `upgrade.show` ルート参照を `url('/upgrade')` 固定値に修正。
- テストコード: `tests/Feature/EdgeCaseValidationTest.php` に **31ケース（65 assertions）** を実装。全グリーン確認済み。
  - 全体テスト: `tests/Feature/` に **56ケース・126assertions** 実装・全グリーン確認済み。

## 5. 情報の下書き保存機能（実装済み）
- DBカラム: `posts.is_published` boolean（default: true）。既存レコードは全て公開済み扱い。
- 投稿モーダル: 「下書き保存」「投稿する」の2ボタン。Alpine.js `isDraft` 変数で hidden input を切り替え。モーダルを閉じると `$watch` でフォームを自動リセット。
- 下書き保存時: OGPスクレイピング・通知をスキップし高速処理。`draft_saved=true` セッション付きでダッシュボードへリダイレクト→「下書き」タブが自動選択される。
- 編集制限: `is_published=false`（下書き）のみ `PostController::edit/update` を許可。公開済みは 403。
- 本投稿への昇格: 編集画面の「本投稿する」ボタンで `is_published=true` に変更。昇格時のみOGP再取得・通知送信。
- ダッシュボード下書きUI: `x-post-card :draft="true"` コンポーネントを使用。サムネイル部分は点線枠の「準備中」プレースホルダー、タイトル部分は「※本投稿時にサムネイルとタイトルを自動取得します」と表示。
- トピック詳細: `where('is_published', true)` でフィルタし、他人の下書きは一切表示しない。

## 6. UIバグ修正（実装済み）
- x-cloakフラッシュ問題: `app.blade.php` の `<head>` に `<style>[x-cloak] { display: none !important; }</style>` をインライン追加。Viteバンドルより先にCSSを適用し、Alpine.js初期化前のPROモーダル点滅を完全に解消。
- ロゴ重複問題: `application-logo.blade.php` コンポーネント内にすでに「LOGOS」テキストが含まれているため、`guest.blade.php` 側の重複 `<span>LOGOS</span>` を削除。ログイン・登録画面でのロゴ二重表示を修正。

## 7. コントローラー一覧
- `Auth/` — Breeze 認証コントローラ群
- `AnalysisController.php` — 分析ツール 保存・公開・AI連携・いいね
- `BookmarkController.php` — トピックお気に入り
- `CategoryController.php` — カテゴリ管理（管理者専用）
- `CommentController.php` — コメント・返信・いいね・通知
- `DashboardController.php` — マイページ
- `HistoryController.php` — 閲覧履歴
- `LikeController.php` — エビデンスいいね・一覧
- `NotificationController.php` — 通知 一覧・既読管理
- `PostController.php` — エビデンス 投稿・削除・下書き編集（edit/update）。Next.js向けには `routes/api.php` に `PATCH /api/posts/{post}` を追加済み（下書きのみ・本投稿昇格時OGP取得・通知送信）
- `ProfileController.php` — プロフィール設定
- `SupplementController.php` — 補足（Post/Analysis への返信）
- `TopicController.php` — トピック CRUD・AI時系列生成
- `TopicImageController.php` — トピックへの画像直接アップロード
- `Api/TopicApiController.php` — Next.js向けAPI（**編集可能な2ファイルの1つ**）
