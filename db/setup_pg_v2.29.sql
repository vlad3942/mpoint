insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('NATURAL_MAXAmount',8282000,10101,'client',2)
    insert into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('JURIDICAL_MAXAmount',28985000,10101,'client',2)
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_PSE_TIMEOUT',35,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_NIT',890.100.577-6,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_PaymentType','untokenized',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_PaymentApiVersion',1.3.0,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('HourToAddInBookingTime',22,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('DomesticHourToMinusFromDeparture',8,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('InterNationalHourToMinusFromDeparture',11,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_EXTENDED_TIME_IN_MINUTE',10,(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);

INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_BANK_API_USERNAME','4Vj8eK4rloUd272L48hsrarnUA',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);

INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_BANK_API_PASSWORD','pRRXKOl8ikMmt9u',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);

INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_USER_TYPE','N',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);

INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_USER_DOCUMENT_NUMBER','AV 3BV9PL',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_USER_DOCUMENT_TYPE','12345678',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);
/*if PAYU_DYNAMIC_DOCUMENT true means document should be dynamic  from hpp*/
INSERT into client.additionalproperty_tbl (key,value,externalid,type,scope) values ('PAYU_DYNAMIC_DOCUMENT','false',(SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10101 and pspid = 53),'merchant',1);