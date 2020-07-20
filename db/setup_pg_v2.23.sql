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