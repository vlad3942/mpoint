---------- Set below property only if merchant wants to participate in 3ds authentication -------------------------------
---------- Replace 10007 with respective merchant id ----------------------------
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('3DVERIFICATION', 'true', 10007, 'client');