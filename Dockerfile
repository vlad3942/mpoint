#-----------------------FETCH TEST DEPENDENCIES------------------------

FROM registry.t.cpm.dev/library/phpcomposerbuildimage:master20200203174815 as devbuilder
COPY composer.json .
RUN composer install -v --prefer-dist

#-----------------------RUN UNITTESTS-----------------------------
# TODO This should run simply from pgunittestextras, but we need all db deps to run via liquibase first
FROM registry.t.cpm.dev/library/php:7.4.6-apache-buster

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

EXPOSE 80 5432

# Apache vhost
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /opt/cpm/mPoint

RUN mkdir /opt/cpm/mPoint/log && chmod -R 777 /opt/cpm/mPoint/log
VOLUME ["/opt/cpm/mPoint"]
RUN setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log

# Project files
COPY api api
COPY test test
COPY conf conf
COPY webroot webroot

# Load test db
RUN /etc/init.d/postgresql start \
    && cat test/db/mpoint_db.sql | psql -U postgres \
    && /etc/init.d/postgresql stop

# Runtime dependencies
COPY --from=devbuilder /app /opt/cpm/mPoint
RUN cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api

# Prepare entrypoint script, env debug disables unnittests
#ENV debug=true
COPY docker/docker.sh /docker.sh
RUN dos2unix /docker.sh && /docker.sh

#-----------------------FETCH PROD DEPENDENCIES -----------------

FROM devbuilder as builder
RUN composer install -v --prefer-dist --no-dev

#-----------------------FINAL IMAGE-------------------------------

FROM registry.t.cpm.dev/library/phpfpmextras:master20201006171426

WORKDIR /opt/cpm/mPoint

# Project files
COPY api api
COPY conf conf
COPY webroot webroot

# Runtime dependencies
COPY --from=builder /app /opt/cpm/mPoint
RUN cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api
RUN mkdir /opt/cpm/mPoint/log && chmod -R 777 /opt/cpm/mPoint/log