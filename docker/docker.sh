#!/bin/bash
# USE the trap if you need to also do manual cleanup after the service is stopped,
#     or need to start multiple services in the one container
trap "echo TRAPed signal" HUP INT QUIT KILL TERM

# insert special hostnames
echo "127.0.0.1 mpoint.local.cellpointmobile.com" >>/etc/hosts
echo "ServerName mpoint.local.cellpointmobile.com" >>/etc/apache2/ports.conf

# start services in background here
/etc/init.d/postgresql start
/etc/init.d/apache2 start

setfacl -d -m group:www-data:rwx /opt/cpm/mPoint/log
cd /opt/cpm/mPoint
chmod -R 777 log

if [ -z ${debug+x} ]; then
	php phpunit.phar test
else
	/bin/bash
fi
