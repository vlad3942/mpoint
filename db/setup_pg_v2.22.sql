/* ========== Global Configuration for DragonPay Offline = STARTS ========== */

INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (88, 'DRAGONPAYOFFLINE', null, true, 23, -1, -1, -1, 4);

/* ========== Global Common Configuration for DragonPay Offline/DragonPay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',1);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');


INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 88, true);

/*
* Dragon pay offline card with Dragon Pay aggregator 
*/

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (88, 61);

/* ========== Global Common Configuration for DragonPay Offline/DragonPay Online = STARTS ========== */

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 61, 'DragonPay', <DragonPay_merchatid>, <DragonPay_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 61, 'DragonPay', 'CPM', '3GJ8LubyWVUMgqY');

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (<ClientID>, <countryid>, <CurrencyID>,true)
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10018,640,608,true)


INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 61, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 61, '-1');

-- Route DragonPay Offline Card to DragonPayAggregator with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,88,true,61,<countryid>,1,1);
-- Route DragonPay Offline  Card to DragonPayAggregator with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,88,true,61,640,1,1);

--set timezone for CEBU
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10077, 'client', 2);


--------- start CEBU Paypal configuration to setup multiple MIDs as per currency

--PHP and all other currencies not configured in additionalproperty_tbl will go the the default merchant account entry for PHP.
-- that is JPY, AED, AUD, IDR, BND, CNY, KRW,THB,TWD and MOP as defined in CEBU-6
-- default merchantaccount_tbl onboarding sql
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 24,'ACCu12.jik2Wb3kzHJgFE1palQEsAHD7wsynoTYqRcOZAX7RzupgW4sQ', 'sb-sahh431638744_api1.business.example.com', '7W56K2VQBRYF8FLX', true, null);

---additional MIDs for SGD, HKD, MYR, USD

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_SGD', 'sb-mohn91867880_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_SGD', 'B9WX2HPY9DPD6284', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_SGD', 'ATG2YLmUZFFk6n5kHVGwCC2A2dsDAEAdhFEakpPDRN5lxmGq3zimTG6A', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_HKD', 'sb-ph1ko1832308_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_HKD', '5QBM4GMSFPV8AHN', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_HKD', 'A78isDTCcuwhNZKyvzOsXXSzxdUPA25dTmhkEQxH7G1T4iQSksAr-SK0', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_MYR', 'sb-ivizq1858258_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_MYR', 'VMXEJAT9DCLCR7LQ', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_MYR', 'AJXtiLishvWwk7Jm0EnHXPmvJv1xAqMsrFcJMX8Exsbl9aQ5NJRKPyem', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_USERNAME_USD', 'sb-43kvng1868465_api1.business.example.com', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_PASSWORD_USD', '37JT6WGJFFUJFRM3', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) select 'PAYPAL_MID_USD', 'Awzp6NMJcMOsM6SlpR13Cez-7vPFAMODxiW5ZT0qx6EbatLFSrMoBKtc', true, id, 'merchant', 2 from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24;

UPDATE client.additionalproperty_tbl SET  value = '5QBM4GMSFPV8AHNK' where key = 'PAYPAL_PASSWORD_HKD' and externalid in (select id from Client.MerchantAccount_Tbl where clientid=10077 and pspid=24);

---end CEBU paypal --

-- Card prefix range for master card --
INSERT INTO "system".cardprefix_tbl (cardid, min, max, enabled) VALUES(7, 222100, 272099, true);

-- CYBS DM for Fraud integration -- CEBU
INSERT INTO client.additionalproperty_tbl (key,value,enabled,externalid,type,scope) SELECT 'DEFAULT_EMAIL_ID','null@cybersource.com','t', id, 'merchant',2 FROM client.merchantaccount_tbl WHERE clientid=10077 AND pspid=64;
--Store Billing Addrs property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, "type","scope" ) VALUES('IS_STORE_BILLING_ADDRS', 'true', <ClientID>, 'client', 0);
INSERT INTO "system".cardprefix_tbl (cardid, min, max, enabled) VALUES(7, 222100, 272099, true);

--- If any client using the cybersource api then as per cybersource documentation compulsary businesstype is 2(airline) for airline transaction
update client.account_tbl set businesstype = <businesstype> where clientid = <clientid>
update client.account_tbl set businesstype = 2 where clientid = 10020;