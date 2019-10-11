/* Ticket level transaction - Add new column fees in log.order_tbl */
ALTER TABLE Log.order_tbl ADD COLUMN fees integer DEFAULT 0;

-- Create new table system.businesstype_tbl to store businesstype for each client
CREATE TABLE system.businesstype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  enabled boolean DEFAULT true,
  CONSTRAINT businesstype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE system.businesstype_tbl OWNER TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO jona;

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

-- Added new column and foreign key constraint
ALTER TABLE client.account_tbl 
	ADD COLUMN businessType integer DEFAULT 0,
	ADD CONSTRAINT businessType_pk FOREIGN KEY (businessType) REFERENCES system.businesstype_tbl (id);

-- Set businesstype 2 for UATP client
update client.account_tbl set businesstype = 2 where clientid = 10069;

-- Insert New transactions states into log.state_tbl
insert into log.state_tbl (id,name, module,func) values
(2010101,'Failed during Capture','Payment','Capture'),
(2010201,'Failed during Cancel','Payment','Cancel'),
(2010301,'Failed during Refund','Payment','Refund');

ALTER TABLE Log.Order_Tbl ADD COLUMN orderref character varying(40);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_TICKET_LEVEL_SETTLEMENT', 'true', <merchant-table-id>, 'merchant', 0);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_TICKET_LEVEL_SETTLEMENT', 'true', <merchant-table-id>, 'merchant', 0);