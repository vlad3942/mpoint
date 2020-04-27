--DCC---
CREATE TABLE system.externalreferencetype_tbl (
	id serial NOT NULL,
	"name" text NOT NULL,
	created timestamp NULL DEFAULT now(),
	modified timestamp NULL DEFAULT now(),
	enabled bool NULL DEFAULT true,
	CONSTRAINT externalreferencetype_pk PRIMARY KEY (id)
);
ALTER TABLE system.externalreferencetype_tbl OWNER TO mpoint;

ALTER TABLE log.externalreference_tbl ADD type int4 CONSTRAINT externalreferencetype_fk REFERENCES system.externalreferencetype_tbl(id);
ALTER TABLE log.transaction_tbl ADD convetredcurrencyid int4 NULL CONSTRAINT convertedcurrency_fk REFERENCES system.currency_tbl(id);
ALTER TABLE log.transaction_tbl ADD convertedamount int8 NULL;
ALTER TABLE log.transaction_tbl ADD conversionrate decimal DEFAULT 1;
ALTER TABLE client.cardaccess_tbl ADD dccenabled bool NULL DEFAULT false;
---DCC---

---DCC--
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(0, 'System');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(50, 'UATP');
INSERT INTO system.externalreferencetype_tbl (id, "name") VALUES(1, 'CellPoint Foreign Exchange');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1980, 'Foreign Exchange  Ack Accepted', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1981, 'Foreign Exchange  Ack Constructed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1983, 'Foreign Exchange  Ack Connection Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1984, 'Foreign Exchange  Ack Transmission Failed', 'Callback', 'send');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(1985, 'Foreign Exchange  Ack Rejected', 'Callback', 'send');
--DCC--

