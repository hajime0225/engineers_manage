# 人材管理システム (仮称) 環境構築手順

## 1. 概要

本プロジェクトは、派遣事業者からエンジニアを探している企業様向けの材管理システムです。
Docker を使用して、PHP (Laravel)、Nginx、MySQL の開発環境を構築します。

## 2. 使用技術

* **バックエンド**: PHP (最新バージョン)
    * フレームワーク: Laravel (モダンなフレームワークで、開発に必要な機能が標準で備わっています)
* **データベース**: MySQL (PHPとの親和性が高い)
* **ウェブサーバー**: Nginx
* **コンテナ仮想化**: Docker, Docker Compose
* **フロントエンド (検討中)**: Bootstrap (Laravelの標準的なビューと組み合わせやすいため)

## 3. 前提条件

* Docker Desktop がインストールされていること。

## 4. 環境構築手順

### 4.1. プロジェクトディレクトリの作成

任意の場所にプロジェクト用のディレクトリを作成します。

```bash
mkdir laravel-project
cd laravel-project
※ディレクトリ名は任意
```

### 4.2. 環境設定ファイル (.env) の編集

src/.env ファイルを開き、データベース接続情報を以下のように設定（または確認）します。

```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_passå
```

### 4.3. コンテナのビルドと起動

以下のコマンドで Docker コンテナをビルドし、バックグラウンドで起動します。

```bash
docker-compose up -d --build
```

### 4.4. データベースマイグレーション

Laravel が必要とするテーブル（セッション用テーブルなど）をデータベースに作成します。

```bash
docker-compose exec php php artisan migrate
```

### 5. 動作確認

Web ブラウザで http://localhost:8080 にアクセスします。
画面表示されれば環境構築は成功です。

### 6. ディレクトリ構成 (主要部分)

```
laravel-project/
├── Dockerfile             # PHPコンテナの定義
├── docker-compose.yml     # Docker Compose設定ファイル
├── nginx/
│   └── default.conf       # Nginx設定ファイル
└── src/                   # Laravelアプリケーションのソースコード
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    │   └── migrations/    # データベースマイグレーションファイル
    ├── public/            # 公開ディレクトリ (index.phpなど)
    ├── resources/
    │   └── views/         # ビューファイル
    ├── routes/
    │   └── web.php        # ウェブアプリケーションのルート定義
    ├── storage/
    ├── tests/
    ├── vendor/
    ├── .env               # 環境設定ファイル
    └── composer.json      # PHPパッケージ管理
```
