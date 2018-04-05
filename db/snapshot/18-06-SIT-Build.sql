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
--Citcon Wechat change for user experience
--Redirect URL for HPP integration - can be blank or accept url or as required for client configuration
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('WECHAT_CALLBACK_URL', '', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');
