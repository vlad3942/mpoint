UPDATE Client.Client_Tbl SET terms = 'Aftalevilkår og oplysningspligt
Du vil automatisk modtage en ordrebekræftelse med oplysninger om din ordre samt om retur- og reklamationsret. Du kan altid finde de gældende købsbetingelser på denne side. Du kan altid genfinde dine ordrer under "min e-boghandel".

Priser
Alle priser ved levering i Danmark er gældende udsalgspriser inkl. 25% moms. Ved levering til adresser uden for EU fratrækkes momsen automatisk. Det er forlagene i Danmark, der fastsætter, om en given bog skal have en fast pris. Har forlaget besluttet en fast pris, er det for os som boghandel ikke tilladt at sælge til en anden pris. DER TAGES FORBEHOLD FOR TRYKFEJL OG PRISÆNDRINGER, fx. at et forlag vælger at sætte en anden pris på udgivelsesdagen end oprindeligt planlagt.
Rabatter: Vi yder rabatter til alle kunder i form af diverse kampagner og i forbindelse med konceptet "Altid Billige Bestsellers". Herudover yder vi rabatter til virksomheder og institutioner, såfremt disse har et stort indkøb.
Organiseret rabat: Vi yder p.t. rabat til medlemmer af følgende foreninger: FDM, Forbrugsforeningen og LIC.  Endvidere yder vi rabat til ansatte i virksomheder, som handler under en firmaaftale med Logbuy.

Leveringstid
Leveringstiden fremgår af den enkelte vare (normalt max. 1-8 hverdage). For engelske bøger vil leveringstiden normalt være 4-8 hverdage. Er leveringstiden længere, vil dette fremgå af din ordre/ordrebekræftelse. OBS: Ordren sendes samlet, når den er komplet. Hvis du bestiller bøger med ukendt leveringstid eller bøger, der ikke er udkommet, anbefaler vi, at du bestiller disse bøger separat.

Hvis du ønsker selv at afhente din bestilling, får du en e-mail, når din bestilling ligger klar til afhentning. Hvis du har ønsket at få din ordre tilsendt, vil du modtage en e-mail, når den afsendes fra os.

Bestillinger til udlandet vil ligeledes blive afsendt inden for 1-8 hverdage (engelske bøger som nævnt noget senere), med mindre andet er oplyst af ordren/ordrebekræftelsen. Vi er dog ikke herre over de lokale udbringningsforhold, så derfor kan vi ikke love en bestemt leveringstid ved udlandslevering.

Returret
SAXO.COM YDER 30 DAGES FULD RETURRET.
HVIS DU RETURNERER, SKAL VARERNE VÆRE I VÆSENTLIG SAMME STAND SOM VED MODTAGELSEN OG GERNE LEDSAGET AF EN KOPI AF FAKTURAEN.
DU KAN FORTRYDE ET KØB VED
1) AT NÆGTE AT MODTAGE VAREN, VED
2) AT OVERDRAGE VAREN TIL POSTVÆSENET ELLER VED
3) AT RETURNERE VAREN TIL DEN ANGIVNE ADRESSE.
FRAGTUDGIFTEN VED AT RETURNERE VAREN TIL SAXO.COM, PÅHVILER DIG SOM FORBRUGER. SÅFREMT DET RETURNEREDE OPFYLDER KRAVENE,  VIL DU INDEN 14 DAGE MODTAGE EN ANVISNING PÅ DET BETALTE BELØB.

Reklamationsret
DU HAR 2 ÅRS REKLAMATIONSRET. SÅFREMT DU MODTAGER EN FORKERT VARE I FORHOLD TIL DIN BESTILLING ELLER EN BESKADIGET VARE, SKAL DU REKLAMERE INDEN EN RIMELIG TID, EFTER DU HAR KONSTATERET MANGLEN. REKLAMERER DU INDEN 2 MÅNEDER, ANSES REKLAMATIONEN FOR VÆRENDE RETTIDIG. REKLAMATIONER KAN MEDDELES PÅ TELEFON 38150510 ELLER PER E-MAIL: INFO@SAXO.COM. DU KAN UDNYTTE DIN REKLAMATIONSRET VED AT SENDE VAREN RETUR ELLER MØDE OP PÅ VORES FYSISKE ADRESSE OG REKLAMERE DER. VI BETALER DIN UDGIFT TIL FORSENDELSE, HVIS VI HAR LEVERET EN FORKERT ELLER BESKADIGET VARE. VI FORBEHOLDER OS RET TIL AT AFHJÆLPE EN MANGEL/ERSTATTE MED EN UBESKADIGET VARE FREMFOR AT BETALE KØBESUMMEN TILBAGE.' WHERE id = 10001;

INSERT INTO Client.Client_Tbl (countryid, flowid, name, username, passwd, maxamount, lang, logourl, cssurl, callbackurl, accepturl, cancelurl, terms, mode) VALUES (200, 1, 'PBS Kids Test', 'CPMDemo', 'DEMOisNO_2', 1000000, 'gb', '', 'http://pbskids.mretail.demo.cellpointmobile.com/css/pbskids_mpoint.css', 'http://pbskids.mretail.demo.cellpointmobile.com/mOrder/sys/mpoint.php', '', '', 'PBS Kids Test Terms & Conditions', 1);
INSERT INTO Client.Account_Tbl (clientid, name, mobile) SELECT Max(id), 'Default', '' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT Max(Cl.id), PC.cardid, PC.pspid FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid > 0 AND PC.pspid IN (1, 6) GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'CPT', true FROM Client.Client_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 1, 'CPMDemo' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 1, '-1'  FROM Client.Account_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 6, 'ID ### KEY' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 6, '-1'  FROM Client.Account_Tbl;

INSERT INTO Client.Shop_Tbl (clientid, keywordid, shipping, ship_cost, free_ship, del_date) SELECT Max(CL.id), 5, 'Post Danmark', 2900, -1, true FROM Client.Client_Tbl Cl;
INSERT INTO Client.Shipping_Tbl (shippingid, shopid, cost, free_ship) SELECT 1, Max(id), 2900, -1 FROM Client.Shop_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'Cash (Jens Findus)', 1, 19995, 'http://demo.ois-inc.com/mpoint/prod/cash.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'NYC (Kristina Korsholm)', 1, 9950, 'http://demo.ois-inc.com/mpoint/prod/nyc.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'De otte (Katherine Neville)', 1, 19995, 'http://demo.ois-inc.com/mpoint/prod/de_otte.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'Narnia Fortællingerne (CS Lewis)', 1, 44900, 'http://demo.ois-inc.com/mpoint/prod/narnia.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'By af Jazz (Christian Munch-Hansen)', 1, 20000, 'http://demo.ois-inc.com/mpoint/prod/by_af_jazz.jpg');

INSERT INTO Client.SurePay_Tbl (clientid, resend, notify, email) SELECT Max(CL.id), 2, 10, 'support@oismail.com' FROM Client.Client_Tbl CL;



UPDATE Client.Client_tbl SET name = 'Panorama Bio', username = 'CPMDemo', passwd = 'DEMOisNO_2', logourl = '', cssurl = 'http://panorama.mretail.cellpointmobile.com/css/integra_mpoint.css', callbackurl = 'http://panorama.mretail.cellpointmobile.com/mOrder/sys/mpoint.php', cancelurl = '', smsrcpt = false, emailrcpt = false, auto_capture = false, send_pspid = true, store_card = 3, iconurl = 'http://panorama.mretail.cellpointmobile.com/img/mpoint_mycards.gif', mode = 0 WHERE id = 10009;
UPDATE Client.Account_Tbl SET clientid = 10009, name = 'iPhone', mobile = NULL WHERE id = 100009;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10008, pspid = 1, name = 'CPMDemo' WHERE id = 10;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10009, pspid = 7, name = '201110312771', username = 'WF86795', passwd = 'qbreW@66' WHERE id = 11;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10009, pspid = 1, name = 'CPMDemo' WHERE id = 12;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100008, pspid = 1 WHERE id = 11;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100009, pspid = 7 WHERE id = 12;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100009, pspid = 1 WHERE id = 13;
UPDATE Client.CardAccess_Tbl SET pspid = 7 WHERE clientid = 10009 AND pspid = 2;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10009, 11, 1);
UPDATE Client.Keyword_tbl SET clientid = 10009, name = 'CPM' WHERE id = 10;

UPDATE System.PSPCurrency_Tbl SET name = 'GBP' WHERE countryid = 103 AND pspid = 4;
UPDATE Client.Client_tbl SET countryid = 103, name = 'Pizza Hut', username = 'CPMDemo', passwd = 'DEMOisNO_2', logourl = '', cssurl = 'http://stage.bemoko.com/pizzahut/css/cellpoint.css', callbackurl = 'http://stage.bemoko.com/pizzahut/cellpoint', accepturl = 'http://stage.bemoko.com/pizzahut/orders/confirmation', cancelurl = '', smsrcpt = false, emailrcpt = false, auto_capture = false, send_pspid = false, store_card = 0, iconurl = 'http://panorama.mretail.cellpointmobile.com/img/myaccount.gif' WHERE id = 10010;
UPDATE Client.Account_Tbl SET clientid = 10010, name = 'Mobile Web', mobile = NULL WHERE id = 100010;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10010, pspid = 4, name = 'PIZZAHUTECOMMT', username = 'PIZZAHUTECOMMT', passwd = '3Pjge5RTT1' WHERE id = 13;
--UPDATE Client.MerchantAccount_Tbl SET clientid = 10010, pspid = 1, name = 'CPMDemo' WHERE id = 12;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100010, pspid = 4 WHERE id = 14;
--UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100010, pspid = 1 WHERE id = 13;
UPDATE Client.CardAccess_Tbl SET pspid = 4 WHERE clientid = 10010 AND pspid = 2;
DELETE FROM Client.CardAccess_Tbl WHERE clientid = 10010 AND cardid = 2;
--INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10009, 11, 1);
UPDATE Client.Keyword_tbl SET clientid = 10010, name = 'CPM' WHERE id = 11;


UPDATE Client.Client_tbl SET name = 'Roenne Bio', username = 'CPMDemo', passwd = 'DEMOisNO_2', logourl = '', cssurl = 'http://roenne-bio.mretail.cellpointmobile.com/css/integra_mpoint.css', callbackurl = 'http://roenne-bio.mretail.cellpointmobile.com/mOrder/sys/mpoint.php', cancelurl = '', smsrcpt = false, emailrcpt = false, auto_capture = false, send_pspid = true, store_card = 3, iconurl = 'http://roenne-bio.mretail.cellpointmobile.com/img/mpoint_mycards.gif', mode = 0 WHERE id = 10012;
UPDATE Client.Account_Tbl SET clientid = 10012, name = 'iPhone', mobile = NULL WHERE id = 100012;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10012, pspid = 1, name = 'CPMDemo' WHERE id = 7;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10012, pspid = 7, name = '2006082577772', username = 'ronnebio.dk', passwd = '4z7e3xo8!' WHERE id = 8;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100012, pspid = 1 WHERE id = 7;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100012, pspid = 7 WHERE id = 8;
--UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100012, pspid = 1 WHERE id = 13;
UPDATE Client.CardAccess_Tbl SET pspid = 7 WHERE clientid = 10012 AND pspid = 2;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10012, 11, 1);
UPDATE Client.Keyword_tbl SET clientid = 10012, name = 'CPM' WHERE id = 6;

UPDATE Client.Client_Tbl SET name = 'Pizza Hut Corp', lang = 'gb' WHERE id = 10010;
UPDATE Client.Client_Tbl SET name = 'Pizza Hut Francise', mode = 0, countryid = 103, logourl = NULL, cssurl = 'http://stage.bemoko.com/pizzahut/css/cellpoint.css', callbackurl = 'http://stage.bemoko.com/pizzahut/cellpoint', accepturl = 'http://stage.bemoko.com/pizzahut/orders/confirmation', lang = 'gb', smsrcpt = false, emailrcpt = false, send_pspid = false, iconurl = 'http://panorama.mretail.cellpointmobile.com/img/myaccount.gif' WHERE id = 10011;
UPDATE Client.Account_Tbl SET clientid = 10011, name = 'Mobile Web' WHERE id = 100011;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10011, pspid = 4, name = 'PIZZAHUTECOMMBK', username = 'PIZZAHUTECOMMBK', passwd = '3Pjge5RTT1' WHERE id = 14;
UPDATE Client.MerchantSubAccount_Tbl SET name = '282897', pspid = 4, accountid = 100011 WHERE id = 29;
UPDATE Client.Keyword_Tbl SET name = 'BIOAPP' WHERE id IN (10, 6);
UPDATE Client.Keyword_tbl SET name = 'CPM', clientid = 10011 WHERE id = 11;
UPDATE Client.CardAccess_Tbl SET pspid = 4 WHERE clientid = 10011 AND cardid IN (6, 7, 8);
DELETE FROM Client.CardAccess_Tbl WHERE clientid = 10011 AND pspid != 4;


UPDATE Client.Client_tbl SET name = 'Panorama Fredericia', username = 'CPMDemo', passwd = 'DEMOisNO_2', logourl = '', cssurl = 'http://panorama-fredericia.mretail.cellpointmobile.com/css/integra_mpoint.css', callbackurl = 'http://panorama-fredericia.mretail.cellpointmobile.com/mOrder/sys/mpoint.php', cancelurl = '', smsrcpt = false, emailrcpt = false, auto_capture = false, send_pspid = true, store_card = 3, iconurl = 'http://panorama-fredericia.mretail.cellpointmobile.com/img/mpoint_mycards.gif', mode = 0 WHERE id = 10008;
UPDATE Client.Account_Tbl SET clientid = 10008, name = 'iPhone', mobile = NULL, markup = 'xhtml' WHERE id = 100018;
--UPDATE Client.MerchantAccount_Tbl SET clientid = 10008, pspid = 1, name = 'CPMDemo' WHERE id = 29;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10008, pspid = 7, name = '201109202747', username = 'WF86429', passwd = 'gjmzG%81!' WHERE id = 9;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100018, pspid = 1 WHERE id = 31;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100018, pspid = 7 WHERE id = 32;
--UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100012, pspid = 1 WHERE id = 13;
UPDATE Client.CardAccess_Tbl SET pspid = 7 WHERE clientid = 10008 AND pspid = 2;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10008, 11, 1);
UPDATE Client.Keyword_tbl SET clientid = 10008, name = 'CPM' WHERE id = 12;

UPDATE Client.Client_tbl SET name = 'Panorama Middelfart', username = 'CPMDemo', passwd = 'DEMOisNO_2', logourl = '', cssurl = 'http://panorama-middelfart.mretail.cellpointmobile.com/css/integra_mpoint.css', callbackurl = 'http://panorama-middelfart.mretail.cellpointmobile.com/mOrder/sys/mpoint.php', cancelurl = '', smsrcpt = false, emailrcpt = false, auto_capture = false, send_pspid = true, store_card = 3, iconurl = 'http://panorama-middelfart.mretail.cellpointmobile.com/img/mpoint_mycards.gif', mode = 0 WHERE id = 10013;
UPDATE Client.Account_Tbl SET clientid = 10013, name = 'iPhone', mobile = NULL, markup = 'xhtml' WHERE id = 100019;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10013, pspid = 1, name = 'CPMDemo' WHERE id = 29;
UPDATE Client.MerchantAccount_Tbl SET clientid = 10013, pspid = 7, name = '200704161546', username = 'WF73369', passwd = '79qah27e' WHERE id = 30;
--UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100019, pspid = 1 WHERE id = 33;
UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100019, pspid = 7 WHERE id = 34;
--UPDATE Client.MerchantSubAccount_Tbl SET accountid = 100012, pspid = 1 WHERE id = 13;
UPDATE Client.CardAccess_Tbl SET pspid = 7 WHERE clientid = 10013 AND pspid = 2;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10013, 11, 1);
UPDATE Client.Keyword_tbl SET clientid = 10013, name = 'CPM' WHERE id = 19;