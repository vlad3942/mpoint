/*=============== Gateway Stat Data -================ */

-- Table: system.statisticstype_tbl

-- DROP TABLE system.statisticstype_tbl;

CREATE TABLE system.statisticstype_tbl
(
  id serial NOT NULL,
  name character varying(200),
  description character varying(200),
  enabled boolean NOT NULL DEFAULT true,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT stattype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.statisticstype_tbl
  OWNER TO mpoint;


  -- Table: client.gatewaystat_tbl

-- DROP TABLE client.gatewaystat_tbl;

CREATE TABLE client.gatewaystat_tbl
(
  id serial NOT NULL,
  gatewayid integer NOT NULL,
  clientid integer NOT NULL,
  statetypeid integer NOT NULL,
  statvalue integer NOT NULL,
  enabled boolean NOT NULL DEFAULT true,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  reseton timestamp without time zone,
  CONSTRAINT stat_pk PRIMARY KEY (id),
  CONSTRAINT clientstat_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT gatewaystat_fk FOREIGN KEY (gatewayid)
      REFERENCES system.psp_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT stattype_fk FOREIGN KEY (statetypeid)
      REFERENCES system.statisticstype_tbl (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gatewaystat_tbl
  OWNER TO mpoint;



INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (1,'Txn Volume', 'Volume of Transactions thourgh a particular gateway for a specific client');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (2,'Success Ratio', 'Succes vs. failure transactions using a gateway for a time period');
INSERT INTO system.statisticstype_tbl(  id, name, description)    VALUES (3,'Response Time', 'Avg response time of a gateway during txn authorization');
-- Citcon WeChat --
--QR Code timeout value in seconds
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('QR_CODE_TIMEOUT', '180', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');


--Virtual payment page timer value in mm:ss, this should be less than or equal to the QR code timeout property
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('VIRTUAL_PAYMENT_TIMER', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = 10007 and pspid = 41), 'merchant');


--url to link wechat icon
INSERT INTO client.url_tbl(urltypeid, clientid, url)
VALUES (14, 10007, "https://s3-ap-southeast-1.amazonaws.com/cpmassets/payment/icons");
--------------

