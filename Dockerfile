FROM debian:wheezy
RUN apt-get update && apt-get install -y --force-yes apache2 postgresql libapache2-mod-php5 php5-pgsql php5-xsl less acl unzip
EXPOSE 80 5432

# Apache stuff
COPY docker/php.ini /etc/php5/apache2/php.ini
COPY docker/php.ini /etc/php5/cli/php.ini
COPY docker/000-default.conf /etc/apache2/sites-available/default

WORKDIR /opt/cpm/mPoint

RUN a2enmod rewrite
RUN mkdir /opt/cpm/mPoint/log
RUN chmod -R 777 /opt/cpm/mPoint/log
VOLUME ["/var/log/apache2", "/etc/apache2", "/opt/cpm/mPoint"]
RUN setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log

# Host file, database and timezone config
RUN echo "host all all 127.0.0.1/32 trust" >/etc/postgresql/9.1/main/pg_hba.conf
RUN echo "host all all ::1/128 trust" >>/etc/postgresql/9.1/main/pg_hba.conf
RUN echo "local all postgres trust" >>/etc/postgresql/9.1/main/pg_hba.conf
RUN echo "Europe/Copenhagen" >/etc/timezone
RUN dpkg-reconfigure -f noninteractive tzdata

# Populate session database
COPY docker/session.sql session.sql
RUN /etc/init.d/postgresql start && cat session.sql |psql -U postgres && /etc/init.d/postgresql stop

# Project files
COPY api api
COPY test test
COPY conf conf
COPY webroot webroot

# Runtime dependencies
COPY build/php5api-1.0.zip /opt/php5api.zip
COPY build/phpunit/phpunit-4.3.5.phar phpunit.phar

# Unzip php5api
WORKDIR /opt
RUN unzip php5api.zip
WORKDIR /opt/cpm/mPoint

# Prepare entrypoint script
COPY docker/docker.sh /docker.sh
ENTRYPOINT ["/docker.sh"]
