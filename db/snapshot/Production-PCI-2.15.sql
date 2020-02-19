-- Diasabling Non 3D authentication for JCB MID
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'SUPRESS_3DS_FLOW', 'JPY.5', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=26 AND clientid = 10018;


---  JCB MID Script
update client.additionalproperty_tbl set value= '764764000002012',scope=2 where externalid = 408 and key = 'username.5' 
and value = '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA'

update client.additionalproperty_tbl set value= '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA',scope=2
where externalid = 408 and key = 'MID.5' and value = '764764000002012'

---rollback Scripts :

update client.additionalproperty_tbl set value= '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA' 
where externalid = 408 and key = 'username.5' and value = '764764000002012'

update client.additionalproperty_tbl set value= '764764000002012' 
where externalid = 408 and key = 'MID.5' and value = '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA'


--//********MADA-MPGS*******************//

--//**********system.psp_tbl************//
INSERT INTO system.psp_tbl (id, name, enabled, system_type) VALUES (57, 'MADA MPGS', true, 1);

--//**********system.pspcard_tbl************//
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (71, 57, true);

--//**********system.pspcurrency_tbl************//
INSERT INTO system.pspcurrency_tbl (pspid, name, enabled, currencyid) VALUES (57, 'SAR', true, 682);

--//**********client.merchantaccount_tbl************//
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, enabled, username, passwd, stored_card) VALUES (<clientid>, 57, 'TEST603001002',  true, 'merchant.TEST603001002', 'd92028b344ea6d1df4f89d1bc9fa0b78', null);

--//**********client.merchantsubaccount_tbl************//
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, created, modified, enabled) VALUES (<accountid>, 57, '-1', '2016-03-31 08:59:59.941696', '2016-09-19 12:23:07.805804', true);

--//**********client.cardaccess_tbl************//
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, installment) VALUES ( <clientid>, 71, true, 57, 608, 1, null, false, 1, 0);


--//**********client.additionalproperty_tbl************//
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'username.SAR', <value>, true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'password.SAR', <value>, true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'Notification-Secret.SAR', <value>, true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'mid.SAR', <value>, true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'HOST', <value>, true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mvault', 'true', true, <ClientId>, 'client', 2);




//********END OF MADA-MPGS*******************//
