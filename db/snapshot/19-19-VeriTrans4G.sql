/* ========== Global Configuration for VeriTrans4G = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (59, 'VeriTrans4G',2);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,1,'JPY');
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (392,59,'JPY');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 59);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 59);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 59);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 59, 'VeriTrans4G', <VeriTrans4G_merchatid>, <VeriTrans4G_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 59, 'VeriTrans4G', 'A100000000000001069713cc', '02ed5298dc4efe31f4a10d651dbd93a5d16145325ff21b7edc182819a1a717e4');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 59, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 59, '-1');


INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,8,true,59,616,1,1);


-- Route VISA Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,8,true,59,616,1,1);
-- Route Master Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,7,true,59,616,1,1);
-- Route AMEX Card to VeriTrans4G with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,1,true,59,616,1,1);


/* ========== Global Configuration for VeriTrans4G = ENDS ========== */
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES (<KeyName>, <Value>, <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_JPO', '10', <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_TXN_VERSION', '2.0.0', <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_DUMMY_REQUEST', '1', <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_SERVICE_OPTION_TYPE', 'mpi-complete', <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_BROWSER_DEVICE_CATEGORY', '0', <ClientID>, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('CLIENT_3DS_ENABLE', 'true', <ClientID>, 'client', 2 );




INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_JPO', '10', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_TXN_VERSION', '2.0.0', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_DUMMY_REQUEST', '1', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_SERVICE_OPTION_TYPE', 'mpi-complete', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('VeriTrans4G_BROWSER_DEVICE_CATEGORY', '0', 10018, 'client', 2 );
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope ) VALUES ('CLIENT_3DS_ENABLE', 'true', 10018, 'client', 2 );


