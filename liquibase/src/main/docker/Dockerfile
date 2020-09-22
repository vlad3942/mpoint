FROM registry.t.cpm.dev/library/com.cellpointmobile.liquibase.base:1.3.0-SNAPSHOT

RUN apk add --no-cache dos2unix

COPY app/ /app

RUN chmod u+rx /app/scripts/post.sh /app/scripts/pre.sh \
    && dos2unix /app/scripts/* /app/resources/* \
    && apk del dos2unix