-- Split payment hybrid --
UPDATE client.split_configuration_tbl SET type = '20' WHERE trim(name) IN ('Card+Voucher','APM+Voucher','Wallet+Voucher');
UPDATE client.split_configuration_tbl SET type = '20' WHERE trim(name) IN ('Card+Card');

-- Client propert fingerprint enchancment --
--UPDATE client.client_property_tbl SET value = 'k8vif92e' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;

-- AVIANCA --
delete from client.pm_tbl where clientid = 10101 ;
delete from client.providerpm_tbl where routeid in (select id from client.route_tbl where clientid=10101) ;

-- CEBU --
delete from client.pm_tbl where clientid = 10077;
delete from client.providerpm_tbl where routeid in (select id from client.route_tbl where clientid=10077) ;

-----CMP-6219-----
UPDATE client.additionalproperty_tbl SET  value='cellpoint_o' WHERE id=172 and key='UATP_SFTP_USERNAME' and  externalid=428 and "type"='merchant';
UPDATE client.additionalproperty_tbl SET  value='/uatp/cellpoint_o/uploads' WHERE id=170 and key='UATP_SFTP_FILE_PATH' and  externalid=428 and "type"='merchant';
UPDATE client.additionalproperty_tbl SET  value='https://sitaftp.sita.aero' WHERE id=181 and key='SFTP_HOST' and  externalid=428 and "type"='merchant';