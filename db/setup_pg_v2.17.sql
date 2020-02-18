--Sandbox token for OD - DEV , SIT
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', 10018, 'client', 2 );

-----------------------------
--For test env we have a single token for all clients, insert one for each client.
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', <client-id>, 'client', 2 );

---------------

--Universal Client token from mProfile
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', <value>, <client-id>, 'client', 2 );

--PR
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc2MjY0NzYsImlhdCI6MTU2MTk1Mjg3NiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIwfQ.vGiU5yzW2hf0Eb4lkV6IIJJP-DkKYrRlr1OWadTWOzA', 10020, 'client', 2 );

--OD
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU4NDIsImlhdCI6MTU2MjA0MjI0MiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDE4fQ.Jbt9ET6fKG0j4j5r6rIHyUcNIC4O5xD-8fRf5bFT2rE', 10018, 'client', 2 );

--SGA
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU3NTgsImlhdCI6MTU2MjA0MjE1OCwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIxfQ.r0ZjGNqoWkQCfUS-bCkPBbzempoeljurOwe5OPeNNQI', 10021, 'client', 2 );

----------------------------


-- Profile expiry defined by Client -- Defined in number of days
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', <value>, <client-id>, 'client', 2 );

--Sandbox profile expiry for profiles created for OD txns, 180 days approx 6 months
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', '180', 10018, 'client', 2 );
--------------
---------

-- Data anonymization flag to enable/disable it for a Client
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('ENABLE_PROFILE_ANONYMIZATION', <value>, <client-id>, 'client', 0 );

--Sandbox Data anonymization flag enabled for a test Client
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('ENABLE_PROFILE_ANONYMIZATION', 'true', 10018, 'client', 0 );

-- Insert business type details
insert into system.businesstype_tbl (id,name) values
(0,'None'),
(1,'Non-Industry-Specific'),
(2,'Airline Industry'),
(3,'Auto Rental Industry'),
(4,'Cruise Industry'),
(5,'Hospitality Industry'),
(6,'Entertainment/Ticketing Industry'),
(7,'e-commerce Industry');

-- Insert New transactions states into log.state_tbl
insert into log.state_tbl (id,name, module,func) values
(2010101,'Failed during Capture','Payment','Capture'),
(2010201,'Failed during Cancel','Payment','Cancel'),
(2010301,'Failed during Refund','Payment','Refund');

-- Set businesstype 2 for UATP client
update client.account_tbl set businesstype = 2 where clientid = 10069;

--Sandbox token for OD - DEV , SIT
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', 10018, 'client', 2 );

-----------------------------
--For test env we have a single token for all clients, insert one for each client.
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', <client-id>, 'client', 2 );

---------------

--Universal Client token from mProfile
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', <value>, <client-id>, 'client', 2 );

--PR
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc2MjY0NzYsImlhdCI6MTU2MTk1Mjg3NiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIwfQ.vGiU5yzW2hf0Eb4lkV6IIJJP-DkKYrRlr1OWadTWOzA', 10020, 'client', 2 );

--OD
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU4NDIsImlhdCI6MTU2MjA0MjI0MiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDE4fQ.Jbt9ET6fKG0j4j5r6rIHyUcNIC4O5xD-8fRf5bFT2rE', 10018, 'client', 2 );

--SGA
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU3NTgsImlhdCI6MTU2MjA0MjE1OCwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIxfQ.r0ZjGNqoWkQCfUS-bCkPBbzempoeljurOwe5OPeNNQI', 10021, 'client', 2 );

----------------------------


-- Profile expiry defined by Client -- Defined in number of days
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', <value>, <client-id>, 'client', 2 );

--Sandbox profile expiry for profiles created for OD txns, 180 days approx 6 months
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', '180', 10018, 'client', 2 );
--------------
---------

-- Data anonymization flag to enable/disable it for a Client
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('ENABLE_PROFILE_ANONYMIZATION', <value>, <client-id>, 'client', 0 );

--Sandbox Data anonymization flag enabled for a test Client
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('ENABLE_PROFILE_ANONYMIZATION', 'true', 10018, 'client', 0 );
--------------

--index on table: log.TxnPassbook_tbl --column:performedopt,status
CREATE INDEX txn_status ON log.txnpassbook_tbl (performedopt, status);
