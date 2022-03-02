-- APIKEY property for Adyen Provider
INSERT INTO client.psp_property_tbl (clientid,propertyid,value,enabled)
VALUES
('<client-id>',(SELECT id FROM system.psp_property_tbl WHERE pspid  = 12 AND name = 'APIKEY'),'<api-key>',true);

-- APIKEY property for Adyen Provider - 10101 Client
INSERT INTO client.psp_property_tbl (clientid,propertyid,value,enabled)
VALUES
('10101',(SELECT id FROM system.psp_property_tbl WHERE pspid  = 12 AND name = 'APIKEY'),'d3NfMjAwMDQ1QENvbXBhbnkuQ2VsbHBvaW50TW9iaWxlOmplcGY5c2F3bkZwYkNQTURFTU9ERU1P',true);