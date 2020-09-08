#Fetch dependencies
FROM composer:1 as composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
WORKDIR /app

# Project files
COPY api api
COPY composer.json .

RUN composer install -v --prefer-dist --no-dev

##------------------------------------------------------------------------------

# Dockerfile for building the anonymous container for running mPoint test cases
# Author: johan@cellpointmobile.com

FROM cellpointmobile/main:php-test
EXPOSE 80 5432
    
# Apache vhost
COPY docker/000-default.conf /etc/apache2/sites-available/default

WORKDIR /opt/cpm/mPoint

RUN mkdir /opt/cpm/mPoint/log && chmod -R 777 /opt/cpm/mPoint/log
VOLUME ["/opt/cpm/mPoint"]
RUN setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log

# Project files
COPY test test
COPY conf conf
COPY webroot webroot

# Composer dependencies
COPY --from=composer /app /opt/cpm/mPoint

# Runtime dependencies
COPY build/php5api-*.zip /opt/php5api.zip
COPY build/phpunit/phpunit-4.3.5.phar phpunit.phar

# Unzip php5api
WORKDIR /opt
RUN unzip php5api.zip
WORKDIR /opt/cpm/mPoint

# Prepare entrypoint script
COPY docker/docker.sh /docker.sh
RUN dos2unix /docker.sh
CMD ["/docker.sh"]
