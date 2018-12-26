--Datacash Start --
--Edit notification secret value column.
-- Edit client id if required.
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('Notification-Secret.AED', '<notification-secret-from-SABBorDatacash>', (SELECT id FROM client.merchantaccount_tbl t WHERE clientid=10021 and pspid=17), 'merchant');

--Existing entry with key 'Notification-Secret' should be updated as for SAR currency.
UPDATE client.additionalproperty_tbl set  key='Notification-Secret.SAR' where key='Notification-Secret';
--Datacash END --