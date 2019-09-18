/* ========== Global Configuration for VeriTrans4G = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (58, 'VeriTrans4G',2);


INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,1,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,58,'JPY');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 58);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 58);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 58);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 58, 'VeriTrans4G', <VeriTrans4G_merchatid>, <VeriTrans4G_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 58, 'VeriTrans4G', 'A100000000000001069713cc', '02ed5298dc4efe31f4a10d651dbd93a5d16145325ff21b7edc182819a1a717e4');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 58, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 58, '-1');


INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,8,true,58,616,1,1);

-- Route VISA Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,8,true,58,616,1,1);
-- Route Master Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,7,true,58,616,1,1);
-- Route AMEX Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,1,true,58,616,1,1);


/* ========== Global Configuration for VeriTrans4G = ENDS ========== */
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES (<KeyName>, <Value>, <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_JPO', '10', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_TXN_VERSION', '2.0.0', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_DUMMY_REQUEST', '1', 10018, 'client', 2 );

