FROM php:7.3-cli

WORKDIR /app

COPY . /app

# install git
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

# install composer
# RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
# RUN php -r "unlink('composer-setup.php');"
# ENV PATH $PATH:/root/.composer/vendor/bin

# https://web-techno.net/code-quality-check-tools-php/
# RUN composer global require phpunit/phpunit
# RUN composer global require friendsofphp/php-cs-fixer
# RUN composer global require phpmd/phpmd
# RUN composer global require phpmetrics/phpmetrics
# RUN composer global require squizlabs/php_codesniffer
# RUN composer global require phpstan/phpstan
# RUN composer global require phploc/phploc
# RUN composer global require sebastian/phpcpd
# RUN composer global require bmitch/churn-php

ENTRYPOINT ["/bin/bash"]

CMD ["/app/docker/boot.sh"]
