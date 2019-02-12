--Payment is currently unavailable due to large amount of IDR currency
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;
update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=10018;

--Invalid Currency issue for RGN so checkout is failing
UPDATE System.Country_Tbl set id = 653, currencyid = 104, alpha2code = 'MM', alpha3code = 'MMR', code = 104 WHERE id = 653;

--new fields to be added in callback (OD - PSPs)
alter table log.additional_data_tbl alter column value type varchar(50) using value::varchar(50);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10018, 'client');
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10018,653,840);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'CAV', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 25), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', '2C2', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 26), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'MBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 27), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'PBB', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 28), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('SHORT-CODE', 'FPX', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10018 and pspid = 51), 'merchant');

-- Card Prefix for visa and Master
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 5110, 5210);
INSERT INTO "system".cardprefix_tbl ( cardid, min, max) VALUES( 7, 2700, 2730);

---AMEX ONBOARD SGA (https://confluence.cellpointmobile.com/display/CWK/AMEX+Integration)

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_ORIGIN', 'Cellpoint Mobile', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_COUNTRY_CODE', '682', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_REGION', 'EMEA', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_MESSAGE_TYPE', 'ISO GCAG', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_MERCHANT_NUMBER', '4417414679', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_ROUTING_INDICATOR', '050', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_BUSINESS_CODE', '4511', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_CITY', 'BROBY', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_ADDRESS', 'BOULEVARD 4', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_NAME', 'AMEX TESTER', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_TERMINAL_ID', '208752', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_COUNTRY', 'SAU', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_REGION', 'SA', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_ZIP', '85054 ', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_MESSAGE_REASON_CODE', '1100', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_CARD_ACCEPTOR_IDENTIFICATION_CODE', '4417414679', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_SUBMITTER_ID', 'CPMOB682', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_SFTP_HOST', 'https://fsgateway.aexp.com', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_SFTP_USERNAME', 'CPMTST', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_SFTP_PASSWORD', 'CPMOB682', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('AMEX_SFTP_FILENAME', 'CPMTST.TEST.CPMOB682', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10021 and pspid = <amexpspid>), 'merchant');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('AIRLINE_CODE', '6S', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARRIER_NAME', 'SAUDI GULF AIRLINES', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('TICKET_ISSUE_CITY', 'DEIRA', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_COUNTRY', 'DNK', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_REGION', 'DK', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_ZIP', '3266', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_CITY', 'Broby', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_ADDRESS', 'Boulevard 4', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_NAME', 'NETS TESTER', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_IDENTIFICATION_CODE', '1978551', 10021, 'client', true);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled) VALUES ('CARD_ACCEPTOR_BUSINESS_CODE', '4816', 10021, 'client', true);
