#-----------------------FETCH TEST DEPENDENCIES------------------------

FROM registry.t.cpm.dev/library/phpcomposerbuildimage:master20200203174815 as devbuilder
COPY composer.json .
RUN composer install -v --prefer-dist

#-----------------------RUN UNITTESTS-----------------------------
# TODO Clean this up MartinW
#Run unittests
FROM registry.t.cpm.dev/library/pgunittestextras:master20201027200551

ENV APPROOT=/opt/cpm/mPoint
ENV WEBROOT=$APPROOT/webroot
ENV PHPUNIT_EXEC_PATH=$APPROOT/vendor/bin/phpunit
ENV PHPUNIT_CONFIG_PATH=$APPROOT/phpunit.xml
ENV LIQUIBASE_CHANGELOG_ROOT=/liquibase/db

#postgres specific
ENV POSTGRES_USER=postgres
ENV POSTGRES_PASSWORD=postgres
ENV POSTGRES_DB=mpoint

#liquibase specific
ENV DB_HOST=localhost
ENV DB_PORT=5432
ENV DB_DATABASE=mpoint
ENV DB_USERNAME=postgres
ENV DB_PASSWORD=postgres

# mPoint specific
ENV LOG_OUTPUT_METHOD=1

WORKDIR /opt/cpm/mPoint

COPY api api
COPY test test
COPY conf conf
COPY webroot webroot
COPY phpunit.xml phpunit.xml
COPY --from=devbuilder /app /opt/cpm/mPoint
COPY liquibase/src/main/resources/liquibase/db /liquibase/db
COPY docker/runtests.sh /sh/runtests.sh

RUN mkdir /opt/cpm/mPoint/log \
    && cd /opt/cpm/mPoint/log && touch db_exectime_.log db_error_.log app_error_.log \
    && chmod -R 777 /opt/cpm/mPoint/log \
    && cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api \
    && apk add --no-cache apache2 php7-apache2 dos2unix \
    && printf "LoadModule rewrite_module modules/mod_rewrite.so" >> /etc/apache2/httpd.conf \
    && chmod +x -R /sh \
    && dos2unix /sh/*
    
COPY docker/apache.default.conf /etc/apache2/conf.d

RUN /sh/runtests.sh

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
COPY webroot webroot
# Runtime dependencies
COPY --from=builder /app /opt/cpm/mPoint

# TODO CMP-4532	Library dependencies should be fetched from the vendor folder
RUN cp -R /opt/cpm/mPoint/vendor/cellpointmobile/php5api /opt/php5api \
    && mkdir /opt/cpm/mPoint/log \
    && chown -R 1000:1000 /opt \
# webroot must be without _test folder
    && rm -rf /opt/cpm/mPoint/webroot/_test

USER 1000