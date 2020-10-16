#!/bin/sh

export PGHOST=${DB_HOST}
export PGUSER=${DB_USER}
export PGPASSWORD=${DB_PASSWORD}
export PGDATABASE=${DB_DBNAME}
export PGPORT=${DB_PORT:-5432}

if [ -z "$APP_USER" ]; then
  APP_USER="mpoint"
fi

if [ -z $DEBUG ]; then
  set -x
fi

echo "This script run after liquibase and will set the password for the database user: ${APP_USER}"

USER_EXISTS=$(echo "SELECT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = '${APP_USER}');" | psql -t | tr -d '[:space:]')

UPDATE_USER_PASS="ALTER ROLE ${APP_USER} WITH LOGIN PASSWORD '${APP_PASS}'"

if [ "$USER_EXISTS" = "t" ]; then
    echo "USER EXISTS, updating ${APP_USER} password"
    echo "${UPDATE_USER_PASS}" | psql
fi

for i in $(find /app/scripts/sql -name "*.sql" -type f | sort -n); do # will break on whitespaces
    psql -af "$i"
done