-- mPoint DB Scripts :

-- CEBU MPGS
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 72, 'TEST048583918507', 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', true, null);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100770, 72, '-1', true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, capture_type) VALUES (10077, 8, true, 72, null, 1, null, false, 1, 2);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, capture_type) VALUES (10077, 7, true, 72, null, 1, null, false, 1, 2);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type, capture_type) VALUES (10077, 5, true, 72, null, 1, null, false, 1, 2);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.PHP', 'TEST048583918507', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.PHP', 'merchant.TEST048583918507', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.PHP', '42b5c09392e50702e05f29c37c75841a', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('mvault', true, 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('AIRLINE_CODE', '6S', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('CARRIER_NAME', 'SAUDI GULF AIRLINES', 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('PROFILE_EXPIRY', 180, 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('ENABLE_PROFILE_ANONYMIZATION', true, 10077, 'client', true, 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.HKD', 'TEST088008881200', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.HKD', 'merchant.TEST088008881200', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.HKD', '7f8945cebdfd056530baca4e3e8d384a', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.SGD', 'TEST065004485703', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.SGD', 'merchant.TEST065004485703', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.SGD', 'c1e2d0ffb6f52218be89ac0061af1f2a', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.PHP', 'FB07E814271FBD40F7DDD16760241C2C', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.HKD', 'E6C804D72C50FE51B314129F707E9FD8', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SGD', '96E5B4B82E98A1048073D96F32F69E1F', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.USD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.USD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.USD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.AED', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.AED', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.AED', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.AED', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.AUD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.AUD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.AUD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.AUD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.BND', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.BND', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.BND', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.BND', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CNY', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CNY', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CNY', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CNY', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.IDR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.IDR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.IDR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.IDR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.JPY', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.JPY', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.JPY', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.JPY', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.KRW', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.KRW', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.KRW', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.KRW', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.MOP', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.MOP', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.MOP', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.MOP', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.MYR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.MYR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.MYR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.MYR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.THB', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.THB', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.THB', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.THB', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.TWD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.TWD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.TWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.TWD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.USD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.USD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.USD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CAD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CAD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CAD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CAD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CHF', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CHF', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CHF', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CHF', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.DKK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.DKK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.DKK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.DKK', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.EUR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.EUR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.EUR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.EUR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.GBP', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.GBP', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.GBP', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.GBP', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.LKR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.LKR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.LKR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.LKR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.NZD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.NZD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.NZD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.NZD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.QAR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.QAR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.QAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.QAR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.SAR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.SAR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.SAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SAR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.SEK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.SEK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.SEK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SEK', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.INR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.INR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.INR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.INR', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.BHD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.BHD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.BHD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.BHD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.KWD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.KWD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.KWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.KWD', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.VND', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.VND', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.VND', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.VND', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.NOK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.NOK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.NOK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.NOK', '840F03BBDB27997C18FD567F977A9F94', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);


--CEBU MPGS CRS query

INSERT INTO client.route_tbl (id, clientid, providerid) VALUES(( SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 10077, 72);
 
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_PHP', 2, 'TEST048583918507', 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='TEST048583918507';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'TEST048583918507';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_HKD', 2, 'TEST088008881200', 'merchant.TEST088008881200', '7f8945cebdfd056530baca4e3e8d384a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='TEST088008881200';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'TEST088008881200';


INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SGD', 2, 'TEST065004485703', 'merchant.TEST065004485703', 'c1e2d0ffb6f52218be89ac0061af1f2a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='TEST065004485703';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'TEST065004485703';


INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_USD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_USD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_USD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_AED', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_AED';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_AED';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_AUD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_AUD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rrc.name ='MPGS_AUD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_BND', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_BND';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_BND';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CNY', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_CNY';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.CNY';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_IDR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_IDR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_IDR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_JPY', 2, 'TEST048583918508Y', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_JPY';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_JPY';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_KRW', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_KRW';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_KRW';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_MOP', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_MOP';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_MOP';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_MYR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_MYR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_MYR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_THB', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_THB';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_THB';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_TWD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_TWD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_TWD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CAD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_CAD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_CAD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CHF', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_CHF';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_CHF';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_DKK', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_DKK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_DKK';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_EUR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_EUR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_EUR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_GBP', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_GBP';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_GBP';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_LKR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_LKR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_LKR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_NZD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_NZD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_NZD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_QAR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_QAR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_QAR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SAR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_SAR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_SAR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SEK', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_SEK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_SEK';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_INR', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_INR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_INR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_BHD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_BHD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_BHD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_KWD', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_KWD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_KWD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_VND', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_VND';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_VND';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_NOK', 2, 'TEST048583918508', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and rc.name ='MPGS_NOK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.name ='MPGS_NOK';