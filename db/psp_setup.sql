INSERT INTO System.PSP_Tbl (name) VALUES ('Authorize.Net');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 101, 'SEK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 200, 'USD' FROM System.PSP_Tbl;