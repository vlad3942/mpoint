INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_USERNAME', 'cellpoint_o', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_PASSWORD', 'cl5PG49IzmrH', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_FILENAME', 'tsto1654.asc', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) VALUES ('UATP_SETTLEMENT_FILE_NAME', 'tsto1654', <CLIENTID>, 'client');

UPDATE System.PSP_Tbl SET system_type = 8 , capture_method = 6 WHERE id = 50;


--CMP-2836
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60110000 and max = 60110999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60112000 and max = 60114999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117400 and max = 60117499;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60117700 and max = 60117999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 60118600 and max = 60119999;
UPDATE system.cardprefix_tbl SET enabled=false where cardid=3 and min = 64400000 and max = 65999999;