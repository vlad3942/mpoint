--mPoint DB Script

--Table Name : Client.CardAccess_Tbl

--GCash

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 93, true, 40, 640, 1, NULL, false, 3, 0, 2, NULL, 0, false);


--Continuum

UPDATE client.cardaccess_tbl SET dccenabled=true WHERE clientid=10077 and cardid in (7,8) and psp_type = 1;


--GrabPay

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_type, walletid, capture_method, dccenabled) VALUES(10077, 94, true, 67, 640, 1, NULL, false, 4, 0, 2, NULL, 0, false);

--Table Name : Client.MerchantAccount_Tbl

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 67, 'c636da2c-cd58-46e0-9821-8277e2b9bbde', '14db420a-dd9e-4ffb-aa8d-ed63c73dad3b', 'dOFP-WHyKlePySZ5', true, null);


--Table Name : Client.MerchantSubAccount_Tbl

INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100770, 67, '-1', true);


--Table Name : Client.Additionalproperty_Tbl

INSERT INTO client.additionalproperty_tbl (id, key, value, enabled, externalid, type, scope) VALUES((SELECT MAX(id)+1 FROM client.additionalproperty_tbl),'CLIENT_ID', '61b27ce1ca6d4a09ab9283cc052d7a36', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 67 and clientid = 10077), 'merchant', 1);
INSERT INTO client.additionalproperty_tbl (id, key, value, enabled, externalid, type, scope) VALUES((SELECT MAX(id)+1 FROM client.additionalproperty_tbl),'CLIENT_SECRET', 'YbgUJ7DZf0agz8dY', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 67 and clientid = 10077), 'merchant', 1);

--PayPal - invoice id

DELETE FROM client.additionalproperty_tbl WHERE id= (SELECT id FROM client.additionalproperty_tbl WHERE  "key" like '%invoiceidrule_PAYPAL_CEBU%' and externalid = 10077);

DELETE FROM client.additionalproperty_tbl WHERE id= (SELECT id FROM client.additionalproperty_tbl WHERE  "key" like '%invoiceidrule_CCPP_CEBU%' and externalid = 10077);

(id,"key", value, enabled, externalid, "type", "scope")
VALUES((SELECT MAX(id)+1 From client.additionalproperty_tbl),'invoiceidrule', 'invoiceid ::= (psp-config/@id)=="24"OR(psp-config/@id)=="40"=(transaction.@id)', true, 10077, 'client', 0);

