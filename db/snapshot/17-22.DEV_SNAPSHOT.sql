
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2004, 'Payment approved for partial amount', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2005, '3d verification required for Authorization', 'Payment', '');

/*=========================PayTabs===================================== */

INSERT INTO System.PSP_Tbl (id, name) VALUES (38, 'PayTabs');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (602,38,'AED');
INSERT INTO System.PspCard_Tbl(cardid, pspid) VALUES (31, 38);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 38, 'PayTabs', 'Arun123', 'zoVCrg1wOzCN22cXIZt5YM3TnAKoA5paulNWBOtqo6eq8roRqSWoEZh1A2qb7PlCa9yMX2cm8qMgSb7i34HH3ZID19P9YaL9jkVh');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 38, '-1');
UPDATE Client.CardAccess_Tbl SET pspid = 38, countryid = 602 WHERE clientid = 10007 AND cardid = 31;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('MERCHANT_URL', 'test_sadad@paytabs.com', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 38), 'merchant');

/*=========================PayTabs===================================== */

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




/* Maybank configuration in additional configuration for Malindo (sandbox/production has same set of mid and password)  */

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8', '02700770202075001284',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7', '02700770202075001284',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.1', '02701700290875100472',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.8', '4GkR2Hkk',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.7', '4GkR2Hkk',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'pwd.1', '6sjhPN9X',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 27),'merchant');

/* Maybank configuration in additional configuration for merchant:production  */



/*
--Public bank additional config for MID based on payment method and currency
 */

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.MYR', '5500003631',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.HKD', '5500003798',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.SGD', '5500003658',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.AUD', '5500003771',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.LKR', '5500003895',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.CNY', '5500003909',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.THB', '5500003887',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.TWD', '5500004077',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.SAR', '5500004492',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.USD', '5500003666',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.7.IDR', '5500004239',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
---
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.MYR', '3300004667',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.HKD', '3300004802',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.SGD', '3300004675',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.AUD', '3300004799',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.LKR', '3300004918',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.CNY', '3300004942',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.THB', '3300004896',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.TWD', '3300005116',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.SAR', '3300005574',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.USD', '3300004683',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'mid.8.IDR', '3300005302',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 28) ,'merchant');
/*
End of Public Bank additional configuration
 */

/*
--CCAvenue additional config for Working and access key
--Access key is same for all envs
 */
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'ccavenue.access.key', 'AVBV69EB32BP61VBPB',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25),'merchant');
/*--working key for all envs except PROD*/
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'ccavenue.working.key', '93C3C30ED0AF63F7D222ACB2A53DB025',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25),'merchant');
/*--PROD working key-*/
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'ccavenue.working.key', 'F9D8D501AB87FC404EAC9C5CA682C1D9',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 25),'merchant');

/*
End of CCAvenue additional config
 */

/*
--WireCard additional config for enrollment mid key - same for all environments
 */

INSERT INTO client.additionalproperty_tbl( key, value, externalid, type) VALUES ( 'enrollment.mid.key', '33f6d473-3036-4ca5-acb5-8c64dac862d1',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 18),'merchant');

/*
End of WireCard additional config
 */