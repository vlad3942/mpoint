
-- Hpp flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('isAutoRedirect', 'true', <clientid>, 'client', true, 2);



-- Amex - Start of New Route - Jordan  

INSERT INTO client.cardaccess_tbl (clientid,cardid,enabled,pspid,countryid,stateid,position,preferred,psp_type) 
VALUES(<clientid>,1,true,45,617,1,NULL,false,1);

INSERT INTO client.countrycurrency_tbl (clientid, countryid, currencyid, enabled) VALUES (<clientid>, 617, 840, true);

INSERT INTO system.pspcurrency_tbl (pspid, name, currencyid, enabled) VALUES (45, 'USD', 840, true);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, type, externalid, scope) 
SELECT 'AMEX_MERCHANT_NUMBER_840', '<value>', true, 'merchant', id, 2 FROM client.merchantaccount_tbl WHERE pspid=45 AND clientid=<clientid>;
