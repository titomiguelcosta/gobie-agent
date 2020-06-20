FROM php:7.3-cli

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

RUN composer install

# avoid conflicts on global packages https://github.com/consolidation/cgr
RUN composer global require consolidation/cgr

# https://web-techno.net/code-quality-check-tools-php/
RUN cgr phpunit/phpunit \
    phpmd/phpmd phpmetrics/phpmetrics squizlabs/php_codesniffer \
    phpstan/phpstan phploc/phploc sebastian/phpcpd bmitch/churn-php \
    sensiolabs/security-checker nunomaduro/phpinsights \
    vimeo/psalm

ENTRYPOINT ["php", "bin/console"]

CMD ["app:dependencies"]
