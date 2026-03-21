# Laravelディレクトリ構成

```
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
│   │   │   ├── TopicImageController.php    # トピックへの画像直接アップロード
│   │   │   └── Api/
│   │   │       └── TopicApiController.php  # ★ Next.js向けAPI（編集可能）
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
│       ├── EdgeCaseValidationTest.php  # エッジケース検証 31ケース・65assertions（全グリーン）
│       └── Auth/                       # Breeze 認証テスト群
│                                       # 全体: 56ケース・126assertions（全グリーン）
│
├── routes/
│   └── web.php                         # 全ルーティング定義
│   └── api.php                         # ★ APIルーティング（編集可能）
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
```

---

## Next.js側のBladeファイル参照表

| Next.jsページ | 参照するBladeファイル |
|---|---|
| app/page.tsx | resources/views/topics/index.blade.php |
| app/topics/[id]/page.tsx | resources/views/topics/show.blade.php |
| app/topics/create/page.tsx | resources/views/topics/create.blade.php |
| app/login/page.tsx | resources/views/auth/login.blade.php |
| app/register/page.tsx | resources/views/auth/register.blade.php |
| app/notifications/page.tsx | resources/views/notifications/index.blade.php |
| app/dashboard/page.tsx | resources/views/dashboard.blade.php |
| app/categories/page.tsx | resources/views/categories/list.blade.php |
| app/profile/page.tsx | resources/views/profile/edit.blade.php |
| app/history/page.tsx | resources/views/history/index.blade.php |
| app/likes/page.tsx | resources/views/likes/index.blade.php |
| app/tools/tree/page.tsx | resources/views/tools/tree.blade.php |
| app/tools/matrix/page.tsx | resources/views/tools/matrix.blade.php |
| app/tools/swot/page.tsx | resources/views/tools/swot.blade.php |
| components/Header.tsx | resources/views/layouts/navigation.blade.php |
| components/Sidebar.tsx | resources/views/layouts/sidebar.blade.php |
