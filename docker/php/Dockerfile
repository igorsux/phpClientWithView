FROM php:7.2.13-fpm-stretch

RUN buildDeps="libpq-dev libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libmagickwand-6.q16-dev" && \
    apt-get update && \
    apt-get install -y $buildDeps --no-install-recommends && \
    ln -sf /usr/lib/x86_64-linux-gnu/ImageMagick*/bin-*/MagickWand-config /usr/bin && \
    pecl install imagick && \
    echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install \
        opcache \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        mysqli \
        pgsql \
        sockets \
        intl

RUN echo "date.timezone = UTC" >> /usr/local/etc/php/conf.d/php.ini

CMD ["php-fpm"]