FROM php:8.0-rc-cli

WORKDIR /app

COPY . /app

# update apt
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip libzip-dev

RUN docker-php-ext-install pcntl
RUN docker-php-ext-install zip

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"
ENV PATH $PATH:/root/.composer/vendor/bin

RUN composer install --ignore-platform-reqs

# avoid conflicts on global packages https://github.com/consolidation/cgr
RUN composer --ignore-platform-reqs global require consolidation/cgr

# https://web-techno.net/code-quality-check-tools-php/
RUN cgr --ignore-platform-reqs \
    phpmd/phpmd:2.9.1 squizlabs/php_codesniffer:3.5.6 \
    phpstan/phpstan:0.12.48 phploc/phploc:6.0.2 \ 
    bmitch/churn-php:1.0.3 sensiolabs/security-checker:6.0 \ 
    nunomaduro/phpinsights:1.14.0 vimeo/psalm:3.16

ENTRYPOINT ["php", "bin/console"]

CMD ["app:dependencies"]
