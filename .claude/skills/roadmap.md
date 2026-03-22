# フェーズ計画・進捗・障害記録

## プロジェクト理念
- 目的: 感情的な「レスバ（不毛な論争）」を防ぎ、エビデンスに基づいた俯瞰的・建設的な議論を促進する。
- 課題感: ネット上の二重基準、カオスな世論誘導、一喜一憂するだけのニュース消費を是正する。
- ターゲット: 知的で論理的な議論を好み、情報を体系的に整理したい層。
- 企画書: https://docs.google.com/presentation/d/1-VJg89C2BWsBFwfAj1EYvgCATzrD6pj95m9shFSN17c/edit

---

## ロードマップ

### フェーズ1：MVPの磨き込み（完了）
- [x] UI/UXの総点検: モーダルのフォームリセット（`$watch`）、下書きUIブラッシュアップ
- [x] エッジケースのテスト: バリデーション強化（文字数オーバー・不正URL等）PHPUnit 56ケース・126assertions 全グリーン
- [x] 通知機能: いいね・返信・エビデンス追加時の通知。既読管理・ヘッダーバッジUI
- [x] PROアクセスガード: ミドルウェア・Policy・モーダルによる有料機能の保護
- [x] 情報の下書き保存機能: `posts.is_published` カラム追加。モーダル2ボタン化・ダッシュボード下書きタブ・編集画面から本投稿
- [x] コメント制限ロジックのバックエンド実装: 親コメント1件/トピック、自己返信5回、他者返信1回
- [x] UIバグ修正: x-cloakフラッシュ（PROモーダル点滅）修正、ログイン画面ロゴ重複修正
- [x] CI/CD: GitHub Actionsによる自動デプロイ構築
- [x] さくらのレンタルサーバーへのデプロイ完了（2026-03-18）
- インフラ: さくらのレンタルサーバー + GitHub Actionsで低コストかつモダンな開発体験を実現
- ※重要: DBはMySQLで完結。Supabase等のBaaSは絶対に使用しない。VercelはNext.js専用。

### フェーズ2：フロントエンドのモダン化（**完了** 2026-03-22）
- **ゴール**: Next.js + LaravelのAPI構成でWebが動くこと
- コンポーネント分割: 現在Bladeで書いているUIを小さな部品に分け、React / Next.js に移行しやすい設計に整理する
- API化: LaravelをデータだけをAPIとして返す構成にし、Next.jsと通信するSPA構成にする

**技術スタック方針（2026-03-19確定）:**
- ZustandやReact Nativeなどフェーズ4以降の要素は今は設計に含めない
- フェーズ4で完成形を決めるときに困らないよう、特定ライブラリへの過度な依存は避ける
- 機能要件: ダークモード対応、レスポンシブUI、SPA（画面遷移なしの快適な操作）
- **Step1完了**: Laravel JSON API追加（/api/topics, /api/topics/{id}, /api/user/me, /api/categories）
- **Step2完了**: Next.js 16.2.0 + TypeScript + Tailwind CSS + shadcn/ui セットアップ
- **Step3進行中**: 画面移行（一覧・詳細・ヘッダー・サイドバー・ログイン実装済み）
- **Step4完了**: 認証実装（Sanctumトークン・login/logout API）

### フェーズ3：集客・マーケティング基盤（**着手中** 2026-03-22〜）
- SEO対策: 適切なHTMLタグ（h1, h2）の使用、メタデータ（OGP設定）
- 表示速度の最適化: 画像の圧縮、不要なコードの削減、Nginxやキャッシュの導入
- LP作成: LOGOSの魅力（レスバではなく議論、AIサポートなど）を伝える登録用ページ
- KPI設定: 新規登録者数、トピック投稿数、継続率などの計測
- Stripe Webhook受け口のみ実装（決済コードの作り込みはしない）
- SSRへの移行（Vercelとさくらのネットワーク疎通問題解決後）

### フェーズ4：スケールとマネタイズ（未着手）
- インフラ移行: さくら環境からAWSやVercel等へ。Docker等を用いた本格運用。
- 本人確認（eKYC）: TRUSTDOCK連携等による質の高いユーザー層の担保
- 有料課金（Stripe連携）: PRO機能や特定トピック作成権限などのサブスク決済システム実装
- AIの全自動更新: エビデンス投稿時に、AIが自動的に時系列や評価表をアップデートするロジック構築

---

## Gitタグ履歴

| タグ | 内容 | 日付 |
|---|---|---|
| v1.0-laravel-only | GitHub Actions動作確認版 | 2026-03-18 |
| v1.0-phase1-complete | Phase1完成・Laravel Blade版本番稼働確認済み | 2026-03-19 |
| v1.1-phase2-step4-complete | Phase2 Step4完了・Sanctum認証API追加済み | 2026-03-20 |

---

## フェーズ2 詳細進捗（2026-03-20時点）

### 完了済み
- Step1: Laravel JSON API追加完了
  - GET /api/topics（トピック一覧・ページネーション）
  - GET /api/topics/{topic}（トピック詳細）
  - GET /api/user/me（auth:sanctum）
  - TopicApiControllerはcategory→categoriesに修正済み
  - laravel/sanctum ^4.3インストール済み
  - bootstrap/app.phpにapi:ルート登録済み

- Step2: Next.js新規作成完了
  - リポジトリ: https://github.com/F-04-yusuke/logos-next
  - 構成: Next.js 16.2.0 + TypeScript + Tailwind CSS + shadcn/ui
  - ローカルパス: ~/logos-next

- Step3: 画面移行（一覧・詳細・ヘッダー・サイドバー・ログイン実装済み）
  - CSR（クライアントサイドレンダリング）方式で実装（Vercel↔さくら疎通問題の暫定対応）

- Step4: 認証実装完了
  - POST /api/login・POST /api/logout 追加
  - User モデルに HasApiTokens トレイト追加
  - personal_access_tokens テーブル作成（ローカル・さくら両方）

### 認証方針（2026-03-20確定）
- Next.js側の認証はLaravel Sanctumのトークン認証（APIトークン方式）を使う
- NextAuth.jsは使わない（ユーザー管理の二重化を避けるため）
- VercelとさくらがドメインをまたぐためCookieではなくTokenベース認証
- トークン保存はlocalStorage（フェーズ2簡易実装）→フェーズ3でhttpOnly Cookie化

### 開発体制（2026-03-21更新）
- **Claude Code がリードエンジニアとしてメインで動く**
- AIチャット（claude.ai）: ブラウザでしか判断できない視覚的レビュー（スクショ確認）のみ
- 大きな方針転換があった場合は必ずCLAUDE.mdに追記してから実装する

---

## 技術スタック全体図

### ユーザー層
- 無料ユーザー: ROM専 / コメント可能
- PROユーザー: 分析ツール利用 / トピック作成 / 月額課金

### システム構成
```
[ユーザー]
    ↓
[Vercel] logos-next（Next.js 16.2.0）
    ↓ API（HTTPS）
[さくらレンタルサーバー] logos-laravel（Laravel 12.x）
    ↓
[MySQL 8.0] mysql3113.db.sakura.ne.jp / gs-f04_logos
```

### 将来構成
- フロントエンド: Vercel（Next.js）
- バックエンド: AWS（将来移行）
- DB: PostgreSQL（AWS RDS・将来移行）
- Webサーバー: Nginx（将来移行）
