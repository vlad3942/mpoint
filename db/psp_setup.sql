INSERT INTO System.PSP_Tbl (name) VALUES ('WannaFind');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id IN (4, 6, 9, 10) GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, '208' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 101, '840' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 103, '826' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 200, '752' FROM System.PSP_Tbl;