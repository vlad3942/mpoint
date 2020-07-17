--------------------------Custom Google Pay JS Path-----------------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', <Google Pay JS URL>, <Client-id>, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com/payment/od/gpay/googlepay.js', <Client-id>, 'client', true, 2);
--------------------------Custom Google Pay JS Path-----------------------------------------------
------Worldpay-Modirum-----
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'mpi', true, <merchant-id>, 'merchant', 2);
----------------------
------AMEX-Modirum-----
UPDATE client.additionalproperty_tbl
SET value = 'mpi'
WHERE key = '3DVERIFICATION' and externalid = <AMEX merchant-id> ;
----------------------