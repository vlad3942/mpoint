/* ========== Global Configuration for SWISH = STARTS ========== */

--For country Swden - 101
--For currency 752 swdesh krona
INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (92, 'SWISH', null, true, 23, -1, -1, -1, 4);

/* ========== Global Common Configuration for SWISH = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (66, 'SWISH',4);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (752,66,'SEK');


INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-752, 92, true);

/*
* SWISH card with SWISH APM 
*/

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (92, 66);

/* ========== Global Common Configuration for SWISH = STARTS ========== */

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 66, 'SWISH', <Swish_merchatid>, <DragonPay_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 66, 'SWISH', '9870671949', 'test');

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (<ClientID>, <countryid>, <CurrencyID>,true)
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10018,101,752,true)


INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 66, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 66, '-1');

-- Route SWISH APM with country Swden
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,88,true,61,<countryid>,1,1);
-- Route SWISH APM with country Swden
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,92,true,66,101,1,4);



-------71D0C697FA57967E8ADCF2E42F7226E3

