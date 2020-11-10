#!/bin/sh

set -ex

if [[ -z "${STATIC_FILES_PATH}" ]]; then
  echo "env STATIC_FILES_PATH not set, skipping static files copy!"
else
  echo "env STATIC_FILES_PATH set, copying static files to dest..."
  mkdir -p "$STATIC_FILES_PATH"
  cp -r /opt/cpm/mPoint/webroot/wsdl "$STATIC_FILES_PATH/"
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
