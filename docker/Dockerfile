FROM dunglas/frankenphp:php8.4

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    @composer \
    intl \
    pdo_mysql \
    mysqli \
    zip \
    bcmath \
    exif \
    gd \
    opcache

RUN apt update && apt install -y ffmpeg

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt update && apt install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/php.ini