UPDATE Client.Client_Tbl SET terms = 'Aftalevilk�r og oplysningspligt
Du vil automatisk modtage en ordrebekr�ftelse med oplysninger om din ordre samt om retur- og reklamationsret. Du kan altid finde de g�ldende k�bsbetingelser p� denne side. Du kan altid genfinde dine ordrer under "min e-boghandel".

Priser
Alle priser ved levering i Danmark er g�ldende udsalgspriser inkl. 25% moms. Ved levering til adresser uden for EU fratr�kkes momsen automatisk. Det er forlagene i Danmark, der fasts�tter, om en given bog skal have en fast pris. Har forlaget besluttet en fast pris, er det for os som boghandel ikke tilladt at s�lge til en anden pris. DER TAGES FORBEHOLD FOR TRYKFEJL OG PRIS�NDRINGER, fx. at et forlag v�lger at s�tte en anden pris p� udgivelsesdagen end oprindeligt planlagt.
Rabatter: Vi yder rabatter til alle kunder i form af diverse kampagner og i forbindelse med konceptet "Altid Billige Bestsellers". Herudover yder vi rabatter til virksomheder og institutioner, s�fremt disse har et stort indk�b.
Organiseret rabat: Vi yder p.t. rabat til medlemmer af f�lgende foreninger: FDM, Forbrugsforeningen og LIC.  Endvidere yder vi rabat til ansatte i virksomheder, som handler under en firmaaftale med Logbuy.

Leveringstid
Leveringstiden fremg�r af den enkelte vare (normalt max. 1-8 hverdage). For engelske b�ger vil leveringstiden normalt v�re 4-8 hverdage. Er leveringstiden l�ngere, vil dette fremg� af din ordre/ordrebekr�ftelse. OBS: Ordren sendes samlet, n�r den er komplet. Hvis du bestiller b�ger med ukendt leveringstid eller b�ger, der ikke er udkommet, anbefaler vi, at du bestiller disse b�ger separat.

Hvis du �nsker selv at afhente din bestilling, f�r du en e-mail, n�r din bestilling ligger klar til afhentning. Hvis du har �nsket at f� din ordre tilsendt, vil du modtage en e-mail, n�r den afsendes fra os.

Bestillinger til udlandet vil ligeledes blive afsendt inden for 1-8 hverdage (engelske b�ger som n�vnt noget senere), med mindre andet er oplyst af ordren/ordrebekr�ftelsen. Vi er dog ikke herre over de lokale udbringningsforhold, s� derfor kan vi ikke love en bestemt leveringstid ved udlandslevering.

Returret
SAXO.COM YDER 30 DAGES FULD RETURRET.
HVIS DU RETURNERER, SKAL VARERNE V�RE I V�SENTLIG SAMME STAND SOM VED MODTAGELSEN OG GERNE LEDSAGET AF EN KOPI AF FAKTURAEN.
DU KAN FORTRYDE ET K�B VED
1) AT N�GTE AT MODTAGE VAREN, VED
2) AT OVERDRAGE VAREN TIL POSTV�SENET ELLER VED
3) AT RETURNERE VAREN TIL DEN ANGIVNE ADRESSE.
FRAGTUDGIFTEN VED AT RETURNERE VAREN TIL SAXO.COM, P�HVILER DIG SOM FORBRUGER. S�FREMT DET RETURNEREDE OPFYLDER KRAVENE,  VIL DU INDEN 14 DAGE MODTAGE EN ANVISNING P� DET BETALTE BEL�B.

Reklamationsret
DU HAR 2 �RS REKLAMATIONSRET. S�FREMT DU MODTAGER EN FORKERT VARE I FORHOLD TIL DIN BESTILLING ELLER EN BESKADIGET VARE, SKAL DU REKLAMERE INDEN EN RIMELIG TID, EFTER DU HAR KONSTATERET MANGLEN. REKLAMERER DU INDEN 2 M�NEDER, ANSES REKLAMATIONEN FOR V�RENDE RETTIDIG. REKLAMATIONER KAN MEDDELES P� TELEFON 38150510 ELLER PER E-MAIL: INFO@SAXO.COM. DU KAN UDNYTTE DIN REKLAMATIONSRET VED AT SENDE VAREN RETUR ELLER M�DE OP P� VORES FYSISKE ADRESSE OG REKLAMERE DER. VI BETALER DIN UDGIFT TIL FORSENDELSE, HVIS VI HAR LEVERET EN FORKERT ELLER BESKADIGET VARE. VI FORBEHOLDER OS RET TIL AT AFHJ�LPE EN MANGEL/ERSTATTE MED EN UBESKADIGET VARE FREMFOR AT BETALE K�BESUMMEN TILBAGE.' WHERE id = 10001;

INSERT INTO Client.Client_Tbl (countryid, flowid, name, username, passwd, maxamount, lang, logourl, cssurl, callbackurl, accepturl, cancelurl, terms) VALUES (100, 1, 'IHI', 'CPMDemo', 'DEMOisNO_2', 1000000, 'da', 'http://ihi.cellpointmobile.com/img/ihi_logo.gif', 'http://ihi.cellpointmobile.com/css/mpoint.css', 'http://ihi.cellpointmobile.com/mpoint/callback.php', 'http://ihi.cellpointmobile.com/mpoint/accept.php', '', 'Handelsbetingelser');
INSERT INTO Client.Account_Tbl (clientid, name, address) SELECT Max(id), 'Default', '' FROM Client.Client_Tbl;
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) SELECT Max(Cl.id), PC.cardid, PC.pspid FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid > 0 AND PC.pspid = 2 GROUP BY PC.cardid, PC.pspid;
INSERT INTO Client.Keyword_Tbl (clientid, name, standard) SELECT Max(id), 'IHI', true FROM Client.Client_Tbl;
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name) SELECT Max(id), 2, '4216310' FROM Client.Client_Tbl;
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) SELECT Max(id), 2, '-1'  FROM Client.Account_Tbl;

INSERT INTO Client.Shop_Tbl (clientid, keywordid, shipping, ship_cost, free_ship, del_date) SELECT Max(CL.id), 5, 'Post Danmark', 2900, -1, true FROM Client.Client_Tbl Cl;
INSERT INTO Client.Shipping_Tbl (shippingid, shopid, cost, free_ship) SELECT 1, Max(id), 2900, -1 FROM Client.Shop_Tbl;
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'Cash (Jens Findus)', 1, 19995, 'http://demo.ois-inc.com/mpoint/prod/cash.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'NYC (Kristina Korsholm)', 1, 9950, 'http://demo.ois-inc.com/mpoint/prod/nyc.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'De otte (Katherine Neville)', 1, 19995, 'http://demo.ois-inc.com/mpoint/prod/de_otte.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'Narnia Fort�llingerne (CS Lewis)', 1, 44900, 'http://demo.ois-inc.com/mpoint/prod/narnia.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'By af Jazz (Christian Munch-Hansen)', 1, 20000, 'http://demo.ois-inc.com/mpoint/prod/by_af_jazz.jpg');

INSERT INTO Client.SurePay_Tbl (clientid, resend, notify, email) SELECT Max(CL.id), 2, 10, 'support@oismail.com' FROM Client.Client_Tbl CL;