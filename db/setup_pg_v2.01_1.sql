
-- INSERT MESB URL for Taxa Client.
-- Note: Check taxa clientid on deploying environment
-- Note: Check that SSL proxy is working on deploying environment
-- MUST BE HTTPS (TLS/SSL)
INSERT INTO Client.URL_Tbl
  (clientid, urltypeid, url)
VALUES
  (10028, 4, 'https://taxa.mesb.cellpointmobile.com');

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) VALUES (10028, 11, 'APPDK8535306001');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100043, 11, '-1');
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10028, 17, 11);
