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

REPUSERPERMISSIONQUERY="GRANT USAGE ON SCHEMA system, log, client, enduser TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.businesstype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.card_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.cardstate_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.country_tbl  TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.currency_tbl  TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.externalreferencetype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.flow_tbl  TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.iinaction_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.paymenttype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.processortype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.producttype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.psp_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.sessiontype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.triggerunit_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.type_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE system.urltype_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.additional_data_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.externalreference_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.message_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.paymentsecureinfo_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.session_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.settlement_record_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.settlement_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.state_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.transaction_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE log.txnpassbook_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.account_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.additionalproperty_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.cardaccess_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.client_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.countrycurrency_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.gatewaytrigger_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.iinlist_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.keyword_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.merchantaccount_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.product_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE client.url_tbl TO repuser;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE enduser.account_tbl TO repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA system to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA log to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA client to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA enduser to repuser;"

REP_USER_EXISTS=$(echo "SELECT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = 'repuser');" | psql -t | tr -d '[:space:]')

if [ "${REP_USER_EXISTS}" = "t" ]; then
    echo "REPUSER EXISTS, giving permissions"
    echo ${REPUSERPERMISSIONQUERY} | psql
fi