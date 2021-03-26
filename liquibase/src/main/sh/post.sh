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
GRANT SELECT ON TABLE system.businesstype_tbl TO repuser;
GRANT SELECT ON TABLE system.card_tbl TO repuser;
GRANT SELECT ON TABLE system.cardstate_tbl TO repuser;
GRANT SELECT ON TABLE system.country_tbl  TO repuser;
GRANT SELECT ON TABLE system.currency_tbl  TO repuser;
GRANT SELECT ON TABLE system.externalreferencetype_tbl TO repuser;
GRANT SELECT ON TABLE system.flow_tbl  TO repuser;
GRANT SELECT ON TABLE system.iinaction_tbl TO repuser;
GRANT SELECT ON TABLE system.paymenttype_tbl TO repuser;
GRANT SELECT ON TABLE system.processortype_tbl TO repuser;
GRANT SELECT ON TABLE system.producttype_tbl TO repuser;
GRANT SELECT ON TABLE system.psp_tbl TO repuser;
GRANT SELECT ON TABLE system.sessiontype_tbl TO repuser;
GRANT SELECT ON TABLE system.triggerunit_tbl TO repuser;
GRANT SELECT ON TABLE system.type_tbl TO repuser;
GRANT SELECT ON TABLE system.urltype_tbl TO repuser;
GRANT SELECT ON TABLE log.additional_data_tbl TO repuser;
GRANT SELECT ON TABLE log.externalreference_tbl TO repuser;
GRANT SELECT ON TABLE log.message_tbl TO repuser;
GRANT SELECT ON TABLE log.paymentsecureinfo_tbl TO repuser;
GRANT SELECT ON TABLE log.session_tbl TO repuser;
GRANT SELECT ON TABLE log.settlement_record_tbl TO repuser;
GRANT SELECT ON TABLE log.settlement_tbl TO repuser;
GRANT SELECT ON TABLE log.state_tbl TO repuser;
GRANT SELECT ON TABLE log.transaction_tbl TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_1_1000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_1000001_2000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_2000001_3000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_3000001_4000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_4000001_5000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_5000001_6000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_6000001_7000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_7000001_8000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_8000001_9000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_9000001_10000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_10000001_11000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_11000001_12000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_12000001_13000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_13000001_14000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_14000001_15000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_15000001_16000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_16000001_17000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_17000001_18000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_18000001_19000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10018_19000001_20000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_1_1000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_1000001_2000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_2000001_3000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_3000001_4000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_4000001_5000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_5000001_6000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_6000001_7000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_7000001_8000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_8000001_9000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_9000001_10000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_10000001_11000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_11000001_12000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_12000001_13000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_13000001_14000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_14000001_15000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_15000001_16000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_16000001_17000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_17000001_18000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_18000001_19000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10020_19000001_20000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_1_1000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_1000001_2000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_2000001_3000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_3000001_4000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_4000001_5000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_5000001_6000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_6000001_7000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_7000001_8000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_8000001_9000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_9000001_10000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_10000001_11000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_11000001_12000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_12000001_13000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_13000001_14000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_14000001_15000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_15000001_16000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_16000001_17000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_17000001_18000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_18000001_19000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_19000001_20000001 TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_10069_default TO repuser;
GRANT SELECT ON TABLE log.txnpassbook_tbl_default TO repuser;
GRANT SELECT ON TABLE client.account_tbl TO repuser;
GRANT SELECT ON TABLE client.additionalproperty_tbl TO repuser;
GRANT SELECT ON TABLE client.cardaccess_tbl TO repuser;
GRANT SELECT ON TABLE client.client_tbl TO repuser;
GRANT SELECT ON TABLE client.countrycurrency_tbl TO repuser;
GRANT SELECT ON TABLE client.gatewaytrigger_tbl TO repuser;
GRANT SELECT ON TABLE client.iinlist_tbl TO repuser;
GRANT SELECT ON TABLE client.keyword_tbl TO repuser;
GRANT SELECT ON TABLE client.merchantaccount_tbl TO repuser;
GRANT SELECT ON TABLE client.product_tbl TO repuser;
GRANT SELECT ON TABLE client.url_tbl TO repuser;
GRANT SELECT ON TABLE enduser.account_tbl TO repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA system to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA log to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA client to repuser;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA enduser to repuser;
GRANT SELECT ON TABLE system.fxservicetype_tbl TO repuser;

GRANT SELECT ON TABLE client.route_tbl TO repuser;
GRANT SELECT ON TABLE client.routeconfig_tbl TO repuser;
GRANT SELECT ON TABLE client.routecountry_tbl TO repuser;
GRANT SELECT ON TABLE client.routecurrency_tbl TO repuser;
GRANT SELECT ON TABLE client.routefeature_tbl TO repuser;"


REP_USER_EXISTS=$(echo "SELECT EXISTS (SELECT 1 FROM pg_catalog.pg_roles WHERE rolname = 'repuser');" | psql -t | tr -d '[:space:]')

if [ "${REP_USER_EXISTS}" = "t" ]; then
    echo "REPUSER EXISTS, giving permissions"
    echo ${REPUSERPERMISSIONQUERY} | psql
fi