--------------------------Custom Google Pay JS Path-----------------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', <Google Pay JS URL>, <Client-id>, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('GOOGLE_PAY_JS_URL', 'https://devcpmassets.s3-ap-southeast-1.amazonaws.com/payment/od/gpay/googlepay.js', <Client-id>, 'client', true, 2);
--------------------------Custom Google Pay JS Path-----------------------------------------------

-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope)
VALUES('3DSVERSION', '2.0', true, <client ID>, 'client', 2);
-------------------------- Property TO enable 3DS2.0 FOR a client id -------------------------------------