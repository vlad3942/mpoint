-- Split payment hybrid --
UPDATE client.split_configuration_tbl SET type = 'hybrid' WHERE trim(name) IN ('Card+Voucher','APM+Voucher','Wallet+Voucher');
UPDATE client.split_configuration_tbl SET type = 'conventional' WHERE trim(name) IN ('Card+Card');

SELECT setval('client.route_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.route_tbl), 1), false);
SELECT setval('client.routeconfig_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routeconfig_tbl), 1), false);
SELECT setval('client.routecountry_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecountry_tbl), 1), false);
SELECT setval('client.routecurrency_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routecurrency_tbl), 1), false);
SELECT setval('client.routefeature_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routefeature_tbl), 1), false);
SELECT setval('client.routepm_tbl_id_seq', COALESCE((SELECT MAX(id)+1 FROM client.routepm_tbl), 1), false);

-- Client propert fingerprint enchancment --
UPDATE client.client_property_tbl SET value = '45ssiuz3' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;

-- AVIANCA --

delete from client.pm_tbl where clientid=10101 ;

insert into client.pm_tbl (clientid, pmid ) select 10101, id FROM system.card_tbl where name in ('UATP','VISA','Master Card','American Express','Diners Club','PSE','Via Baloto','Efecty','Banco de Bogata','ELO');

delete from client.providerpm_tbl where routeid in (select id from client.route_tbl where clientid=10101) ;

insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=21 and enabled=true) from system.card_tbl where id in (1,3,7,8);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=4 and enabled=true) from system.card_tbl where id in (21,1,3,7,8,82);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=70 and enabled=true) from system.card_tbl where id in (97,98,99);

-- CEBU --

delete from client.pm_tbl where clientid = 10077;

insert into client.pm_tbl (clientid, pmid ) select 10077, id FROM system.card_tbl where id in (7, 8, 93, 5, 1, 28, 94, 95, 26, 96);

delete from client.providerpm_tbl where routeid in (select id from client.route_tbl where clientid=10077) ;

insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=4 and enabled=true ) from system.card_tbl where id in (7,8);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=40 and enabled=true) from system.card_tbl where id in (7,8,93);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=62 and enabled=true) from system.card_tbl where id in (7,8,5);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=63 and enabled=true) from system.card_tbl where id in (1);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=24 and enabled=true) from system.card_tbl where id in (28);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=67 and enabled=true) from system.card_tbl where id in (94);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=68 and enabled=true) from system.card_tbl where id in (95);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=71 and enabled=true) from system.card_tbl where id in (26);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10077 and providerid=69 and enabled=true) from system.card_tbl where id in (96);