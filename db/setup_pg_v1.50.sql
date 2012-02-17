INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2002, 'Payment Cancelled', 'Cancel', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2003, 'Payment Refunded', 'Refund', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (177, 'Payment already Refunded for Transaction', 'Refund', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (179, 'Payment in invalid State for Transaction', '', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (141, 'Undefined E-Mail address', 'Validate', 'valEMail');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (142, 'E-Mail address is too short', 'Validate', 'valEMail');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (143, 'E-Mail address is too long', 'Validate', 'valEMail');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (144, 'E-Mail address contains invalid characters', 'Validate', 'valEMail');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (145, 'E-Mail has an invalid form', 'Validate', 'valEMail');

UPDATE Client.MerchantAccount_Tbl SET username = 'dsbmosart', passwd = 'mosart1234' WHERE clientid = 10014 AND pspid = 2;
UPDATE Client.MerchantAccount_Tbl SET username = 'mosart', passwd = 'nb2kv17' WHERE clientid = 10005 AND pspid = 2;

-- DSB Test Client
INSERT INTO Admin.Access_tbl (clientid, userid) VALUES (10014, 4);

INSERT INTO System.PSP_Tbl (name) VALUES ('PayEx');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;

INSERT INTO System.PSP_Tbl (name) VALUES ('Authorize.Net');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT Max(PSP.id), C.id FROM System.PSP_Tbl PSP, System.Card_Tbl C WHERE C.id < 10 GROUP BY C.id;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 100, 'DKK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 101, 'SEK' FROM System.PSP_Tbl;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) SELECT Max(id), 200, 'USD' FROM System.PSP_Tbl;

INSERT INTO Client.Client_Tbl (countryid, flowid, name, username, passwd, maxamount, lang, logourl, cssurl, callbackurl, accepturl, cancelurl, terms, mode) VALUES (200, 1, 'PBS KIDS', 'CPMDemo', 'DEMOisNO_2', 1000000, 'gb', '', 'http://m.shop.pbskids.org/css/pbskids_mpoint.css', 'http://m.shop.pbskids.org/mOrder/sys/mpoint.php', '', '', 'PBS Kids Test Terms & Conditions', 0);
INSERT INTO Client.Account_Tbl (clientid, name, mobile, markup) SELECT Max(id), 'Default', '', 'html5' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT Max(Cl.id), PC.cardid, PC.pspid FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (1, 5, 7, 8, 9) AND PC.pspid IN (1, 6) GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 1, 'CPMDemo' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 1, '-1'  FROM Client.Account_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 6, '2AHxw8y2g ### 9AXK67dgW56sG57q' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 6, '-1'  FROM Client.Account_Tbl;

UPDATE Client.Client_Tbl SET callbackurl = 'http://dsb.mticket.cellpointmobile.com:10080/mticket/dsb/mpoint-callback' WHERE id IN (10005, 10014);