//********MADA-MPGS*******************//

//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type, capture_method, installment) VALUES (57, 'MADA MPGS', true, 1, 0, 0);

//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 57, true);

INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 57, true);

//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (57, 'SAR', true, 682);

//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 57, 'TEST603001002',  true, 'merchant.TEST603001002', 'd92028b344ea6d1df4f89d1bc9fa0b78', null);

//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, created, modified, enabled) VALUES (<accountid>, 57, '-1', '2016-03-31 08:59:59.941696', '2016-09-19 12:23:07.805804', true);

//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 7, true, 57, 608, 1, null, false, 1, 0);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 8, true, 57, 608, 1, null, false, 1, 0);

//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'username.SAR', 'merchant.TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'password.SAR', 'd92028b344ea6d1df4f89d1bc9fa0b78', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'Notification-Secret.SAR', '561da90d33a04f990e1b28d7486db58f', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'mid.SAR', 'TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'HOST', 'ap-gateway.mastercard.com', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mvault', 'true', true, <ClientId>, 'client', 2);

//********END OF MADA-MPGS*******************//