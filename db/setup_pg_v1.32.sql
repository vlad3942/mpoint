INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2009, 'Ticket Created', 'Callback', '');

INSERT INTO System.PSP_Tbl (name) VALUES ('WorldPay');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 101, 'SEK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 200, 'USD' FROM System.PSP_Tbl;

INSERT INTO System.PSP_Tbl (name) VALUES ('PayEx');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 101, 'SEK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 103, 'GBP' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 200, 'USD' FROM System.PSP_Tbl;

-- Configure Client
UPDATE CLient.CardAccess_tbl SET pspid = 4 WHERE clientid = 10002 AND pspid = 2;
UPDATE CLient.MerchantAccount_tbl SET pspid = 4, name = 'CELLPOINT' WHERE clientid = 10002 AND pspid = 2;  