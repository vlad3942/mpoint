#-----------------------FETCH TEST DEPENDENCIES------------------------

FROM registry.t.cpm.dev/library/phpcomposerbuildimage:master20200203174815 as devbuilder
COPY composer.json .
RUN composer install -v --prefer-dist

#-----------------------RUN UNITTESTS-----------------------------
# TODO This should run simply from pgunittestextras, but we need all db deps to run via liquibase first
FROM registry.t.cpm.dev/library/php:7.4.6-apache-buster as tester

RUN apt update \
    && apt install -y postgresql-11 libpq-dev libxslt-dev acl unzip dos2unix less jq \
    && docker-php-ext-install pgsql xsl \
    && docker-php-ext-install soap

## Host file, database and timezone config
RUN echo "Europe/Copenhagen" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata \
    && echo "fsync=off" >> /etc/postgresql/11/main/postgresql.conf \
    && echo "host all all 127.0.0.1/32 trust" > /etc/postgresql/11/main/pg_hba.conf \
    && echo "host all all ::1/128 trust" >> /etc/postgresql/11/main/pg_hba.conf \
    && echo "local all postgres trust" >> /etc/postgresql/11/main/pg_hba.conf

## Populate session database
COPY test/db/session.sql session.sql
RUN /etc/init.d/postgresql start \
    && cat session.sql | psql -U postgres \
    && /etc/init.d/postgresql stop

RUN a2enmod rewrite

#-----------------------BASEIMAGE END-----------------------------

WORKDIR /opt/cpm/mPoint

# Apache vhost
COPY docker/apache.default.conf /etc/apache2/sites-available/000-default.conf
# Project files
COPY api api
COPY test test
COPY conf conf
COPY webroot webroot
# Runtime dependencies
COPY --from=devbuilder /app /opt/cpm/mPoint

#Load db, run unittests
RUN mkdir /opt/cpm/mPoint/log \
    && chmod -R 777 /opt/cpm/mPoint/log \
    && setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log \
    && /etc/init.d/postgresql start \
    && cat test/db/mpoint_db.sql | psql -U postgres \
    && cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api \
    && echo "127.0.0.1 mpoint.local.cellpointmobile.com" >>/etc/hosts \
    && echo "ServerName mpoint.local.cellpointmobile.com" >>/etc/apache2/ports.conf \
    && /etc/init.d/apache2 start \
    && php vendor/bin/phpunit test/api \
    && /etc/init.d/postgresql stop \
    && /etc/init.d/apache2 stop \
    && rm -rf /opt/cpm/mPoint/webroot/_test

#-----------------------FETCH PROD DEPENDENCIES -----------------

FROM devbuilder as builder
RUN composer install -v --prefer-dist --no-dev

#-----------------------FINAL IMAGE-------------------------------
FROM registry.t.cpm.dev/library/phpfpmextras:master20201020083451

USER 0

WORKDIR /opt/cpm/mPoint

# Project files
COPY api api
COPY conf conf
# webroot without _test folder
COPY --from=tester /opt/cpm/mPoint/webroot webroot
# Runtime dependencies
COPY --from=builder /app /opt/cpm/mPoint

RUN cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api \
    && mkdir /opt/cpm/mPoint/log \
    && chown -R 1000:1000 /opt

USER 1000