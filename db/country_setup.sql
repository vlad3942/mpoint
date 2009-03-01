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
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'Narnia Fortællingerne (CS Lewis)', 1, 44900, 'http://demo.ois-inc.com/mpoint/prod/narnia.jpg');
INSERT INTO Client.Product_Tbl (keywordid, name, quantity, price, logourl) VALUES (10, 'By af Jazz (Christian Munch-Hansen)', 1, 20000, 'http://demo.ois-inc.com/mpoint/prod/by_af_jazz.jpg');

INSERT INTO Client.SurePay_Tbl (clientid, resend, notify, email) SELECT Max(CL.id), 2, 10, 'support@oismail.com' FROM Client.Client_Tbl CL;