# Dockerfile for building the anonymous container for running mPoint test cases
#Fetch dependencies
FROM registry.t.cpm.dev/library/phpcomposerbuildimage:master20200203174815 as builder
COPY composer.json .
RUN composer install -vvv --prefer-dist --no-dev

# Template dockerfile for building the cellpointmobile/main:php-test container
#-----------------------BASEIMAGE BEGIN------------------------

FROM php:7.4.6-apache-buster

RUN apt-get update \
    && apt-get install -y postgresql-11 libpq-dev libxslt-dev less nano vim net-tools acl unzip dos2unix \
    && docker-php-ext-install pgsql xsl

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

#-----------------------BASEIMAGE END------------------------

EXPOSE 80 5432

# Apache vhost
COPY docker/000-default.conf /etc/apache2/sites-available/default

WORKDIR /opt/cpm/mPoint

RUN mkdir /opt/cpm/mPoint/log && chmod -R 777 /opt/cpm/mPoint/log
VOLUME ["/opt/cpm/mPoint"]
RUN setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log

# Project files
COPY api api
COPY test test
COPY conf conf
COPY webroot webroot

#THIS WAS NOT RUN IN THE OLD DOCKERFILE !?
RUN /etc/init.d/postgresql start \
    && cat test/db/mpoint_db.sql | psql -U postgres \
    && /etc/init.d/postgresql stop

# Runtime dependencies
COPY --from=builder /app /opt/cpm/mPoint
RUN cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api

# Prepare entrypoint script
COPY docker/docker.sh /docker.sh
RUN dos2unix /docker.sh
CMD ["/docker.sh"]
