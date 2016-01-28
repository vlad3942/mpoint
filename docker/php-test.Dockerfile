# Template dockerfile for building the cellpointmobile/main:php-test container
# Author: johan@cellpointmobile.com

FROM debian:wheezy
RUN apt-get update && apt-get install -y --force-yes apache2 postgresql libapache2-mod-php5 php5-pgsql php5-xsl less acl unzip

# Apache stuff
COPY docker/php.ini /etc/php5/apache2/php.ini
COPY docker/php.ini /etc/php5/cli/php.ini

# Host file, database and timezone config
RUN echo "host all all 127.0.0.1/32 trust" >/etc/postgresql/9.1/main/pg_hba.conf && echo "host all all ::1/128 trust" >>/etc/postgresql/9.1/main/pg_hba.conf && echo "local all postgres trust" >>/etc/postgresql/9.1/main/pg_hba.conf
RUN echo "Europe/Copenhagen" >/etc/timezone && dpkg-reconfigure -f noninteractive tzdata

# Populate session database
COPY docker/session.sql session.sql
RUN /etc/init.d/postgresql start && cat session.sql |psql -U postgres && /etc/init.d/postgresql stop

RUN a2enmod rewrite
