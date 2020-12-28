#!/bin/sh

export PGHOST=${DB_HOST}
export PGUSER=${DB_USER}
export PGPASSWORD=${DB_PASSWORD}
export PGDATABASE=${DB_DBNAME}
export PGPORT=${DB_PORT:-5432}


if [ -z $DEBUG ]
then
  set -x
  echo "DEBUG - printenv"
  printenv
fi

echo "This script run before liquibase - TODO update it with what you need"