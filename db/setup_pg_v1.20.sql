INSERT INTO System.Shipping_Tbl (name, logourl) VALUES ('Post Danmark', 'http://mpoint.cellpointmobile.com/img/post_denmark_logo.gif');

INSERT INTO Client.Shipping_Tbl (shippingid, shopid, cost, free_ship) SELECT 1, id, 2900, -1 FROM Client.Shop_Tbl WHERE id > 0;