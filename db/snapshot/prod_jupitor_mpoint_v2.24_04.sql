--REF - CTECH-4245
--mPoint :

--client.cardaccess_tbl :

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10020, 15, true, 56, 642, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10020, 7, true, 56, 642, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10020, 8, true, 56, 642, 1, NULL, false, 1, 0, 0, 2, 14, false);

--client.merchantaccount_tbl :

INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card) VALUES(10020, 56, 'Global Payments', true, '', '', NULL);
INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card) VALUES(10020, 14, 'merchant.pal.applepay', true, 'uname' ,'pass', NULL);

--client.merchantsubaccount_tbl :

INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100121, 56, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES(100121, 14, '-1', true);

--ApplePay PHP :                                                                           

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('GlobalPayment.Wallet.USERNAME.14.PHP', 'gpmnl045623832731', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 56 and clientid = 10020), 'merchant', 2);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('GlobalPayment.Wallet.PASSWORD.14.PHP', '2OcEjLAKyEzjy2fxjw6xAo7WL1uXlOuwjM1T2p6YupPJG3NWPm2v4qu9OaoztU8LqhO39yHDkk3k+qOENc6IUpq/VKzesH4KyDgNrpMn4PuhaJ/iN7Kt0u9Kr+CExksTO5TVTuoYtmXIB2L5Pe6xYkUwaMm8kADncWJq1EaxgbNrMiJMJuZ+dulYBC/5fE62MpEJmmx+QXLzxt+rqyZ31kBTfp1tOFeRvOy+oFxQK3pUFP3eYNDeG1q2rTUqK5KyJtv447QJ4XFtzVp3Ycgh3HqeeLVjIrL6tQbnYxDuFXSPBuMdFatAUQu9b5roxmIFixW/Wz44+tr5A+Z1dnVW7g==', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 56 and clientid = 10020), 'merchant', 2);

--ApplePay Other Currency :                                                                

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('GlobalPayment.Wallet.USERNAME.14', 'gpmnl042772772761', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 56 and clientid = 10020), 'merchant', 2);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('GlobalPayment.Wallet.PASSWORD.14', '/BqAFHYCws3429A/Ath80pUhtYDC58+8jV117Q7aTWSWL9DVY8gGzAgZHXjuK6kk7UZ11L+koxRW6+UUCHv24WORAwCeVG/+0+QySr3La/T8RHNCTD3+3m0t+4yEhAwplhbxSq8WMkGNOrTcHgJkCTR21+bIAeB2bldx8PAMAdbkL/bu3QxW/eiAr3cCC+d6eir5ZB40gFq1BzDTMGcy8ZQuTQ3wQSU9aGM5NMZqJjSgdECTvAGN0zjACI7WjdTsYxqHca/3gh+JtwUfoUDdDxrTzFL27K/OpoRSz9N1c4BoJYDEFlzugb1qJrMQUQEpJMG2Jsvs4nfQmQjuwSjX2Q==', true, (SELECT ID FROM client.merchantaccount_tbl WHERE pspid = 56 and clientid = 10020), 'merchant', 2);

-- June to Sep
update log.transaction_tbl set callbackurl = 'https://mpoint.prod-01.cellpoint.cloud/uatp/callback.php'
where clientid = 10069 and callbackurl in ('http://wn.velocity.cellpointmobile.net:10080/mpoint/uatp/callback','http://mpoint.cellpointmobile.net/uatp/callback.php') 
and created > '2020-06-01 00:00:00' and  created < '2020-09-01 00:00:00';

-- March to June
update log.transaction_tbl set callbackurl = 'https://mpoint.prod-01.cellpoint.cloud/uatp/callback.php'
where clientid = 10069 and callbackurl in ('http://wn.velocity.cellpointmobile.net:10080/mpoint/uatp/callback','http://mpoint.cellpointmobile.net/uatp/callback.php') 
and created > '2020-03-01 00:00:00' and created < '2020-06-01 00:00:00';

-- Jan to March
update log.transaction_tbl set callbackurl = 'https://mpoint.prod-01.cellpoint.cloud/uatp/callback.php'
where clientid = 10069 and callbackurl in ('http://wn.velocity.cellpointmobile.net:10080/mpoint/uatp/callback','http://mpoint.cellpointmobile.net/uatp/callback.php') 
and created > '2020-01-01 00:00:00' and created < '2020-03-01 00:00:00';


