--html supports MD5 or RSA
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'signtype.html', 'RSA', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
--app supports RSA and RSA2
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'signtype.app', 'RSA2', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;