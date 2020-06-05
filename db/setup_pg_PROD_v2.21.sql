/* Stored Card Route for stored card 10018*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT  clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36 FROM client.cardaccess_tbl where clientid = 10018 and enabled = true and cardid in (8,7,1,5);

/* Stored Card Route for stored card 10021*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT id, clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36
FROM client.cardaccess_tbl where clientid = 10021 and enabled = true and cardid in (8,7,1,5);

/* Stored Card Route for stored card 10062*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)
SELECT  clientid, cardid, created, modified, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,36 FROM client.cardaccess_tbl where clientid = 10062 and enabled = true and cardid in (8,7,1,5,22,3);

/* Wallet Based Routing scripts*/
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES(10074, 7, 21, 405, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES(10074, 8, 21, 405, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES( 10081, 7, 21, 429, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid)VALUES( 10081, 8, 21, 429, 1, NULL, false, 1, 0, 0, 3,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,8,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,7,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,1,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10069,22,52, 200, 1, NULL, false, 1, 0, 6, 1,14);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10099, 8,18, 200, 1, NULL, false, 1, 0, 0, 2,44);
INSERT INTO client.cardaccess_tbl (clientid, cardid,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type,walletid) VALUES(10099, 7,18, 200, 1, NULL, false, 1, 0, 0, 2,44);

UPDATE client.cardaccess_tbl SET  psp_type=3 WHERE id=1513 and clientid=10069;









