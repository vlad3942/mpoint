--Datacash Notification Secret config Start --
-- Please execute in given order.
--1. Add notification secret for AED currency txns.
-- Edit clientid, value columns if required.
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('Notification-Secret.AED', 'E50ED8C370FDA03A8817AE9CA1B29884', (SELECT id FROM client.merchantaccount_tbl WHERE clientid=10021 and pspid=17), 'merchant');

--2. Existing entry with key 'Notification-Secret' should be updated for SAR currency.
UPDATE client.additionalproperty_tbl set  key='Notification-Secret.SAR' where key='Notification-Secret' and externalid=(SELECT id FROM client.merchantaccount_tbl WHERE clientid=10021 and pspid=17);

--3. Add an entry for USD -- Assuming SAR notification secret works for USD txns currently
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'Notification-Secret.USD', value, externalid, 'merchant' from client.additionalproperty_tbl WHERE key='Notification-Secret.SAR' ;


--Optional - Generic sql for any new currency to be added for DC in the future
--Edit the currency code (3 char alpha code), notification secret value and client id (if other than SGA).
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('Notification-Secret.<alpha3code>', '<notification-secret-from-SABBorDatacash>', (SELECT id FROM client.merchantaccount_tbl WHERE clientid=10021 and pspid=17), 'merchant');

--Datacash END --