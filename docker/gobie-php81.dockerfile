FROM php:8.1-cli

WORKDIR /app

COPY . /app

# update apt
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip libzip-dev

# install php deps
RUN docker-php-ext-install pcntl && \
    docker-php-ext-install zip

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

ENV PATH $PATH:/root/.composer/vendor/bin

RUN rm -f vendor/ symfony.lock composer.lock && \
    composer update --ignore-platform-reqs --no-interaction --no-progress --optimize-autoloader --no-cache

RUN composer global config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true

# https://web-techno.net/code-quality-check-tools-php/
RUN composer global require --ignore-platform-reqs \
    phpmd/phpmd:2.9.1 squizlabs/php_codesniffer:3.5.6 \
    phpstan/phpstan:0.12.48 phploc/phploc:6.0.2 \ 
    bmitch/churn-php:1.0.3 sensiolabs/security-checker:6.0 \ 
    nunomaduro/phpinsights:1.14.0 vimeo/psalm:3.16

ENTRYPOINT ["php", "bin/console"]

CMD ["app:dependencies"]
