#!/bin/sh
#This script is running unittests, and is to be executed as part of the docker build process.
set -x

echo "127.0.0.1 mpoint.local.cellpointmobile.com http://mpoint.local.cellpointmobile.com" >> /etc/hosts
echo "start webserver process..."
httpd

echo "start postgres process..."
/docker-entrypoint.sh postgres >/dev/null 2>&1 &
while ! nc -z -v -w5 localhost 5432 >/dev/null 2>&1; do sleep 1; done

echo "run liquibase..."

if [ -z "$LOG_LEVEL" ]; then
  echo "Missing LOG_LEVEL variable, setting it to default 'info'. Following values are allowed:"
  echo "off - show no logging at all"
  echo "severe - only show the most severe errors"
  echo "warning - show warnings and severe errors"
  echo "info - show more chatty info level messages, warnings, and severe errors"
  echo "debug - most verbose. Show debug-level messages, chatty info messages, warnings, and severe errors."
  export LOG_LEVEL='info'
fi

url="jdbc:postgresql://$DB_HOST:$DB_PORT/$DB_DATABASE"
liquibase --classpath "/usr/share/java/postgresql-jdbc.jar" \
          --driver=org.postgresql.Driver \
          --changeLogFile=/$LIQUIBASE_CHANGELOG_ROOT/db.changelog.xml \
          --url=$url \
          --username=$DB_USERNAME \
          --password=$DB_PASSWORD \
          --logLevel=$LOG_LEVEL \
          migrate

if [ -z "$POST_SCRIPT_PATH" ]; then
   echo "No postscript detected!"
else
   echo "Postscript detected.. running"
   chmod -R +x "$POST_SCRIPT_PATH"
   "$POST_SCRIPT_PATH"
fi


#execute phpunit
php $PHPUNIT_EXEC_PATH -c $PHPUNIT_CONFIG_PATH