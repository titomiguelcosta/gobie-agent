FROM php:7.4-cli

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

RUN rm -f symfony.lock composer.lock && \
    composer update --ignore-platform-reqs --ignore-platform-reqs --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader

# avoid conflicts on global packages https://github.com/consolidation/cgr
RUN composer global require consolidation/cgr

# https://web-techno.net/code-quality-check-tools-php/
RUN cgr \ 
    phpmd/phpmd:2.9.1 squizlabs/php_codesniffer:3.5.6 \
    phpstan/phpstan:0.12.48 phploc/phploc:6.0.2 \ 
    bmitch/churn-php:1.0.3 sensiolabs/security-checker:6.0 \ 
    nunomaduro/phpinsights:1.14.0 vimeo/psalm:3.16

ENTRYPOINT ["php", "bin/console"]

CMD ["app:dependencies"]
