--------------------------Custom Google Pay JS Path-----------------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', <Google Pay JS URL>, <Client-id>, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com/payment/od/gpay/googlepay.js', <Client-id>, 'client', true, 2);
--------------------------Custom Google Pay JS Path-----------------------------------------------

-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope)
VALUES('3DSVERSION', '2.0', true, <client ID>, 'client', 2);
-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------

------Worldpay-Modirum-----
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'mpi', true, <merchant-id>, 'merchant', 2);
----------------------
------AMEX-Modirum-----
UPDATE client.additionalproperty_tbl
SET value = 'mpi'
WHERE key = '3DVERIFICATION' and externalid = <AMEX merchant-id> ;
----------------------
-------CYBS AMEX Modirum 3ds 1.0

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10077, 47, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
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
bNwLDrkEV6VmYvJIHNDGkkY=
-----END PRIVATE KEY-----
');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100077, 47, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100770, 47, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position, psp_type) VALUES (10077, 1, true, 47, 640, 1, null, 6);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('3DVERIFICATION', 'mpi', 10077, 'merchant',2);

-- end --

-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope)
VALUES('3DSVERSION', '2.0', true, <client ID>, 'client', 2);
-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------

------Worldpay-Modirum-----
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'mpi', true, <merchant-id>, 'merchant', 2);
----------------------
------AMEX-Modirum-----
UPDATE client.additionalproperty_tbl
SET value = 'mpi'
WHERE key = '3DVERIFICATION' and externalid = <AMEX merchant-id> ;
----------------------

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'RestrictedTicket', '1', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'FareBasisCode', 'BK', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyName', 'CebuPacificair', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyCode', '5J', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;

-- CMP-4296
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('invoiceidrule_PAYPAL_CEBU', 'invoiceid ::= (psp-config/@id)=="24"=(transaction.@id)', true, 10077, 'client', 0);

/* ========== Grab Pay Integration = STARTS ========== */
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 67, 'dbb00e18-83ee-49cf-b54d-2707a069b3e4', '0112218e-dda0-4ca8-8489-65a3d28abd69', 'apWSvBQj_evmVfzY');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 67, '-1');
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'CLIENT_ID', '14c3e87ce4e04e82954fd78cea2b3a64', id, 'merchant',1 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=67;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'CLIENT_SECRET', 'dcyDLGEYkeLZA1YM', id, 'merchant',1 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=67;
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, enabled,capture_type,psp_type) VALUES (<>, 94, 67, 640, 1, true,2,4);
/* ========== Grab Pay Integration = STARTS ========== */

-- CMP-4323
INSERT INTO client.additionalproperty_tbl
("key", value, enabled, externalid, "type", "scope")
VALUES('PAYPAL_ORDER_NUMBER_PREFIX', 'Cebu Pacific Air - ', true, 10077, 'client', 2);


DELETE FROM client.additionalproperty_tbl where key = 'post_fraud_rule';
DELETE FROM client.additionalproperty_tbl where key = 'mpi_rule';
---2c2p-alc Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<pspid>=="40"
pspid::=(psp-config.@id)', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=40;

---First Data Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="2"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=62;
---WorldPay Rule for MPI---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isSkippAuth::=<status>!=="1"AND<status>!=="2"AND<status>!=="4"AND<status>!=="5"AND<status>!=="6"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
---WorldPay Rule for FRAUD---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<status>=="1"OR<status>=="4"
status::=(card.info-3d-secure.additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;

update client.cardaccess_tbl set enabled = false where psp_type in (9,10) and cardid not in (7,8) and clientid = 10077;


-------------G-CASH 2C2P-ALC FOR CEBU START------------

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, psp_type,capture_type) SELECT 10077, PC.cardid, PC.pspid, 3, 2 FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (93,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, psp_type,capture_type) SELECT <client ID>, PC.cardid, PC.pspid, 3, 2 FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (93,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
-------------G-CASH 2C2P-ALC FOR CEBU END------------

--CMP-4471 [Chase Payment] Limit process file additional property	CMP-4471[Chase Payment] Limit process file additional property--
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) select 'MAX_DOWNLOAD_FILE_LIMIT', '2', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=10069 AND pspid=52;
--END CMP-4471 [Chase Payment] Limit process file additional property	CMP-4471[Chase Payment] Limit process file additional property---------------G-CASH 2C2P-ALC FOR CEBU END------------