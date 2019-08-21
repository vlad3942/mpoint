--//********MADA-MPGS*******************//

--//**********system.card_tbl************//
--If Entry for MADA card already exists
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (71, 'MADA', null, true, 23, 16, 16, 3, 4);
--else
update  system.card_tbl set minlength = 16, maxlength = 16,cvclength = 3 where id = 71;

--//**********system.pricepoint_tbl************//
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-682, -1, true, 682);

--//**********system.cardpricing_tbl************//
INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-682, 71, true);


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
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'username.SAR', 'merchant.TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'password.SAR', 'd92028b344ea6d1df4f89d1bc9fa0b78', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'Notification-Secret.SAR', '561da90d33a04f990e1b28d7486db58f', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'mid.SAR', 'TEST603001002', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl ( key, value, enabled, externalid, type, scope) VALUES ( 'HOST', 'ap-gateway.mastercard.com', true, <merchantaccount-ID>, 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mvault', 'true', true, <ClientId>, 'client', 2);


--//**********system.cardprefix_tbl Bin range************//
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,0	,0, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,400861	,400861, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,401757	,401757, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,409201	,409201, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,410685	,410685, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,417633	,417633, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,419593	,419593, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,422817	,422819, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,428331	,428331, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,428671	,428673, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,431361	,431361, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,432328	,432328, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,434107	,434107, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,439954	,439954, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,439956	,439956, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440533	,440533, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440647	,440647, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,440795	,440795, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,445564	,445564, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446393	,446393, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446404	,446404, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,446672	,446672, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,455036	,455036, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,455708	,455708, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,457865	,457865, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,458456	,458456, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,462220	,462220, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,468540	,468543, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,483010	,483012, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,484783	,484783, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,486094	,486096, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,489317	,489319, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,493428	,493428, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,504300	,504300, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,508160	,508160, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,521076	,521076, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,524130	,524130, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,524514	,524514, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,529415	,529415, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,529741	,529741, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,530906	,530906, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,531095	,531095, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,532013	,532013, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,535825	,535825, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,535989	,535989, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,536023	,536023, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,537767	,537767, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,539931	,539931, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,543357	,543357, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,554180	,554180, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,557606	,557606, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,558848	,558848, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,585265	,585265, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,588845	,588851, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,588982	,588983, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,589005	,589005, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,589206	,589206, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,604906	,604906, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,605141	,605141, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,636120	,636120, true);
INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (71,968201	,968211, true);

//********END OF MADA-MPGS*******************//