#!/bin/sh
#This script is running unittests, and is to be executed as part of the docker build process.
set -x

echo "start postgres daemon process..."
/docker-entrypoint.sh postgres >/dev/null 2>&1 &
while ! nc -z -v -w5 localhost 5432 >/dev/null 2>&1; do sleep 1; done

echo "127.0.0.1 mpoint.local.cellpointmobile.com http://mpoint.local.cellpointmobile.com" >> /etc/hosts
echo "start webserver daemon process..."
httpd

echo "run liquibase..."
liquibase --classpath "/usr/share/java/postgresql-jdbc.jar" \
          --driver=org.postgresql.Driver \
          --changeLogFile=/$LIQUIBASE_CHANGELOG_ROOT/db.changelog.xml \
          --url="jdbc:postgresql://localhost:5432/$DB_DATABASE" \
          --username=$DB_USERNAME \
          --password=$DB_PASSWORD \
          --logLevel=info \
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
exitval=$?

#output logs if testing failed
if [ $exitval -ne 0 ]; then
  echo; echo; echo "###################### LOG OUTPUT ###########################"; echo; echo;
  rm /opt/cpm/mPoint/log/access.log
  tail -n +1 /opt/cpm/mPoint/log/*
fi

exit $exitval