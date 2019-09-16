-- UATP CeptorAccessId and CeptorAccessKey
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('CeptorAccessId', 'cellpointuser', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('CeptorAccessKey', 'PhkdD7IB', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10069 and pspid = 50), 'merchant',1);

--  New base url for client assets
INSERT INTO System.urltype_Tbl (id, name) VALUES (16, 'Base URL For Client Asset');
INSERT INTO Client.url_Tbl (urltypeid, clientid, url) VALUES (16, 10018, 'https://s3-ap-southeast-1.amazonaws.com/devcpmassets');

/* Ticket level transaction - Add new column fees in log.order_tbl */
ALTER TABLE Log.order_tbl ADD COLUMN fees integer DEFAULT 0;
