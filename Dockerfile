# PHPの最新安定版イメージをベースとする
FROM php:8.3-fpm

# 必要なPHP拡張機能のインストール (Laravelでよく使われるもの)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリの設定
WORKDIR /var/www/html

# Laravelアプリケーションのファイルは後でマウントするため、ここではコピーしない

# ポート（PHP-FPMはデフォルトで9000番ポートを使用）
EXPOSE 9000

# コンテナ起動時のコマンド
CMD ["php-fpm"]