-- mPoint DB Scripts :

-- CEBU MPGS
INSERT INTO client.merchantaccount_tbl (clientid, pspid, name, username, passwd, enabled, stored_card) VALUES (10077, 72, 'TEST048583918507', 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', true, null);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, name, enabled) VALUES (100770, 72, '-1', true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10077, 8, true, 72, null, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10077, 7, true, 72, null, 1, null, false, 1);
INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, position, preferred, psp_type) VALUES (10077, 5, true, 72, null, 1, null, false, 1);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.PHP', 'TEST048583918507', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.PHP', 'merchant.TEST048583918507', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.PHP', '42b5c09392e50702e05f29c37c75841a', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('HOST', 'gpmnl.gateway.mastercard.com', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
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
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.PHP', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.HKD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SGD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.USD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.USD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.USD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.AED', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.AED', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.AED', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.AED', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.AUD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.AUD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.AUD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.AUD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.BND', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.BND', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.BND', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.BND', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CNY', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CNY', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CNY', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CNY', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.IDR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.IDR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.IDR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.IDR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.JPY', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.JPY', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.JPY', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.JPY', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.KRW', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.KRW', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.KRW', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.KRW', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.MOP', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.MOP', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.MOP', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.MOP', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.MYR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.MYR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.MYR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.MYR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.THB', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.THB', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.THB', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.THB', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.TWD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.TWD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.TWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.TWD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.USD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.USD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.USD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.USD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CAD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CAD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CAD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CAD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.CHF', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.CHF', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.CHF', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.CHF', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.DKK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.DKK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.DKK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.DKK', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.EUR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.EUR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.EUR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.EUR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.GBP', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.GBP', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.GBP', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.GBP', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.LKR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.LKR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.LKR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.LKR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.NZD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.NZD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.NZD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.NZD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.QAR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.QAR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.QAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.QAR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.SAR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.SAR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.SAR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SAR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.SEK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.SEK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.SEK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.SEK', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.INR', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.INR', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.INR', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.INR', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.BHD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.BHD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.BHD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.BHD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.KWD', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.KWD', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.KWD', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.KWD', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.VND', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.VND', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.VND', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.VND', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('mid.NOK', 'TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('username.NOK', 'merchant.TEST048583918508', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('password.NOK', '78ebecfdcd194aac7c1e5fde0b584f40', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES('Notification-Secret.NOK', '379001F6E4852A832F8138F70190585B', true, (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10077 and pspid = 72), 'merchant', 2);


--CEBU MPGS CRS query

INSERT INTO client.route_tbl (clientid, providerid) VALUES(10077, 72);
 
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_PHP', 1, 'mid.PHP', 'merchant.TEST048583918507', '42b5c09392e50702e05f29c37c75841a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.PHP';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.PHP';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_HKD', 1, 'mid.HKD', 'merchant.TEST088008881200', '7f8945cebdfd056530baca4e3e8d384a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.HKD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.HKD';


INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SGD', 1, 'mid.SGD', 'merchant.TEST065004485703', 'c1e2d0ffb6f52218be89ac0061af1f2a', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.SGD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.SGD';


INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_USD', 1, 'mid.USD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.USD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.USD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_AED', 1, 'mid.AED', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.AED';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.AED';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_AUD', 1, 'mid.AUD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.AUD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.AUD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_BND', 1, 'mid.BND', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.BND';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.BND';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CNY', 1, 'mid.CNY', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.CNY';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.CNY';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_IDR', 1, 'mid.IDR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.IDR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.IDR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_JPY', 1, 'mid.JPY', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.JPY';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.JPY';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_KRW', 1, 'mid.KRW', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.KRW';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.KRW';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_MOP', 1, 'mid.MOP', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.MOP';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.MOP';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_MYR', 1, 'mid.MYR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.MYR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.MYR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_THB', 1, 'mid.THB', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.THB';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.THB';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_TWD', 1, 'mid.TWD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.TWD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.TWD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CAD', 1, 'mid.CAD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.CAD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.CAD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_CHF', 1, 'mid.CHF', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.CHF';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.CHF';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_DKK', 1, 'mid.DKK', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.DKK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.DKK';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_EUR', 1, 'mid.EUR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.EUR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.EUR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_GBP', 1, 'mid.GBP', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.GBP';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.GBP';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_LKR', 1, 'mid.LKR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.LKR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.LKR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_NZD', 1, 'mid.NZD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.NZD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.NZD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_QAR', 1, 'mid.QAR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.QAR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.QAR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SAR', 1, 'mid.SAR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.SAR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.SAR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_SEK', 1, 'mid.SEK', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.SEK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.SEK';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_INR', 1, 'mid.INR', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.INR';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.INR';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_BHD', 1, 'mid.BHD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.BHD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.BHD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_KWD', 1, 'mid.KWD', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.KWD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.KWD';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_VND', 1, 'mid.VND', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.VND';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.VND';

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MPGS_NOK', 1, 'mid.NOK', 'merchant.TEST048583918508', '78ebecfdcd194aac7c1e5fde0b584f40', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 72;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mid.NOK';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'mid.NOK';