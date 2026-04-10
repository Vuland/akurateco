FROM php:8.1-cli-alpine

WORKDIR /app

RUN set -eux; \
    apk add --no-cache bash git unzip curl; \
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php; \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
    rm -f /tmp/composer-setup.php

COPY composer.json composer.lock* /app/
RUN set -eux; \
    if [ -f composer.lock ]; then composer install --no-interaction --no-progress; else composer install --no-interaction --no-progress; fi

COPY . /app

CMD ["php", "-a"]

