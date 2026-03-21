# インフラ・さくらサーバー・ローカル開発環境

## さくら本番環境

### 接続情報
- SSHホスト: gs-f04.sakura.ne.jp　ポート: 22
- SSHユーザー名: gs-f04（tasakiではない）
- SSH接続コマンド: `ssh gs-f04@gs-f04.sakura.ne.jp`
- SSH鍵認証済み（パスワード不要）
- DBホスト: mysql3113.db.sakura.ne.jp
- DBユーザー: gs-f04（管理ユーザー）
- DB名: gs-f04_logos
- PHPコマンド: `php`（/usr/local/bin/php）
- シェル: csh（bashではない。heredoc非対応。eeエディタで編集可能）
- PHP: 8.3.30（モジュールモード）
- DB: MySQL 8.0
- Webサーバー: Apache（.htaccessで `~/www/logos` から `logos/public` ディレクトリに転送）
- composerは `~/bin/composer` に手動インストール済み
- URL: https://gs-f04.sakura.ne.jp
- タグ: v1.0-laravel-only（Laravel単体完成版スナップショット）

### パスワード管理（混同厳禁）
さくらには3種類の独立したパスワードが存在する。
- さくらCPパスワード: CPログイン・SSH接続に使用（登録メールに記載）
- DBパスワード: MySQL接続・.envのDB_PASSWORDと一致させる
- GitHub SSH秘密鍵: GitHub Actions自動デプロイ用（GitHub Secretsに登録済み）

これらは完全に独立しており、一方を変えても他方には影響しない。
DBパスワードを変更したら即座に.envを更新し接続テストを行うこと。
パスワードリセットは最後の手段。何度もリセットしない。

### デプロイフロー（厳守）
必ずこの順番を守ること。**サーバー上でファイルを直接編集しない。**

1. ローカル(WSL)で編集
2. git commit & push
3. GitHub Actions自動デプロイ（mainブランチpushで自動実行）
4. SSHで以下を実行:
   ```
   cd ~/www/logos
   git pull origin main
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:clear
   ```

### 新環境デプロイ後の必須チェックリスト
- [ ] php artisan migrate --force（テーブル作成）
- [ ] php artisan config:cache
- [ ] 全ページの動作確認（トップ・トピック・ダッシュボード・カテゴリ管理）
- [ ] adminユーザーのis_pro・is_adminをtrueに設定

### さくら環境の注意事項
- PHPはさくらで8.3止まり
- `composer.json` に `platform: {"php": "8.3.30"}` 設定済み（composer.lockはPHP 8.3互換で生成される）
- `config:cache` 実行後は `env()` 不可。必ず `config()` 経由
- Web公開は `~/www/.htaccess` で logos/public に転送
- シェルはFreeBSD（csh）。bashではない。heredoc非対応
- composerは `~/bin/composer` に手動インストール済み

---

## ローカル開発環境（WSL2 + Laravel Sail）

- OS: Windows + WSL2(Ubuntu)
- エディタ: Cursor（Claude Code使用）
- ローカルはLaravel Sail（Docker）で動いている
- SailコンテナはPHP 8.5.3だが、composer.jsonに `platform: {"php": "8.3.30"}` 設定済みのため composer.lockはPHP 8.3互換で生成される（さくら本番と互換）

### コマンド
```bash
# Sail起動・停止
./vendor/bin/sail up -d
./vendor/bin/sail down

# composerコマンドは必ずSail経由で実行
./vendor/bin/sail composer [コマンド]

# phpコマンドも同様
./vendor/bin/sail php artisan [コマンド]

# フロントエンド（Blade版）
./vendor/bin/sail npm run dev

# Next.js
cd ~/logos-next && npm run dev
```

注意: `php` 単体コマンドはWSL上では使えない（command not found になる）

### Next.js使用時の起動順
1. `cd ~/logos && ./vendor/bin/sail up -d` ← LaravelのAPIを叩くため必須
2. `cd ~/logos-next && npm run dev`
3. http://localhost:3000 で確認

---

## GitHub Actions自動デプロイ
- ファイル: `.github/workflows/deploy.yml`
- トリガー: mainブランチpushで自動実行
- SSH秘密鍵: GitHub Secretsに登録済み

---

## 2026-03-19 障害の教訓
カテゴリ管理調査中に以下が発生した:
- `DB_CONNECTION=sqlite` への変更でMySQL接続が切断
- パスワード複数回リセットによる.envとの不一致
- `categories/index.blade.php` が `likes/index.blade.php` の内容で汚染（a5a335aコミット）
- サーバー上での直接ファイル作成によるgit pull競合

→ **Gitの履歴が調査・復元の唯一の手段となった。こまめなコミットを継続すること。**
→ **migrate:fresh・db:wipe・sqlite切り替えは本番環境で絶対に実行しない**
→ **編集前にバックアップコミットを作成する**
