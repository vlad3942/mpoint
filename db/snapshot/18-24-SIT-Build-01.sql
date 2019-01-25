---------- Set below property only if merchant wants to participate in 3ds authentication -------------------------------

---------- Replace miMeeting with respective merchant id ----------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CHECK_3DS_ENROLLMENT', 'true', 10065, 'client');

---------- Replace miRide with respective merchant id ( DO NOT EXECUTE - as we would skip 3ds check for miRide)----------------------------
--INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('CHECK_3DS_ENROLLMENT', 'false', 10062, 'client');