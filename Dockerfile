# Dockerfile for building the anonymous container for running mPoint test cases
# Author: johan@cellpointmobile.com

FROM cellpointmobile/main:php-test
EXPOSE 80 5432

WORKDIR /opt/cpm/mPoint

RUN mkdir /opt/cpm/mPoint/log && chmod -R 777 /opt/cpm/mPoint/log
VOLUME ["/var/log/apache2", "/etc/apache2", "/opt/cpm/mPoint"]
RUN setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log

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
