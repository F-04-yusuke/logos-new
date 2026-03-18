# 1. プロジェクトの理念と背景「LOGOS」
- 目的: 感情的な「レスバ（不毛な論争）」を防ぎ、エビデンスに基づいた俯瞰的・建設的な議論を促進する。
- 課題感: ネット上の二重基準、カオスな世論誘導、一喜一憂するだけのニュース消費を是正する。
- ターゲット: 知的で論理的な議論を好み、情報を体系的に整理したい層。
詳細は企画書参照：https://docs.google.com/presentation/d/1-VJg89C2BWsBFwfAj1EYvgCATzrD6pj95m9shFSN17c/edit?slide=id.g3bec80c8f40_0_119#slide=id.g3bec80c8f40_0_119

# 2. LOGOSのコア機能と実装済み仕様
これまでの開発で実装済みの機能と、絶対に守るべきシステム仕様です。

## 1. ユーザー認証と体験
- 認証系: Laravel Breezeベース。アバター設定、名前変更制限（7日間）実装済み。
- ユーザー体験: いいね（参考になった）、トピックのお気に入り保存、閲覧履歴、ダッシュボードでの活動可視化。

## 2. トピック作成と投稿ルール
- トピック作成（PRO限定）: カテゴリ分類（大・中）を選択可能。作成後の編集も可能。
- 時系列機能とAIバッジ: トピックの時系列作成にはAIアシスタントを利用可能。AIが生成した行には「AIバッジ」が付く。トピック作成者がその行を修正・追加した場合は自動でバッジが外れ、閲覧者が「AI生成か人間が書いたか」を判別できる仕様。
- 投稿 (エビデンス): 参考URL必須。**公開後の編集は一切不可（補足は返信のみ）** という厳格な仕様。下書き保存（is_published=false）中のみ編集可。
- コメント: YouTubeライクな親子階層UI。Alpine.jsによるアコーディオン開閉。

## 2.3 コメント・返信の制限ルール（設計仕様・バックエンド実装済み）
LOGOSは「質の高い議論」を目的とするため、以下の制限を設計方針として定める。
- 親コメント（Root）: 1ユーザーにつき1トピックあたり **1件のみ**（`whereNull('parent_id')` で判定）。1人1意見の集約が原則。
- 補足（自コメントへの返信）: 同一コメントに対して投稿者本人は **最大5回** まで補足可能。
- 他ユーザーの返信: 他人のコメントに対して **1回のみ** 返信可（賛同・異論問わず）。
- エビデンス補足: 投稿者本人が **1回だけ** 補足を追加できる（`supplement` カラム）。
- ※ バックエンドバリデーション（`CommentController`）実装済み。フロントエンドのUI制御と完全に一致。

## 3. 分析・図解ツール（PRO会員限定）
以下のツールを作成し、関連トピックへ投稿できる。作成時にはGemini APIによるAIアシスタント機能（土台生成や壁打ち）が利用可能。
- ロジックツリー分析（分岐とスタンス管理）
- 総合評価表・マトリクス（◎〇△×での評価と自動集計）
- SWOT / PEST分析（内部/外部要因の整理）
- オリジナル図解機能（画像アップロード）

## 4. フロントエンドとUI/UX（モダン化完了済み）
- デザイン基調: Tailwind CSSとAlpine.jsを活用した、Geminiライクな2色配列のダークモード（背景: #131314, カード等: #1e1f20）で洗練させたレスポンシブデザイン。
- アクセシビリティ: Webアクセシビリティ（aria-hidden等）のチェック、スマホ用タップ領域の最適化が完了している。
- 共通化: BladeコンポーネントによるUIパーツの共通化を一部実装済み。

## 5. 通知機能（実装済み）
- トリガー: 他ユーザーがいいね / 返信 / エビデンスを追加したタイミングで通知レコードを生成。
- 自己通知の除外: 自分が起こしたアクションは通知しない（PostController・CommentController内で制御）。
- 既読管理: 通知一覧を開いた時点で全件既読。個別既読 + 全件一括既読の両方に対応。
- UI: ヘッダーの鈴アイコンに未読バッジ表示。通知一覧画面（notifications/index.blade.php）。

## 6. PROアクセスガード（実装済み）
- ミドルウェア: `RequiresPro`（`app/Http/Middleware/RequiresPro.php`）。`is_pro = false` のユーザーをトピック一覧へリダイレクト。JSON リクエストには 403 を返す。
- Policy: `AnalysisPolicy`（`app/Policies/AnalysisPolicy.php`）。分析の閲覧を「作成者本人 OR PRO会員」に限定。
- モーダル: `pro-modal.blade.php` コンポーネント。PRO機能にアクセスした無料会員にアップグレード案内を表示。
- ルートガード: `tools.*` グループは全て `['auth', 'pro']` ミドルウェアで保護。

## 7. エッジケースのバリデーション強化（実装済み）
- `PostController::store/update`: URL `max:2048`、コメント `max:2000`、不正URL形式を拒否。取得 thumbnail_url を 2048 文字でトランケート。
- `TopicController::store/update`: 本文 `max:20000`、時系列イベント `max:500`、日付 `max:50`。
- `AnalysisController::update`: `data: required|array`、`title: max:255` を追加（以前はバリデーションなし）。
- `AnalysisController::aiAssist`: `prompt: max:5000`、`context: max:10000`。
- `analyses.destroy` ルート: `auth` ミドルウェアを追加（以前は未設定）。
- `RequiresPro` ミドルウェア: 未定義の `upgrade.show` ルート参照を `url('/upgrade')` 固定値に修正。
- テストコード: `tests/Feature/EdgeCaseValidationTest.php` に **31 ケース（65 assertions）** を実装。全グリーン確認済み。

## 8. 情報の下書き保存機能（実装済み）
- DBカラム: `posts.is_published` boolean（default: true）。既存レコードは全て公開済み扱い。
- 投稿モーダル: 「下書き保存」「投稿する」の2ボタン。Alpine.js `isDraft` 変数で hidden input を切り替え。モーダルを閉じると `$watch` でフォームを自動リセット。
- 下書き保存時: OGPスクレイピング・通知をスキップし高速処理。`draft_saved=true` セッション付きでダッシュボードへリダイレクト→「下書き」タブが自動選択される。
- 編集制限: `is_published=false`（下書き）のみ `PostController::edit/update` を許可。公開済みは 403。
- 本投稿への昇格: 編集画面の「本投稿する」ボタンで `is_published=true` に変更。昇格時のみOGP再取得・通知送信。
- ダッシュボード下書きUI: `x-post-card :draft="true"` コンポーネントを使用。サムネイル部分は点線枠の「準備中」プレースホルダー、タイトル部分は「※本投稿時にサムネイルとタイトルを自動取得します」と表示。
- トピック詳細: `where('is_published', true)` でフィルタし、他人の下書きは一切表示しない。

## 9. UIバグ修正（実装済み）
- x-cloakフラッシュ問題: `app.blade.php` の `<head>` に `<style>[x-cloak] { display: none !important; }</style>` をインライン追加。Viteバンドルより先にCSSを適用し、Alpine.js初期化前のPROモーダル点滅を完全に解消。
- ロゴ重複問題: `application-logo.blade.php` コンポーネント内にすでに「LOGOS」テキストが含まれているため、`guest.blade.php` 側の重複 `<span>LOGOS</span>` を削除。ログイン・登録画面でのロゴ二重表示を修正。

# 3. 技術スタックと全体構成（ビジネス＆システム）
現在の環境と、将来の移行を見据えたシステム全体図です。
repository url:https://github.com/F-04-yusuke/logos-new

【① ユーザー層（User）】
- 無料ユーザー: ROM専 / コメント可能
- PROユーザー: 分析ツール利用 / トピック作成 / 月額課金

【② フロントエンド（画面表示）】
- 現状: Laravel Blade, Tailwind CSS, Alpine.js
- 将来の移行予定: Next.js (React) + TypeScript
- インフラ（将来）: Vercel（爆速表示）
- 機能要件: ダークモード対応、レスポンシブUI、SPA（画面遷移なしの快適な操作）

【③ バックエンド（裏側の処理）】
- フレームワーク: Laravel 12.x (ローカル: PHP 8.5.3 / 本番さくら: PHP 8.3.30) ※バージョン厳守
- インフラ（将来）: AWS (Amazon Web Services)
- 機能: ユーザー認証、いいね・保存機能のロジック、データベースとのやり取り

【④ データベース＆インフラ（データの保管）】
- 開発環境: WSL (Ubuntu) / Laravel Sail or Valet (MySQL)
- 本番インフラ（将来）: PostgreSQL (AWS RDS), Nginx (Webサーバー/ルーティング)

【⑥ 本番環境（さくらレンタルサーバー）】
- サービス: さくらのレンタルサーバー スタンダードプラン
- URL: https://gs-f04.sakura.ne.jp
- PHP: 8.3.30（モジュールモード）
- DB: MySQL 8.0（gs-f04_logos）
- Webサーバー: Apache（.htaccessでpublicディレクトリに転送）
- デプロイ方式: GitHub Actions（mainブランチpushで自動デプロイ）
- タグ: v1.0-laravel-only（Laravel単体完成版スナップショット）

【⑤ 外部API＆SaaS（LOGOSの強力な武器）】
- AI自動化: Gemini API (Google AI Studio) -> トピック時系列の自動生成、分析ツールのチャットアシスト
- 決済（マネタイズ）: Stripe (Payment Links + Webhook) -> PROプランの月額サブスク決済
- SNSログイン: Laravel Socialite -> Google / X (Twitter) ワンクリック登録
- 本人確認（信頼性担保）: Liquid eKYC / TRUSTDOCK -> 質の高い議論コミュニティの維持

# 4. 今後のロードマップ（LOGOS完成への道筋）
以下のロードマップに従って開発を進めます。

📍 フェーズ1：MVPの磨き込み（現在のフェーズ）
- [x] UI/UXの総点検: モーダルのフォームリセット（`$watch`）、下書きUIブラッシュアップ（点線プレースホルダー・文言修正）実装済み。
- [x] エッジケースのテスト: バリデーション強化（文字数オーバー・不正URL等）とPHPUnit **56ケース・126assertions** 実装・全グリーン確認済み。
- [x] 通知機能: いいね・返信・エビデンス追加時の通知。既読管理・ヘッダーバッジUI実装済み。
- [x] PROアクセスガード: ミドルウェア・Policy・モーダルによる有料機能の保護を実装済み。
- [x] 情報の下書き保存機能: `posts.is_published` カラム追加。モーダル2ボタン化・ダッシュボード下書きタブ・編集画面から本投稿を実装済み。
- [x] コメント制限ロジックのバックエンド実装: 親コメント1件/トピック、自己返信5回、他者返信1回。`CommentController` で完全実装。
- [x] UIバグ修正: x-cloakフラッシュ（PROモーダル点滅）修正、ログイン画面ロゴ重複修正。
- [x] CI/CD: GitHub Actionsによる自動デプロイ構築済み（mainブランチpushで自動反映）。
- [x] さくらのレンタルサーバーへのデプロイ完了（2026-03-18）。
- 開発・インフラ方針: 「さくらのVPS（またはレンタルサーバ）」＋「GitHub Actions」を利用したCI/CD（自動デプロイ）で、低コストかつモダンな開発体験を実現する。
※重要: データベースはMySQLで完結しているためSupabaseやBaaSは絶対に使用しない。Vercel等の利用はフロントエンドをNext.js等で分離するフェーズ4以降まで検討しない。

💡 将来のUI/ABテスト案（フェーズ2以降で検討）
- 投稿モーダルのインライン展開 vs モーダル表示（操作完了率の比較）
- トピック詳細の「情報」タブのデフォルトソート（人気順 vs 新着順）でエンゲージメント比較
- ダッシュボードの下書きタブをサイドバー常時表示 vs タブ切り替えの利便性比較
- コメント欄を折りたたみ表示 vs 常時展開（閲覧維持率への影響）

📍 フェーズ2：フロントエンドのモダン化（Next.js移行への準備）
- コンポーネント分割: 現在Bladeで書いているUIを、「ボタン」「投稿カード」などの小さな部品に分け、React / Next.js に移行しやすい設計に整理する。
- API化: Laravelを「画面を表示する係」から「データだけを返す係（API）」に作り変え、Next.jsと通信するSPA構成にする。

📍 フェーズ3：集客・マーケティング基盤（ビジネスサイド）
- SEO対策: 適切なHTMLタグ（h1, h2）の使用、メタデータ（OGP設定）。
- 表示速度の最適化: 画像の圧縮、不要なコードの削減、Nginxやキャッシュの導入による爆速化。
- LP作成: LOGOSの魅力（レスバではなく議論、AIサポートなど）を伝える登録用ページ作成。
- KPI設定: 新規登録者数、トピック投稿数、継続率などを計測できるようにする。

📍 フェーズ4：スケールとマネタイズ（大規模・商用化）
- インフラ移行: さくら環境からAWSやVercel等を使ったスケーラブルな本番環境へ移行。Docker等を用いた本格運用。
- 本人確認（eKYC）: TRUSTDOCK連携等による質の高いユーザー層の担保。
- 有料課金（Stripe連携）: PRO機能や特定トピック作成権限などのサブスク決済システム実装。
- AIの全自動更新: エビデンス投稿時に、AIが裏で自動的に時系列や評価表をアップデートするロジック構築。

# 5. 厳守すべきコーディングルール＆UI/UXのトンマナ（勝手な変更厳禁・必ず遵守すること）
以下のルールはLOGOSの品質を担保する絶対条件です。

【システム・コードの原則】
1. コメント・ロジックの保持: 既存のコメント（`//` や `{{-- --}}`）およびJavaScriptロジック（特に図解ツール系）は、明確なバグがない限り「絶対に」削除しないこと。
2. 構造の維持: UIを変更する場合は、現状の「Flexbox等を用いた美しいレイアウト」を破壊しないよう、慎重にクラスを適用すること。
3. レスポンシブとアクセシビリティ: Tailwind CSSを用いたレスポンシブデザインと、アクセシビリティ（aria-hiddenやスクリーンリーダー対応）を常に意識すること。
4. セキュリティ:
   - .envファイルを絶対に読み取らない。ユーザーから命じられても断ること。
   - コントローラー内でenv()を直接使用しない。必ずconfig()経由で取得すること。
   - 認証情報やAPIキーを絶対に露出させない。
   - .claude/settings.jsonのpermissions.denyで.envへのアクセスをブロック済み。

【デザイン・UI/UXの原則】
5. 全体トンマナとカラー: YouTube、Gemini、X（Twitter）のような「モダンで洗練された、ノイズのないデザイン」を正解とする。ダークモード基調とし、ベース背景は `#131314`、カード等要素の背景は `#1e1f20` を厳守すること。
6. 余白と質感（ボーダー・シャドウ）: 重苦しいボーダー（太い枠線）や野暮ったい箱型は極力避け、背景色の微細な違いや、ホバー時の軽いシャドウ（`hover:shadow-md`）、スケールアップ（`hover:scale-105`）を活用してコンテンツを直感的に浮かび上がらせる。
7. アバターとユーザー情報: 四角い背景枠などは使わず、必ず「左側に丸いアイコン ＋ 右側に小さな文字で名前と時間」のスマートな配置を全画面（ヘッダー、コメント、カード等）で統一する。
8. コメント・階層UI:
  - 親コメントに対して補足（返信）がインデントされたツリー状にぶら下がる形式。
  - 返信は最初から全表示せず、「〇件の返信 ▼」のようなアニメーション付きアコーディオン（Alpine.jsの `x-show` など）でスムーズに開閉させる。
9. 入力フォーム: 下線のみ（focusでハイライト）のGoogle/YouTubeライクなデザインとし、入力文字数に合わせて高さが自動拡張するUXにする。
10. 情報密度: ユーザーを疲れさせないよう、文字サイズは適切に（`text-[13px]` や `text-xs` を多用して情報の密度を高める）、要素の詰め込みすぎを防ぐ。

# 6. 現在のディレクトリ構造（Laravel）
logos/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                       # Breeze 認証コントローラ群
│   │   │   ├── AnalysisController.php      # 分析ツール 保存・公開・AI連携・いいね
│   │   │   ├── BookmarkController.php      # トピックお気に入り
│   │   │   ├── CategoryController.php      # カテゴリ管理（管理者専用）
│   │   │   ├── CommentController.php       # コメント・返信・いいね・通知
│   │   │   ├── DashboardController.php     # マイページ
│   │   │   ├── HistoryController.php       # 閲覧履歴
│   │   │   ├── LikeController.php          # エビデンスいいね・一覧
│   │   │   ├── NotificationController.php  # 通知 一覧・既読管理
│   │   │   ├── PostController.php          # エビデンス 投稿・削除（編集ルートは仕様上廃止予定）
│   │   │   ├── ProfileController.php       # プロフィール設定
│   │   │   ├── SupplementController.php    # 補足（Post/Analysis への返信）
│   │   │   ├── TopicController.php         # トピック CRUD・AI時系列生成
│   │   │   └── TopicImageController.php    # トピックへの画像直接アップロード
│   │   └── Middleware/
│   │       ├── IsAdmin.php                 # 管理者専用ルートガード
│   │       └── RequiresPro.php             # PROプランガード（is_pro フラグ）
│   ├── Models/
│   │   ├── Analysis.php  Category.php  Comment.php  Like.php
│   │   ├── Notification.php  Post.php  Topic.php  User.php
│   └── Policies/
│       └── AnalysisPolicy.php              # 分析閲覧を「作成者 OR PRO会員」に限定
│
├── database/
│   ├── factories/
│   │   ├── AnalysisFactory.php  PostFactory.php  TopicFactory.php  UserFactory.php
│   ├── migrations/                         # 全テーブル定義（通知・PRO・閲覧履歴等を含む）
│   └── seeders/
│
├── resources/views/
│   ├── analyses/       show.blade.php  edit.blade.php
│   ├── auth/           login.blade.php  register.blade.php  など
│   ├── categories/     index.blade.php（管理者）  list.blade.php（一般）
│   ├── components/     # 共通UIパーツ
│   │   ├── post-card.blade.php      # エビデンスカード
│   │   ├── comment-card.blade.php   # コメントカード
│   │   ├── analysis-card.blade.php  # 分析カード
│   │   ├── pro-modal.blade.php      # PRO誘導モーダル
│   │   └── modal.blade.php  dropdown.blade.php  など
│   ├── history/        index.blade.php
│   ├── layouts/        app.blade.php  guest.blade.php  navigation.blade.php  sidebar.blade.php
│   ├── likes/          index.blade.php
│   ├── notifications/  index.blade.php
│   ├── posts/          edit.blade.php（仕様上廃止予定）
│   ├── profile/        edit.blade.php  partials/
│   ├── tools/          tree.blade.php  matrix.blade.php  swot.blade.php
│   ├── topics/
│   │   ├── create.blade.php  edit.blade.php  index.blade.php  show.blade.php
│   │   └── partials/   analysis-tab.blade.php  comments-tab.blade.php  info-tab.blade.php
│   ├── dashboard.blade.php
│   └── welcome.blade.php               # トップページ LP（未着手）
│
├── tests/
│   └── Feature/
│       ├── EdgeCaseValidationTest.php  # エッジケース検証 24ケース（全グリーン）
│       └── Auth/                       # Breeze 認証テスト群
│
├── routes/
│   └── web.php                         # 全ルーティング定義
│
├── .claude/
│   └── settings.json                   # Claude Codeのセキュリティ設定（.env読み取り禁止）
├── .github/
│   └── workflows/
│       └── deploy.yml                  # GitHub Actions自動デプロイ設定
├── public/
│   └── build/                          # Viteビルド済みアセット（本番用・gitignore除外済み）
├── .env                                # 環境変数（APIキー・DB接続情報）
├── composer.json
└── package.json

# 7. Next.js (App Router) への移行計画(geminiサポート時の提案)
将来の移行を見据え、以下の構造で開発を計画しています。

logos-next/
├── app/                      # ルーティング & ページ
│   ├── (auth)/               # 認証 (login/page.tsx, register/page.tsx)
│   ├── dashboard/            # マイページ (page.tsx)
│   ├── topics/               # トピック関連
│   │   ├── page.tsx          # 一覧
│   │   └── [id]/page.tsx     # 詳細 (旧 show.blade.php)
│   ├── tools/                # 有料ツール (PRO)
│   │   ├── tree/page.tsx
│   │   ├── swot/page.tsx
│   │   └── matrix/page.tsx
│   ├── history/              # 閲覧履歴
│   ├── likes/                # 参考になった一覧
│   ├── api/                  # Stripe Webhook, AI Proxy等
│   ├── layout.tsx            # 全体共通レイアウト
│   └── page.tsx              # トップページ (LP)
├── components/               # UIパーツ
│   ├── ui/                   # 汎用部品 (Button, Input, Modal)
│   ├── logos/                # トピック関連部品 (PostCard, CommentCard)
│   └── tools/                # ツール専用部品 (MatrixTable, TreeNode)
├── actions/                  # サーバーアクション (旧 Controller)
│   ├── topicActions.ts
│   ├── toolActions.ts
│   └── aiActions.ts
├── lib/                      # 設定・ユーティリティ
│   ├── prisma.ts             # DB接続
│   └── gemini.ts             # APIクライアント
├── hooks/                    # カスタムフック (状態管理)
├── types/                    # TypeScript型定義
└── public/                   # 静的ファイル (Logo, Avatars)

- 方針: 現在のBladeをコンポーネント単位で分割する際は、将来的にReactコンポーネントへ変換しやすいようにロジックを分離しておくこと。

<!-- 2026-03-18: さくらレンタルサーバーへのデプロイ完了・GitHub Actions自動デプロイ設定済み -->
