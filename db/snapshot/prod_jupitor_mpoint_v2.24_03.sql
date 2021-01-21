--Table Name : Client.CardAccess_Tbl
--REF - CTECH-4252

update client.additionalproperty_tbl set value = 'STRICT' where key = 'SSO_PREFERENCE' 
and externalid = 10077;

INSERT INTO client.url_tbl(clientid, urltypeid, url, enabled) Values
(10077, 2, 'https://5j.velocity.cellpointmobile.net:443/mprofile/ciam/get-customer-profile', true );