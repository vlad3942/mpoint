#-----------------------FETCH TEST DEPENDENCIES------------------------

FROM registry.t.cpm.dev/library/phpcomposerbuildimage:latest as devbuilder
COPY composer.json composer.lock* ./
RUN composer install -v --prefer-dist

#-----------------------RUN UNITTESTS-----------------------------
#Run unittests
FROM registry.t.cpm.dev/library/pgunittestextras:master20201202121918

#Overrides baseimage envs
ENV POSTGRES_DB=mpoint
ENV APPROOT=/opt/cpm/mPoint
ENV WEBROOT=$APPROOT/webroot
ENV PHPUNIT_EXEC_PATH=$APPROOT/vendor/bin/phpunit
ENV PHPUNIT_CONFIG_PATH=$APPROOT/phpunit.xml
ENV LIQUIBASE_CHANGELOG_ROOT=/liquibase/db
ENV SERVER_NAME=mpoint.local.cellpointmobile.com
ENV APP_LOG_FOLDER_PATH=/opt/cpm/mPoint/log

# mPoint specific: log to files (filenames defined in global config file)
ENV LOG_OUTPUT_METHOD=1

WORKDIR /opt/cpm/mPoint

COPY api api
COPY test test
COPY conf conf
COPY webroot webroot
COPY phpunit.xml phpunit.xml
COPY --from=devbuilder /app /opt/cpm/mPoint
COPY liquibase/src/main/resources/liquibase/db /liquibase/db

RUN mkdir "$APP_LOG_FOLDER_PATH" && cd "$APP_LOG_FOLDER_PATH" \
    && touch db_exectime_.log db_error_.log app_error_.log \
    && chmod -R 777 "$APP_LOG_FOLDER_PATH" \
    && /sh/runtests.sh

#-----------------------FETCH PROD DEPENDENCIES -----------------

FROM devbuilder as builder
RUN composer install -v --prefer-dist --no-dev

#-----------------------FINAL IMAGE-------------------------------
FROM registry.t.cpm.dev/library/phpfpmextras:master20210127155931

USER 0

WORKDIR /opt/cpm/mPoint

# Project files
COPY api api
COPY conf conf
COPY webroot webroot
COPY --from=builder /app /opt/cpm/mPoint

RUN apk add --no-cache dos2unix \
    && dos2unix /entrypoint.sh \
    && chmod +x /entrypoint.sh \
    && mkdir /opt/cpm/mPoint/log \
    && rm -rf /opt/cpm/mPoint/webroot/_test \
    && chown -R 1000:1000 /opt

USER 1000