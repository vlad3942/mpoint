UPDATE Client.URL_Tbl SET url = 'https://cpmlive.velocity.cellpointmobile.net:443' WHERE clientid=10099 and urltypeid = 4; 
UPDATE Client.URL_Tbl SET url = 'https://wn.velocity.cellpointmobile.net:443' WHERE clientid=10069 and urltypeid = 4; 
UPDATE Client.URL_Tbl SET url = 'https://pr.velocity.cellpointmobile.net:443' WHERE clientid=10020 and urltypeid = 4; 
UPDATE Client.URL_Tbl SET url = 'https://6s.velocity.cellpointmobile.net:443' WHERE clientid=10021 and urltypeid = 4; 

UPDATE Client.URL_Tbl SET url = 'https://mpoint.prod-01.cellpoint.cloud/_test/auth.php' WHERE clientid=10069 and urltypeid = 2;
UPDATE Client.URL_Tbl SET url = 'https://6s.velocity.cellpointmobile.net:443/mpoint/mprofile/authenticate-user' WHERE clientid=10021 and urltypeid = 2;
UPDATE Client.URL_Tbl SET url = 'https://od.velocity.cellpointmobile.net:443/mpoint/mprofile/authenticate-user' WHERE clientid=10018 and urltypeid = 2;


DELETE FROM client.additionalproperty_tbl where key = 'mpi_rule';

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=4;

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'mpi_rule', 'isProceedAuth::=<status>=="2"OR<status>=="5"OR<status>=="6"
status::=(additional-data.param[@name=''status''])', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10077 AND pspid=63;

--Payment is currently unavailable due to large amount of IDR currency
UPDATE Client.Client_Tbl SET maxamount = 999999999900 WHERE id = 10020;

-- Configure CIAM with LOOSE
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES( 'SSO_PREFERENCE', 'LOOSE',true,  10077,'client',2);

--- If any client using the cybersource api then as per cybersource documentation compulsary businesstype is 2(airline) for airline transaction
update client.account_tbl set businesstype = 2 where clientid = 10020;