-- 2c2p alc Airline data improvement -- start --

-- Alter Log.Passenger Tbl to store additional passenger data

ALTER TABLE log.passenger_tbl
  ADD COLUMN title character varying(20);
ALTER TABLE log.passenger_tbl
  ADD COLUMN email character varying(50);
ALTER TABLE log.passenger_tbl
  ADD COLUMN mobile character varying(15);
ALTER TABLE log.passenger_tbl
  ADD COLUMN "country_id" character varying(3);

-- Alter Log.flight_tbl to store additional flight data
ALTER TABLE log.flight_tbl
  ADD COLUMN tag character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "trip_count" character varying(2);
ALTER TABLE log.flight_tbl
  ADD COLUMN "service_level" character varying(2);

-- 2c2p alc Airline data improvement -- end --
-- Alipay Chinese PIDs --

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.html', '2088102135220161', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'pid.app', '2088102170185364', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;


INSERT INTO log.state_tbl(id, name, module) VALUES(4031,'Session Partially Completed','Payment');

-- To execute the below query first need to truncate the session_tbl data.
-- Run the "TRUNCATE TABLE log.session_tbl CASCADE;" before executing below query.
ALTER TABLE log.session_tbl ADD CONSTRAINT constraint_name UNIQUE (orderid);

---Datacash MID
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, password) VALUES (10007, 17, 'SGBSABB01', 'merchant.SGBSABB01', 'bebd68b2fa491f807e40462a6f85617e');
