------ SEPG Below is sample to display how to set up client-account, apart form client,accoutn cocmbination, rest of the set up will be standard

-- Create a new client for SEPG-Malindo (Malindo is example could be any other)
INSERT INTO Client.Client_Tbl (id,countryid, flowid, name, username, passwd, lang, callbackurl, mode, store_card, auto_capture, smsrcpt, emailrcpt, maxamount) VALUES (10040,200, 1, 'SEPG Client', 'MalindoDemo', 'DEMOisNO_2', 'da', 'http://od.mretail.dev2.cellpointmobile.com/mOrder/sys/mpoint.php', 1, 3, false, false, false, 1000000);
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) VALUES ( 10040, 'CPM', true ) ;
INSERT INTO Client.url_tbl(clientid, urltypeid, url, enabled) VALUES ( 10040, 4, 'http://od.mesb.dev2.cellpointmobile.com:10080',true );


-- channelid AGY-1,ATO-2,CTO-3,GSA-4,IVR-5,MOB-6,OTA-7,RES-8,WEB-9
-- Create account - accountid should follow pattern of {clientid}{channelid}
INSERT INTO Client.Account_Tbl (id, clientid, name, markup) VALUES( 100406, 10040, 'SEPG - Channel MOB', 'app'  );


-- Set up static Routes for the clientid,accountid (Standard Static Route set up) - sample below for Wirecard-USA-VISA

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10040, 18, '9105bb4f-ae68-4768-9c3b-3eda968f57ea', '70000-APILUHN-CARD', '8mhwavKVb91T');
INSERT INTO Client.Additionalproperty_tbl( key, value, externalid, type) VALUES ( 'check.enrollment.mid', '33f6d473-3036-4ca5-acb5-8c64dac862d1',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10040 and pspid = 18),'merchant');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100406, 18, '-1');
INSERT INTO Client.CardAccess_Tbl (clientid, countryid , cardid, pspid) VALUES (10040,200, 8, 18 );
 
------ SEPG  sample ends ----------------------

 