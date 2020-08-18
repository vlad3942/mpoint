--------------------------Custom Google Pay JS Path-----------------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', <Google Pay JS URL>, <Client-id>, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com/payment/od/gpay/googlepay.js', <Client-id>, 'client', true, 2);
--------------------------Custom Google Pay JS Path-----------------------------------------------

-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope)
VALUES('3DSVERSION', '2.0', true, <client ID>, 'client', 2);
-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------

------------------------- Card prefix range for VISA/Dankort -------------------------------------
UPDATE system.cardprefix_tbl SET cardid=37 WHERE cardid=2 AND min=5019 AND max=5019;

------------------------- Add new state id into system.cardstate_tbl -------------------------------------

INSERT INTO System.CardState_Tbl (id, name) VALUES (6, 'Disable Show');
------Worldpay-Modirum-----
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'mpi', true, <merchant-id>, 'merchant', 2);
----------------------
------AMEX-Modirum-----
UPDATE client.additionalproperty_tbl
SET value = 'mpi'
WHERE key = '3DVERIFICATION' and externalid = <AMEX merchant-id> ;
----------------------
-------CYBS AMEX Modirum 3ds 1.0
--insert into required currency
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,47,'PHP');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,47,'USD');

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

------------------------- Card prefix range for VISA/Dankort -------------------------------------
UPDATE system.cardprefix_tbl SET cardid=37 WHERE cardid=2 AND min=5019 AND max=5019;

------------------------- Add new state id into system.cardstate_tbl -------------------------------------

INSERT INTO System.CardState_Tbl (id, name) VALUES (6, 'Disable Show');
------Worldpay-Modirum-----
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'mpi', true, <merchant-id>, 'merchant', 2);
----------------------
------AMEX-Modirum-----
UPDATE client.additionalproperty_tbl
SET value = 'mpi'
WHERE key = '3DVERIFICATION' and externalid = <AMEX merchant-id> ;
----------------------

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3117, 'Post-screening Check not attempted Due to rule matched', 'Fraud', '');

---2c2p-alc Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<isPostFraudAttemp1>OR<isPostFraudAttemp2>OR<isPostFraudAttemp3>OR<isPostFraudAttemp4>
isPostFraudAttemp1::=<cardid>=="7"AND<eci>=="2"AND<isCryptogrm>!==""
isPostFraudAttemp2::=<cardid>=="7"AND<eci>=="1"AND<isCryptogrm>!==""
isPostFraudAttemp3::=<cardid>=="8"AND<eci>=="5"AND<isCryptogrm>!==""
isPostFraudAttemp4::=<cardid>=="8"AND<eci>=="6"AND<isCryptogrm>!==""
eci::=(card.info-3d-secure.cryptogram.@eci)
cardid::=(card.@type-id)
isCryptogrm::={trim.(card,info-3d-secure,cryptogram)}', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=40;

---First Data Rule---
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'post_fraud_rule', 'isPostFraudAttemp::=<eci>=="1"OR<eci>=="2"OR<eci>=="4"
eci::=(card.info-3d-secure.additional-data.param[@name=''Secure3DResponse''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=62;

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2017, 'Authorization not attempted due to rule matched', 'Payment', '');

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'allowAuth::=<allowAuth1>OR<allowAuth2>OR<allowAuth3>OR<allowAuth4>
pares::= (card.info-3d-secure.additional-data.param[@name=''paresTxStatus''])
veres::= (card.info-3d-secure.additional-data.param[@name=''veresEnrolledStatus''])
allowAuth1::= <pares>=="Y"AND<veres>=="Y"
allowAuth2::= <pares>=="Y"AND<veres>=="A"
allowAuth3::= <pares>=="Y"AND<veres>=="U"
allowAuth4::= <pares>=="Y"AND<veres>=="Y"', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=<> AND pspid=<pspid>;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'RestrictedTicket', '1', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'FareBasisCode', 'BK', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyName', 'CebuPacificair', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) select 'TravelAgencyCode', '5J', id, 'merchant',2 from client.merchantaccount_tbl WHERE clientid=<> AND pspid=4;

-------------G-CASH 2C2P-ALC FOR CEBU START------------
INSERT INTO system.card_tbl (id, name, position, minlength, maxlength, cvclength, paymenttype) VALUES (95, 'Gcash', 23, -1, -1, -1, 3);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (40, 95);
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT 10077, PC.cardid, PC.pspid 
FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (95,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 95, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 608;
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,40,'PHP');
-------------G-CASH 2C2P-ALC FOR CEBU END------------
