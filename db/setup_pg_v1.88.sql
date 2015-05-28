
INSERT INTO System.PSP_Tbl (id, name) VALUES (11, 'MobilePay');
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (17, 'MobilePay', 15, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), Max(C.id) FROM System.PSP_Tbl PSP, System.Card_Tbl C;
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, Max(Card.id) FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT 10007, Max(CA.cardid), Max(CA.pspid) FROM System.PSPCard_Tbl CA;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username) SELECT 10007, Max(id), 'APPDK0000000000', 'APPDK0000000000' FROM System.PSP_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT A.id, Max(P.id), '-1'  FROM Client.Account_Tbl A, System.PSP_Tbl P WHERE clientid = 10007 GROUP BY A.id;

INSERT INTO System.URLType_Tbl (id, name) VALUES (4, 'Mobile Enterprise Servicebus');
INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10005, 4, 'http://dsb.mesb.cellpointmobile.com/');

ALTER TABLE client.client_tbl
  ADD COLUMN transaction_ttl integer DEFAULT 0;
  COMMENT ON COLUMN client.client_tbl.transaction_ttl
  IS 'Transaction Time To Live in seconds';

UPDATE client.client_tbl SET transaction_ttl = 86400 WHERE id = 10028; -- TAXA

INSERT INTO Client.InfoType_Tbl (id, name, note) VALUES (1, 'PSP Message', 'A message which is shown during payment through a specific Payment Service Provider');
-- DSB Production
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10005, 11, 'da', 'Afvent at returnere til DSB app');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10005, 11, 'gb', 'Afvent at returnere til DSB app');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10005, 11, 'us', 'Afvent at returnere til DSB app');
-- DSB Test / Pre-Prod
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10014, 11, 'da', 'Afvent at returnere til DSB app');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10014, 11, 'gb', 'Afvent at returnere til DSB app');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10014, 11, 'us', 'Afvent at returnere til DSB app');
-- Mobile Travel Card
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10019, 11, 'da', 'Afvent at returnere til Mobilperiodekort');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10019, 11, 'gb', 'Afvent at returnere til Mobilperiodekort');
INSERT INTO Client.Info_Tbl (infotypeid, clientid, pspid, language, text) VALUES (1, 10019, 11, 'us', 'Afvent at returnere til Mobilperiodekort');