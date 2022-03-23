-- AVIANCA

delete from client.pm_tbl where clientid=10101 ;

insert into client.pm_tbl (clientid, pmid ) select 10101, id FROM system.card_tbl where name in ('UATP','VISA','Master Card','American Express','Diners Club','PSE','Via Baloto','Efecty','Banco de Bogata','ELO');

delete from client.providerpm_tbl where routeid in (select id from client.route_tbl where clientid=10101) ;

insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=21 and enabled=true) from system.card_tbl where id in (1,3,7,8);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=4 and enabled=true) from system.card_tbl where id in (21,1,3,7,8,82);
insert into client.providerpm_tbl (pmid, routeid) select id,(select id from client.route_tbl where clientid=10101 and providerid=70 and enabled=true) from system.card_tbl where id in (97,98,99);

-- CEBU

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
