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


--- If any client using the cybersource api then as per cybersource documentation compulsary businesstype is 2(airline) for airline transaction
update client.account_tbl set businesstype = <businesstype> where clientid = <clientid>
update client.account_tbl set businesstype = 2 where clientid = 10020;

--Granular status codes
UPDATE log.state_tbl
SET name = 'The amount is invalid.', module = 'sub-code', func = ''
    WHERE id = 2010101;

UPDATE log.state_tbl
SET name = 'Invalid Access Credentials', module = 'sub-code', func = ''
WHERE id = 2010201;

UPDATE log.state_tbl
SET name = 'Internal error / general system error', module = 'sub-code', func = ''
WHERE id = 2010301;

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010102, 'Card Number is invalid.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010103, 'Installment field value is invalid', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010104, 'Invalid Order Number value', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010105, 'Missing Mandatory Fields / Data not present / invalid data field (general error code when any field is invalid)', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010106, 'Invalid MerchantID', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010107, 'Invalid TransactionID', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010108, 'Invalid Transaction date', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010109, 'Invalid CVC', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010110, 'Invalid Payment Type', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010112, 'Invalid Expiry Date', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010113, 'Invalid 3DS Secure values', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010114, 'Invalid Card type', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010115, 'Invalid Request version', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010116, 'Return URL is not set.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010117, 'Invalid currency code.', 'sub-code', '');


INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010202, 'Invalid PIN OR OTP', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010203, 'Insufficient funds / over credit limit', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010204, 'Expired Card', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010205, 'Unable to authorize', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010206, 'Exceeds withdrawal count limit OR Authentication requested', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010207, 'Do Not Honor', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010208, 'Transaction not permitted to user', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010209, 'Transaction Aborted by user / Card Holder Abandoned 3DS/Wallet', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010210, 'User Inactive or Session Expired', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010211, 'Only a partial amount was approved', 'sub-code', '');


INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010302, 'Parse error / invalid Request', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010303, 'Service not available.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010304, 'Time out', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010305, 'Payment is cancelled / Payment reversed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010306, 'Waiting for upstream response', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010307, 'No Routing Available', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010308, 'System DB Error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010309, 'Invalid Operation / Operation Rejected', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010310, 'Transaction already in progress /  Duplicate Transaction / Duplicate Order Number', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010311, 'Endpoint not supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010312, 'Transaction not permitted to terminal', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010313, 'Invalid merchant account / configuration / API permission missing', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010314, 'Transaction rejected by Issuer / Authorization failed /Transaction Failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010315, 'EMI not available', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010316, 'Void not supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010317, 'Already Captured', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010318, 'Retry limit exceeded', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010319, 'Invalid Capture attempted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010320, 'Transaction Not Posted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010321, 'Recurring Payment Not Supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010322, 'Stored card option is disabled.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010323, 'Request Authentication Failed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010324, 'Unable to decrypt request.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010325, 'Transaction ID / EP Generation Failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010326, 'Installment Payment is disabled.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010327, 'Ticket issue failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010328, 'China Union Pay sign failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010329, 'Card type is not allowed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010330, 'Issuing bank unavailable.	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010331, 'Transaction exceeds the approved limit	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010332, 'Cannot void as capture or credit is submitted	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010333, 'Cannot Refund / You requested a credit for a capture that was previously voided.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010334, 'Credit amount exceeds maximum allowed for your Merchant account.', 'sub-code', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010401, 'FRAUD Suspicion / Rejected', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010402, 'Address verification failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010403, 'Card Acceptor should contact accquirer', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010404, 'Security Voilation', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010405, 'Card is Blocked due to fraud', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010406, '3D Secure authentication failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010407, 'Fraud Stolen Card', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010408, 'Compliance ERROR', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010409, 'Transaction Previously declined', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010410, 'E-commerce declined', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010411, 'Card restricted', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010412, 'Card Function Not Supported', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010413, 'Physical Card Error', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010414, 'BIN check failed', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010415, 'Validation Check Failed.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010416, 'CVN did not match	', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010417, 'The customer matched an entry on the processor’s negative file.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010418, 'Strong customer authentication (SCA) is required for this transaction.', 'sub-code', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(2010419, 'authorization request was approved by the issuing bank but declined by Gateway/processor', 'sub-code', '');
--end of granular status codes