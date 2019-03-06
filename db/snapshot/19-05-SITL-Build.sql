INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_USERNAME', 'cellpoint_o', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_PASSWORD', 'cl5PG49IzmrH', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) SELECT 'UATP_SFTP_FILENAME', 'tsto1654.asc', id, 'merchant' FROM Client.MerchantAccount_Tbl WHERE pspid = 50 AND clientid = <CLIENTID>;
INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) VALUES ('UATP_SETTLEMENT_FILE_NAME', 'tsto1654', <CLIENTID>, 'client');

UPDATE System.PSP_Tbl SET system_type = 8 , capture_method = 6 WHERE id = 50;

