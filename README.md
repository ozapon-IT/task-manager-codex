# Task Manager Monorepo

Task Manager は Nuxt 3 と Laravel 12 を Docker Compose で統合したフルスタックなタスク管理アプリケーションです。バックエンド API と SPA を同時に起動できるため、プロジェクト／タスクの CRUD、ダッシュボード、ダークモード切り替えを一貫した UI で確認できます。

## プロジェクト概要
- Laravel API は `/api/projects` と `/api/projects/{project}/tasks` を中心とした REST エンドポイントを提供し、プロジェクトに紐づくタスクをネストされたリソースとして管理します。
- Nuxt 3 SPA はダッシュボード・プロジェクト一覧・詳細ビューを備え、タスクのクイック完了、優先度／ステータス更新、モーダルでの編集に対応しています。
- ヘッダーのトグルでライト／ダークモードを切り替えられ、Tailwind CSS によるテーマ設定を適用しています。
- Docker Compose（backend / frontend / mysql / nginx）のマルチコンテナ構成で開発環境を提供し、HTTP 入口は `http://localhost:8080`、フロント開発サーバーは `http://localhost:3000` です。

## 技術スタック
| コンポーネント | バージョン / 備考 |
| --- | --- |
| Laravel | 12.x |
| PHP | 8.3（php:8.3-fpm ベース） |
| Composer | 2.7 |
| Nuxt | 3.11.x |
| Node.js | 20.x（node:20-alpine） |
| TypeScript | 5.4.x |
| Tailwind CSS | 3.4.x |
| Pinia | 2.1.x |
| Vee Validate | 4.12.x |
| Axios | 1.6.x |
| MySQL | 8.0 |
| Docker Compose | v2 |
| テスト | PHPUnit 11 / Vitest 3 / Cypress 13 |

## ディレクトリ構成
- `backend/` — Laravel 12 API（Eloquent モデル、REST コントローラ、マイグレーション／シーディングを含む）
- `frontend/` — Nuxt 3 + TypeScript SPA（`src/` 配下にレイアウト・ページ・Pinia ストアを配置）
- `docker/` — php-fpm・nginx の Dockerfile と設定
- `docs/` — プロジェクトドキュメント（仕様書など）
- `Makefile` — Compose 操作用の簡易コマンドセット

## 前提条件
- Docker Desktop 4.20+（または Docker Engine + Docker Compose v2）
- Node.js 20.x（ホスト側でフロントエンドを直接実行する場合に利用）
- GNU Make 3.81+（任意、`make` コマンドを使う場合）

## 初回セットアップ
1. リポジトリを取得後、環境変数ファイルを用意します（必要に応じて上書きしてください）。
   ```bash
   cp .env.example .env
   cp backend/.env.example backend/.env
   ```
2. 依存関係をインストールします（コンテナ側で実行されます）。
   ```bash
   docker compose run --rm backend composer install
   docker compose run --rm backend php artisan key:generate
   docker compose run --rm frontend npm install
   ```
3. コンテナをビルド・起動します。
   ```bash
   docker compose up -d
   ```
4. マイグレーションと初期データ投入を実行します。
   ```bash
   docker compose exec backend php artisan migrate --seed
   ```
5. Nuxt の開発サーバーは `frontend` コンテナで `npm run dev` が常時起動します。ホスト OS で直接立ち上げたい場合は以下を実行してください。
   ```bash
   cd frontend
   npm run dev
   ```

## よく使うコマンド
| コマンド | 説明 |
| --- | --- |
| `docker compose up -d` | すべてのサービスをバックグラウンドで起動 |
| `docker compose down` | コンテナを停止（ボリュームは保持） |
| `docker compose exec backend php artisan migrate --seed` | データベースを最新化してシード実行 |
| `docker compose exec backend php artisan test` | Laravel のテストスイートを実行 |
| `docker compose exec frontend npm run dev` | Nuxt 開発サーバーを再起動（ホットリロード確認） |
| `docker compose exec frontend npm run test:unit` | フロントエンドの Vitest 実行 |
| `docker compose logs -f backend` | バックエンドのリアルタイムログを確認 |
| `make up` / `make down` | Makefile 経由で `docker compose up -d` / `docker compose down` を実行 |

## テストと品質チェック
- バックエンド: `docker compose exec backend php artisan test`（Pest は未導入、PHPUnit 11 で実行）
- フロントエンド（単体）: `docker compose exec frontend npm run test:unit`
- フロントエンド（E2E）: `docker compose exec frontend npm run test:e2e`
- コード整形・Lint:
  - PHP: `docker compose exec backend ./vendor/bin/pint`
  - Frontend: `docker compose exec frontend npm run lint`

## 補足
- MySQL のデータは `mysql_data` ボリュームに保持されます。初期化したい場合は `docker compose down -v` を利用してください。
- API のエントリポイントは `http://localhost:8080/api`、SPA は `http://localhost:3000`（ホットリロード）で確認できます。
- ログ閲覧や手動操作には `make backend-shell` / `make frontend-shell` / `make mysql-shell` などのヘルパーを利用できます。
