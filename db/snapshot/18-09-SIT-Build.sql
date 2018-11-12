
/*START: Fixing incorrect values for USD in PSPCurrency_tbl in system schema */

UPDATE system.pspcurrency_tbl sp
SET name = sc.code
FROM system.currency_tbl sc
WHERE sc.id = sp.currencyid;

/*END: Fixing incorrect values for USD in PSPCurrency_tbl in system schema*/


--MPO Merchant PWD Update

UPDATE CLIENT.MERCHANTACCOUNT_TBL SET PASSWD='5J5584' WHERE PSPID=33

--End MPO Merchant PWD Update

-- Malindo Vietnam Static route

INSERT INTO client.cardaccess_tbl
( clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type)
VALUES( 10018, 7, true, 28, 649, 1, NULL, false, 1);
INSERT INTO client.cardaccess_tbl
( clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type)
VALUES( 10018, 8, true, 28, 649, 1, NULL, false, 1);

-- End Malindo Vietnam Static route