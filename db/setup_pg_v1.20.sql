INSERT INTO System.Shipping_Tbl (name, logourl) VALUES ('Post Danmark', 'http://mpoint.cellpointmobile.com/img/post_denmark_logo.gif');

INSERT INTO Client.Shipping_Tbl (shippingid, shopid, cost, free_ship) SELECT 1, id, 2900, -1 FROM Client.Shop_Tbl WHERE id > 0;

INSERT INTO System.PSP_Tbl (name) VALUES ('IHI');
INSERT INTO System.PSPCard_Tbl (pspid, cardid) SELECT 3, cardid FROM System.PSPCard_Tbl WHERE pspid = 2;
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (3, 100, '208');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1022, 'Payment link Resent as Embedded', 'SurePay', 'produceSurePays');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1023, 'Payment link Resent as WAP Push', 'SurePay', 'produceSurePays');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (1029, 'Client Customer Service Notified', 'SurePay', 'produceSurePays');