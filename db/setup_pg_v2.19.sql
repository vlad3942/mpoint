-- Hpp flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('isAutoRedirect', 'true', <clientid>, 'client', true, 2);

/* Update callback url for UATP client Ref Jira : CMP-3000
 * Replace host url as per env while running the query
 * DevPune - http://mpoint.dev2.cellpointmobile.com/uatp/callback.php
 * SITLondon - http://mpoint.sit.cellpointmobile.com/uatp/callback.php
 * SITPune - http://mpoint.sit2.cellpointmobile.com/uatp/callback.php
 * UATAWS - http://mpoint.uat-01.cellpointmobile.net/uatp/callback.php
 * PROD - http://mpoint.cellpointmobile.net/uatp/callback.php 
*/
update client.client_tbl set callbackurl = 'http://mpoint.sit2.cellpointmobile.com/uatp/callback.php' where id = 10069;

--ACQ Level Auto-Capture
ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture DROP DEFAULT;
ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture TYPE int2 USING CASE WHEN auto_capture=TRUE THEN 1 ELSE 0 END;
ALTER TABLE log.transaction_tbl ALTER COLUMN auto_capture SET DEFAULT 0;

ALTER TABLE system.psp_tbl ADD auto_capture boolean NOT NULL DEFAULT FALSE;

--End of ACQ Level Auto-Capture


/* ========== Global Configuration for POLi - Card========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (34, 'POLi', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (34, 0, 0);

-- CardPricing_Tbl for Australia
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (34, -36);

-- CardPricing_Tbl for New Zealand
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) VALUES (34, -554);
/* ========== Global Configuration for POLi - Card ========== */

/* ========== Global Configuration for POLi = PSP ========== */
INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (32, 'POLi', 4);
-- CardPricing_Tbl for Australia
INSERT INTO System.PSPCurrency_Tbl (pspid, name, currencyid) VALUES (32,'AUD', 36);
-- CardPricing_Tbl for New Zealand
INSERT INTO System.PSPCurrency_Tbl (pspid, name, currencyid) VALUES (32,'NZD', 554);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (34, 32);
/* ========== Global Configuration for POLi = PSP ========== */

/* ========== Global Configuration for POLi  Merchant = STARTS ========== */
-- MerchantAccount_Tbl for Australia default entry
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 32, 'POLi', '6101816', 'MdXqHAM!Y2EWQvVC4WsT');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 32, 'POLi');
/* ========== Global Configuration for POLi  Merchant = STARTS ========== */

-- Route POLi Card to POLi with country Australia
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (<clientid>, 34, 32, true, 500);
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-36, -1, true, 36);

-- Route POLi Card to POLi with country New Zealand
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (<clientid>, 34, 32, true, 513);
INSERT INTO system.pricepoint_tbl (id, amount, enabled, currencyid) VALUES (-554, -1, true, 554);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('mechantaccountrule',
'username ::= (property[@name=''<midpath>''])
midpath ::= "MID."(@country-id)
password ::= (property[@name=''<authpath>''])
authpath ::= "AUTHCODE."(@country-id)', true, (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <clientid> and pspid = 32), 'merchant', 0);