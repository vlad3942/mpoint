
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, 'Payment approved for partial amount', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2005, '3d verification required for Authorization', 'Payment', '');

<-- 2C2P ALC Database Script-->

INSERT INTO system.psp_tbl (id, name, system_type) VALUES (40, '2c2p-alc', 1);
INSERT INTO system.pspcurrency_tbl (countryid, pspid, name) VALUES (644,40,'THB');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 40, '2c2p-alc', 'CELLPM', 'TG2009');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 40, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 40, 644, 1, null);
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 7, true, 40, 644, 1, null);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -644, 8);
INSERT INTO system.cardpricing_tbl ( pricepointid, cardid) VALUES ( -644, 7);
INSERT INTO system.pspcard_tbl (cardid, pspid) VALUES (8, 40);
INSERT INTO system.pspcard_tbl (cardid, pspid) VALUES (7, 40);

