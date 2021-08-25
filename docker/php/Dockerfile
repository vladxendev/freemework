FROM php:7.4.22-fpm

ARG FMW_WORKDIR

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    zlib1g-dev \
    libicu-dev \
    unzip \
    libzip-dev \
    zip \
    libpq-dev \
    libxml2-dev

RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        zip \
        intl \
        exif \
        pcntl \
        opcache \
        pgsql \
        pdo_mysql \
        pdo_pgsql \
        sockets \
        soap \
    && docker-php-ext-enable opcache

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
#RUN pecl install pthreads && docker-php-ext-enable pthreads

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p $FMW_WORKDIR && chmod -R 777 $FMW_WORKDIR

WORKDIR $FMW_WORKDIR

RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/bin

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["php-fpm"]
