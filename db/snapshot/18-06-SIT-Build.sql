-- 2c2p alc Airline data improvement -- start --

-- Alter Log.Passenger Tbl to store additional passenger data

ALTER TABLE log.passenger_tbl
  ADD COLUMN title character varying(20);
ALTER TABLE log.passenger_tbl
  ADD COLUMN email character varying(50);
ALTER TABLE log.passenger_tbl
  ADD COLUMN mobile character varying(15);
ALTER TABLE log.passenger_tbl
  ADD COLUMN "country_id" character varying(3);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN tag character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "trip_count" character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "service_level" character varying(2);

-- 2c2p alc Airline data improvement -- end --
-- Alipay Chinese PIDs --

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.html', '2088102135220161', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.app', '2088102170185364', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;



/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: START ------------------*/
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2006, '3d verification successful', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2016, '3d verification failed', 'Payment', '');
/*-----------------Introducing new states for capturing 3DS approved/ rejected transactions: END ------------------*/

/* ----------------Adding Configurations for Modirum MPI - START ------------------------------ */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (47, 'MODIRUM MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,47,'USD');
  INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 47, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
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
bNwLDrkEV6VmYvJIHNDGkkY=
-----END PRIVATE KEY-----
');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 47, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 1, true, 47, 200, 1, null);
/* ----------------Adding Configurations for Modirum MPI - END ------------------------------ */


/*-----------------START : Enabling 3DS for merchant-------------------------*/
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('3DVERIFICATION', 'true', 10007, 'client');
/*-----------------END ; Enabling 3DS for merchant-------------------------*/
/* END: Adding CARD Configuration Entries */
/* ========== CONFIGURE AMEX START ========== */
/*START: Adding PSP entries to the PSP_Tbl table for AMEX*/

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (45, 'Amex',2);

/*END: Adding PSP entries to the PSP_Tbl table for AMEX*/

/*START: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,45,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (578,45,'NOK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,45,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,45,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (208,45,'DKK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (352,45,'ISK');

/*END: Adding Currency entries to the PSPCurrency_Tbl table for AMEX*/

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 45, true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled) VALUES (10007, 1, 45, 200, 1, true);

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX START ========== */
-- Wire-Card
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 45, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 45, '-1');

/* ========== CONFIGURE DEMO ACCOUNT FOR AMEX END ====== */