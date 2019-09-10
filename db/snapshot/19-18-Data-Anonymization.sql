--Sandbox token for OD - DEV , SIT
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', 10018, 'client', 0 );

-----------------------------
--For test env we have a single token for all clients, insert one for each client.
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ2NjEzOTU4MDEsImlhdCI6MTUwNTcyMjIwMSwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50aWQiOiIxMDAxOCIsInNlc3Npb25JZCI6MX0.GbnU1gTFPAY8jgJWsLJBXDxG8_0Rvazx69MP53hRL1w', <client-id>, 'client', 0 );

---------------

--Universal Client token from mProfile
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', <value>, <client-id>, 'client', 0 );

--PR
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc2MjY0NzYsImlhdCI6MTU2MTk1Mjg3NiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIwfQ.vGiU5yzW2hf0Eb4lkV6IIJJP-DkKYrRlr1OWadTWOzA', 10020, 'client', 0 );

--OD
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU4NDIsImlhdCI6MTU2MjA0MjI0MiwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDE4fQ.Jbt9ET6fKG0j4j5r6rIHyUcNIC4O5xD-8fRf5bFT2rE', 10018, 'client', 0 );

--SGA
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_TOKEN', 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjQ3MTc3MTU3NTgsImlhdCI6MTU2MjA0MjE1OCwiaXNzIjoiQ1BNIiwidHlwZSI6MiwiY2xpZW50SWQiOjEwMDIxfQ.r0ZjGNqoWkQCfUS-bCkPBbzempoeljurOwe5OPeNNQI', 10021, 'client', 0 );

----------------------------
alter table enduser.account_tbl
    add mProfileId varchar default 50;

comment on column enduser.account_tbl.mProfileId is 'mProfile id related to the end-user profile';

---------

-- Profile expiry defined by Client -- Defined in number of days
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', <value>, <client-id>, 'client', 0 );

--Sandbox profile expiry for profiles created for OD txns, 180 days approx 6 months
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
VALUES ('PROFILE_EXPIRY', '180', 10018, 'client', 0 );
--------------