FROM php:8.2-fpm-alpine

# 必要な依存パッケージをインストール
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    curl-dev \
    icu-dev \
    oniguruma-dev

# PHP拡張機能をインストール
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    intl \
    mbstring \
    curl

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# CodeIgniter4のインストール（初回のみ実行される）
RUN if [ ! -f /var/www/html/spark ]; then \
    composer create-project codeigniter4/appstarter . --no-interaction || true; \
    fi

# 権限設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
