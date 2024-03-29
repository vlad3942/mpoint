/*revert previous value from DB only for SIT/Dev*/
DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_PSE_TIMEOUT';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_NIT';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_PaymentType';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_PaymentApiVersion';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_EXTENDED_TIME_IN_MINUTE';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_DYNAMIC_DOCUMENT';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_USER_DOCUMENT_TYPE';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_USER_DOCUMENT_NUMBER';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_USER_TYPE';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_BANK_API_PASSWORD';

DELETE from client.additionalproperty_tbl WHERE key = 'PAYU_BANK_API_USERNAME';

/*first run above quesy only for SIT/Dev */
/*route level can be changes per route for PSP*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PSETimeout',35,(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('NIT','890.100.577-6',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PaymentType','untokenized',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PaymentApiVersion','1.3.0',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('HourToAddInBookingTime',22,(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DomesticHourToMinusFromDeparture',8,(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('InterNationalHourToMinusFromDeparture',11,(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value for psp*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('ExtendedTimeInMinute',10,(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*routelevel for payu*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('BankApiUsername','4Vj8eK4rloUd272L48hsrarnUA',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*route level for PayU*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('BankApiPassword','pRRXKOl8ikMmt9u',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value for payu*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('UserType','N',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);

INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('UserDocumentNumber','AV 3BV9PL',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*fix value for PayU*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('UserDocumentType','12345678',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);
/*if DYNAMIC_DOCUMENT true means document should be dynamic  from hpp it's fix value for PayU*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DynamicDocument','false',(SELECT ID FROM client.route_tbl WHERE clientid = 10101 and providerid = 53),'merchant',1);