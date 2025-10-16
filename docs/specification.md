# Task Manager 仕様概要

## システム全体像
- Nuxt 3 SPA と Laravel 12 API を分離したレイヤーで構築し、Docker Compose（backend / frontend / mysql / nginx）が開発環境を提供します。
- `nginx` コンテナが `http://localhost:8080` を公開し、`/api` へのリクエストを php-fpm（backend）にルーティングします。Nuxt の開発サーバーは `http://localhost:3000` でホットリロード付きの SPA を提供します。
- MySQL 8.0 はプロジェクトとタスクの永続化を担い、ボリューム `mysql_data` にデータを保存します。

| サービス | ポート | 役割 |
| --- | --- | --- |
| backend (php-fpm) | 内部 9000 | Laravel 12 API 実行環境 |
| frontend (Nuxt dev) | 3000 | Nuxt 3 開発サーバー（Vite ベース） |
| nginx | 8080 | ルーティング／静的配信／API プロキシ |
| mysql | 3306 | MySQL 8.0 データベース |

## 技術スタック
| レイヤー | 技術 | バージョン / 備考 |
| --- | --- | --- |
| フレームワーク (Backend) | Laravel | 12.x |
| 言語 (Backend) | PHP | 8.3（php:8.3-fpm） |
| フレームワーク (Frontend) | Nuxt | 3.11.x（Vue 3 + Vite） |
| 言語 (Frontend) | TypeScript | 5.4.x |
| スタイリング | Tailwind CSS | 3.4.x、ライト／ダークモード両対応 |
| 状態管理 | Pinia | 2.1.x |
| フォーム検証 | Vee Validate | 4.12.x |
| HTTP クライアント | Axios | 1.6.x |
| データベース | MySQL | 8.0 |
| テスト | PHPUnit 11、Vitest 3、Cypress 13 |

## 機能仕様
- **ダッシュボード**
  - プロジェクトの進行中件数、当日タスク数、直近 3 日以内のタスク数を KPI カードで表示。
  - 優先度と期日でソートした高優先タスクリストを表示し、ステータス更新・編集・削除をワンクリックで実行。
  - ダーク／ライトモードをヘッダー右上のトグルで切り替え。
- **プロジェクト管理**
  - プロジェクト一覧・作成フォーム・削除操作を 1 画面に集約。
  - プロジェクトにはタイトル、説明、期日、ステータス（`draft` / `active` / `completed` / `archived`）を設定可能。
  - プロジェクト詳細画面ではメタ情報の更新とタスクテーブルを表示。
- **タスク管理**
  - タスクはプロジェクトにネストされたリソースとして CRUD 操作（作成・編集・削除・完了／再オープン）を提供。
  - ステータス（`todo` / `in_progress` / `blocked` / `done`）と優先度（`low` / `medium` / `high` / `critical`）をモーダルで編集。
  - クイック完了ボタンでステータスを `done` ⇔ `todo` に即時切り替え。
  - 期日、説明、優先度の表示を含むテーブルビューを備え、API と同期したデータを Pinia ストアで管理。

## API 仕様（REST）
| メソッド | パス | 概要 |
| --- | --- | --- |
| GET | `/api/ping` | ヘルスチェック（`{"pong": true}` を返却） |
| GET | `/api/projects` | プロジェクト一覧（タスクを含むページネーション付き JSON） |
| POST | `/api/projects` | プロジェクト作成 |
| GET | `/api/projects/{project}` | プロジェクト詳細（関連タスクを含む） |
| PUT | `/api/projects/{project}` | プロジェクト更新 |
| DELETE | `/api/projects/{project}` | プロジェクト削除（関連タスクを含めてソフトデリート） |
| GET | `/api/projects/{project}/tasks` | プロジェクトに紐づくタスク一覧 |
| POST | `/api/projects/{project}/tasks` | タスク作成 |
| GET | `/api/projects/{project}/tasks/{task}` | タスク詳細 |
| PUT | `/api/projects/{project}/tasks/{task}` | タスク更新 |
| DELETE | `/api/projects/{project}/tasks/{task}` | タスク削除 |

- レスポンス形式はすべて JSON。リクエストボディは `application/json`。
- バリデーションは Laravel のフォームリクエスト（`$request->validate()`）で実施し、エラー時は 422 を返却。
- 削除処理はプロジェクト／タスクともにソフトデリートを採用し、関連タスクは削除イベントで連鎖的に削除されます。

## データモデル
### Project
| カラム | 型 / 備考 |
| --- | --- |
| `id` | bigint, 主キー |
| `title` | string(255), 必須 |
| `description` | text, nullable |
| `due_date` | date, nullable |
| `status` | enum（`draft` / `active` / `completed` / `archived`） |
| `created_at` / `updated_at` | timestamp |
| `deleted_at` | timestamp, SoftDeletes |

### Task
| カラム | 型 / 備考 |
| --- | --- |
| `id` | bigint, 主キー |
| `project_id` | foreignId → projects.id（`cascadeOnDelete`） |
| `title` | string(255), 必須 |
| `description` | text, nullable |
| `due_date` | date, nullable |
| `status` | enum（`todo` / `in_progress` / `blocked` / `done`） |
| `priority` | string（`low` / `medium` / `high` / `critical`） |
| `created_at` / `updated_at` | timestamp |
| `deleted_at` | timestamp, SoftDeletes |

- シーダーはダミーのプロジェクト 5 件と各プロジェクトに 3〜5 件のタスクを作成します。

## 運用・開発フロー
- `docker compose up -d` で全コンテナを起動し、`docker compose exec backend php artisan migrate --seed` でデータベースを初期化します。
- フロントエンドは `frontend` コンテナ内の `npm run dev` でホットリロードが有効になり、`npm run dev` をホストで実行することでスタンドアロン開発も可能です。
- テストは `php artisan test`（バックエンド）、`npm run test:unit` / `npm run test:e2e`（フロントエンド）で実施します。
- テーマ設定は `localStorage` に保存され、初回アクセス時はシステムの配色設定に追従します。
